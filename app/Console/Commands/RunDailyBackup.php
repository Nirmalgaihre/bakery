<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;
use App\Models\Backup;

class RunDailyBackup extends Command
{
    protected $signature = 'backup:daily';
    protected $description = 'Run the automated daily backup';

    public function handle(BackupService $backupService)
    {
        try {
            $result = $backupService->run('all', 'auto');

            Backup::create([
                'filename'   => $result['filename'],
                'scope'      => 'Full Backup',
                'size'       => $result['size'],
                'path'       => $result['path'],
                'created_by' => 'System (Scheduled)',
            ]);

            $this->info('Daily backup completed: ' . $result['filename']);
        } catch (\Exception $e) {
            $this->error('Daily backup failed: ' . $e->getMessage());
            \Log::error('Scheduled backup failed: ' . $e->getMessage());
        }
    }
}