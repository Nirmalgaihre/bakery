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
        
        // लोकल डिस्क स्पेस क्यालकुलेशन गर्ने लोजिक
        $freeSpace = @disk_free_space(storage_path()) ?: 1024 * 1024 * 1024 * 50;
        $totalSpace = @disk_total_space(storage_path()) ?: 1024 * 1024 * 1024 * 100;
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
        $scope = $request->input('backup_scope', 'all');
        $prefix = $request->input('prefix', 'manual');

        try {
            $result = $this->backupService->run($scope, $prefix);

            Backup::create([
                'filename' => $result['filename'],
                'scope' => $scope === 'all' ? 'Full Backup' : ($scope === 'database' ? 'DB Only' : 'Files Only'),
                'size' => $result['size'],
                'path' => $result['path'],
                'created_by' => 'Nirmal Gaihre' // निर्मल गैह्रे
            ]);

            return redirect()->route('admin.backups.index')->with('success', 'System backup archive created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Process failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        return redirect()->back()->with('error', 'Requested archive file not found on local storage node.');
    }

    public function destroy($filename)
    {
        $backup = Backup::where('filename', $filename)->firstOrFail();
        $filePath = storage_path('app/backups/' . $filename);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $backup->delete();
        return redirect()->route('admin.backups.index')->with('success', 'Archive package successfully dropped.');
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
}