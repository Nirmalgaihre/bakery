<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backup;
use App\Services\BackupService;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $backups = Backup::latest()->get();
        
        // Get disk space information
        $storagePath = storage_path();
        $freeSpace = @disk_free_space($storagePath) ?: 50 * 1024 * 1024 * 1024;
        $totalSpace = @disk_total_space($storagePath) ?: 100 * 1024 * 1024 * 1024;
        $usedSpace = $totalSpace - $freeSpace;
        
        $diskDetails = [
            'free' => $this->formatBytes($freeSpace),
            'used' => $this->formatBytes($usedSpace),
            'percentage' => round(($usedSpace / $totalSpace) * 100, 1),
            'last_backup' => Backup::latest()->first()
        ];

        return view('admin.backups.index', compact('backups', 'diskDetails'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'backup_scope' => 'required|in:all,db_only',
            'prefix' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_-]+$/'
        ]);

        $scope = $request->input('backup_scope', 'all');
        $prefix = $request->input('prefix') ?? 'manual';

        try {
            $result = $this->backupService->run($scope, $prefix);

            Backup::create([
                'filename' => $result['filename'],
                'scope' => $scope === 'all' ? 'Full Backup' : 'DB Only',
                'size' => $result['size'],
                'path' => $result['path'],
                'created_by' => auth()->user()->name ?? 'System'
            ]);

            return redirect()->route('admin.backups.index')
                ->with('success', 'System backup archive created successfully!');
        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Process failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filename = basename($filename); // Prevent path traversal
        $filePath = storage_path('app/backups/' . $filename);
        
        if (file_exists($filePath)) {
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/zip',
            ]);
        }
        
        return redirect()->route('admin.backups.index')
            ->with('error', 'Archive file not found on storage node.');
    }

    public function destroy($filename)
    {
        $filename = basename($filename);
        $backup = Backup::where('filename', $filename)->firstOrFail();
        $filePath = storage_path('app/backups/' . $filename);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $backup->delete();
        
        return redirect()->route('admin.backups.index')
            ->with('success', 'Archive package successfully deleted.');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip,sql|max:102400', // Max 100MB
        ]);

        try {
            $file = $request->file('backup_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(storage_path('app/temp_imports'), $filename);
            
            $importPath = storage_path('app/temp_imports/' . $filename);

            // Store import job
            $importJob = \App\Models\BackupImport::create([
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $importPath,
                'status' => 'pending',
                'uploaded_by' => auth()->user()->name,
            ]);

            return redirect()->route('admin.backups.index')
                ->with('success', "Backup file uploaded successfully! Ready to restore: {$filename}");
            
        } catch (Throwable $e) {
            \Log::error('Import failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore from backup file
     */
    public function restore($filename)
    {
        try {
            $filename = basename($filename);
            $filePath = storage_path('app/temp_imports/' . $filename);
            $importJob = \App\Models\BackupImport::where('filename', $filename)->firstOrFail();

            if (!file_exists($filePath)) {
                return redirect()->back()
                    ->with('error', 'Import file not found on storage.');
            }

            // If it's a SQL file, import directly
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'sql') {
                $this->importSqlFile($filePath);
            } 
            // If it's a ZIP file, extract and import
            elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'zip') {
                $this->importZipFile($filePath);
            }

            // Mark import as successful
            $importJob->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Cleanup temp file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return redirect()->route('admin.backups.index')
                ->with('success', 'Database restored successfully from backup!');
            
        } catch (Throwable $e) {
            \Log::error('Restore failed: ' . $e->getMessage());
            
            $importJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Import SQL file
     */
    private function importSqlFile($filePath)
    {
        $dbConfig = config('database.connections.' . config('database.default'));
        
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['port']),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($filePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('SQL import failed. Check syntax and permissions.');
        }
    }

    /**
     * Import ZIP file (extract and find SQL)
     */
    private function importZipFile($filePath)
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($filePath) !== true) {
            throw new \Exception('Failed to open ZIP archive.');
        }

        $extractPath = storage_path('app/temp_extract_' . uniqid());
        mkdir($extractPath, 0755, true);

        $zip->extractTo($extractPath);
        $zip->close();

        // Find and import database.sql
        $sqlFile = $extractPath . '/database.sql';
        
        if (file_exists($sqlFile)) {
            $this->importSqlFile($sqlFile);
        } else {
            throw new \Exception('No database.sql found in backup archive.');
        }

        // Cleanup
        $this->cleanupDirectory($extractPath);
    }

    private function cleanupDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            
            rmdir($dir);
        }
    }
}