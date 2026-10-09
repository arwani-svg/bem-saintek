<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLog extends Command
{
    protected $signature = 'activity-log:prune {--days=90 : Hapus log yang lebih tua dari N hari}';

    protected $description = 'Hapus activity log lama agar tabel tetap ringan';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Berhasil menghapus {$deleted} log lebih tua dari {$days} hari.");

        return self::SUCCESS;
    }
}
