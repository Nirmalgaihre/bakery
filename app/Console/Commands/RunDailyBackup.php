<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;
use App\Models\Backup;
use Carbon\Carbon;

class RunDailyBackup extends Command
{
    protected $signature = 'backup:daily';
    protected $description = 'Run automated daily backup at 8:00 PM';

    public function handle(BackupService $backupService)
    {
        try {
            $this->info('Starting daily backup at ' . Carbon::now()->format('Y-m-d H:i:s'));
            
            $result = $backupService->run('all', 'auto');

            Backup::create([
                'filename'   => $result['filename'],
                'scope'      => 'Full Backup',
                'size'       => $result['size'],
                'path'       => $result['path'],
                'created_by' => 'System (Scheduled - 8PM)',
            ]);

            $this->info('✅ Daily backup completed successfully: ' . $result['filename']);
            $this->info('📦 Size: ' . $result['size']);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Daily backup failed: ' . $e->getMessage());
            \Log::error('Scheduled backup failed: ' . $e->getMessage());
            return 1;
        }
    }
}