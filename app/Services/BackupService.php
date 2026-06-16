<?php

namespace App\Services;

use ZipArchive;

class BackupService
{
    public function run($scope, $prefix = null)
{
    $dir = storage_path('app/backups');
    if (!file_exists($dir)) mkdir($dir, 0755, true);

    $timestamp = date('Ymd_His');
    $sqlFile = $dir . "/db_dump_{$timestamp}.sql";
    $zipFilename = "dc_pkg_" . ($prefix ?: 'manual') . "_" . $timestamp . ".zip";
    $zipPath = $dir . '/' . $zipFilename;

    // सिधै हार्डकोडेड कन्फिगरेसन प्रयोग गर्नुहोस् (तपाईंको DB विवरण अनुसार)
    $db_host = '127.0.0.1'; 
    $db_user = 'root';
    $db_name = 'bakery';
    // पासवर्ड छैन भने यो लाइनलाई यत्तिकै छाड्नुहोस्
    $db_pass = ''; 

    $mysqldumpPath = '/opt/lampp/bin/mysqldump';
    
    // कमाण्ड बनाउँदा पासवर्ड छ कि छैन जाँच गर्ने
    $passArg = !empty($db_pass) ? '-p' . escapeshellarg($db_pass) : '';
    
    $command = sprintf(
        '%s -h %s -u %s %s %s > %s 2>&1',
        $mysqldumpPath,
        escapeshellarg($db_host),
        escapeshellarg($db_user),
        $passArg,
        escapeshellarg($db_name),
        escapeshellarg($sqlFile)
    );
    
    exec($command, $output, $returnCode);

    if ($returnCode !== 0) {
        // एरर म्यासेजलाई अझ प्रष्ट हेर्न
        throw new \Exception("mysqldump failed (Code $returnCode). Output: " . implode(" ", $output));
    }

    // ... बाँकी जिप बनाउने कोड उस्तै रहन्छ
    $zip = new \ZipArchive();
    if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
        $zip->addFile($sqlFile, 'database_dump.sql');
        $zip->close();
    }
    if (file_exists($sqlFile)) unlink($sqlFile);

    return [
        'filename' => $zipFilename,
        'size' => file_exists($zipPath) ? $this->formatBytes(filesize($zipPath)) : '0 KB'
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