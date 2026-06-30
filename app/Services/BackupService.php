<?php

namespace App\Services;

use ZipArchive;
use Spatie\DbDumper\Databases\MySql;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackupService
{
    /**
     * Run the backup process.
     *
     * @param string $scope  'all', 'db_only', or 'files_only'
     * @param string $prefix
     * @return array
     * @throws \Exception
     */
    public function run(string $scope = 'all', string $prefix = 'manual'): array
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Ymd-His');
        $baseFilename = sprintf(
            'dc_pkg_%s_%s_%s',
            Str::slug($prefix),
            $scope,
            $timestamp
        );
        $dumpBinaryPath = '/opt/lampp/bin/';
        if ($scope === 'all' || $scope === 'db_only') {
            $dbFilename = $baseFilename . '.sql';
            $dbPath = $backupDir . '/' . $dbFilename;

            MySql::create()
                ->setDbName(config('database.connections.mysql.database'))
                ->setUserName(config('database.connections.mysql.username'))
                ->setPassword(config('database.connections.mysql.password'))
                ->setHost(config('database.connections.mysql.host'))
                ->setPort(config('database.connections.mysql.port'))
                ->setDumpBinaryPath($dumpBinaryPath)
                ->dumpToFile($dbPath);
        }

        $finalPath = null;
        $finalFilename = null;

        if ($scope === 'all' || $scope === 'files_only') {
            $zipFilename = $baseFilename . '.zip';
            $zipPath = $backupDir . '/' . $zipFilename;
            $zip = new ZipArchive();

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception("Cannot create zip archive at {$zipPath}");
            }

            // Add DB dump to zip
            if ($scope === 'all' && isset($dbPath) && File::exists($dbPath)) {
                $zip->addFile($dbPath, basename($dbPath));
            }

            // Add other important files/directories to zip.
            // Example: public/uploads or storage/app/public
            // Using storage/app/public as an example here.
            $filesPath = storage_path('app/public'); // Adjust this path to what you need to back up
            if (File::exists($filesPath)) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filesPath), \RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'files/' . substr($filePath, strlen($filesPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            $zip->close();

            // Clean up the temporary SQL file
            if ($scope === 'all' && isset($dbPath) && File::exists($dbPath)) {
                File::delete($dbPath);
            }

            $finalPath = $zipPath;
            $finalFilename = $zipFilename;
        } elseif ($scope === 'db_only') {
            $finalPath = $dbPath;
            $finalFilename = $dbFilename;
        }

        return [
            'filename' => $finalFilename,
            'size' => $finalPath && File::exists($finalPath) ? $this->formatBytes(File::size($finalPath)) : '0 B',
            'path' => $finalPath,
        ];
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