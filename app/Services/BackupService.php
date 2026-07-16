<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BackupService
{
    public function run($scope = 'all', $prefix = 'auto')
    {
        $timestamp = Carbon::now()->format('Ymd_His');
        $filename = sprintf('dc_pkg_%s_%s_%s.zip', $prefix, $scope, $timestamp);
        $backupPath = storage_path('app/backups');
        
        // Ensure backup directory exists
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $tempDir = storage_path('app/temp_backup_' . Str::random(10));
        mkdir($tempDir, 0755, true);

        try {
            // Export database
            if ($scope === 'all' || $scope === 'db_only') {
                $this->exportDatabase($tempDir);
            }

            // Export files if full backup
            if ($scope === 'all') {
                $this->exportFiles($tempDir);
            }

            // Create ZIP archive
            $zipPath = $backupPath . '/' . $filename;
            $this->createZip($tempDir, $zipPath);

            // Cleanup temp directory
            $this->cleanup($tempDir);

            return [
                'filename' => $filename,
                'path' => $zipPath,
                'size' => $this->formatBytes(filesize($zipPath)),
            ];

        } catch (\Exception $e) {
            // Cleanup on failure
            $this->cleanup($tempDir);
            throw $e;
        }
    }

    private function exportDatabase($tempDir)
    {
        $dbConfig = config('database.connections.' . config('database.default'));
        $sqlFile = $tempDir . '/database.sql';

        // Get all table names
        $tables = DB::select('SHOW TABLES');
        $tableColumn = 'Tables_in_' . $dbConfig['database'];
        
        $sqlContent = "-- Database Backup\n";
        $sqlContent .= "-- Generated: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
        $sqlContent .= "-- Database: " . $dbConfig['database'] . "\n\n";
        
        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            
            // Skip Laravel internal tables if needed
            if (in_array($tableName, ['migrations', 'cache', 'sessions', 'jobs', 'failed_jobs'])) {
                continue;
            }
            
            // Get table structure
            $structure = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sqlContent .= "-- Table: {$tableName}\n";
            $sqlContent .= $structure[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                foreach ($rows as $row) {
                    $columns = array_keys((array)$row);
                    $values = array_map(function($value) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, (array)$row);
                    
                    $sqlContent .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlContent .= "\n";
            }
        }

        file_put_contents($sqlFile, $sqlContent);
    }

    private function exportFiles($tempDir)
    {
        $filesDir = $tempDir . '/files';
        mkdir($filesDir, 0755, true);

        // Copy important directories (adjust paths as needed)
        $directoriesToBackup = [
            storage_path('app/uploads') => 'uploads',
            storage_path('app/documents') => 'documents',
        ];

        foreach ($directoriesToBackup as $source => $dest) {
            if (is_dir($source)) {
                $this->copyDirectory($source, $filesDir . '/' . $dest);
            }
        }
    }

    private function createZip($sourceDir, $zipPath)
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Failed to create ZIP archive.');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    private function copyDirectory($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        $files = scandir($source);
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $sourcePath = $source . '/' . $file;
                $destPath = $dest . '/' . $file;

                if (is_dir($sourcePath)) {
                    $this->copyDirectory($sourcePath, $destPath);
                } else {
                    copy($sourcePath, $destPath);
                }
            }
        }
    }

    private function cleanup($dir)
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