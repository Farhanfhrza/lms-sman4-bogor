<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLogs extends Command
{
    protected $signature = 'lms:prune-logs {--months=6 : Number of months to keep}';

    protected $description = 'Delete activity logs older than the specified number of months to keep storage lean.';

    public function handle(): int
    {
        $months = (int) $this->option('months');
        $cutoff = now()->subMonths($months);

        $count = ActivityLog::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info('No old logs to prune.');
            return self::SUCCESS;
        }

        $this->info("Found {$count} log(s) older than {$months} months.");

        // Delete in chunks to avoid memory overflow on large datasets
        $deleted = 0;
        ActivityLog::where('created_at', '<', $cutoff)
            ->chunkById(1000, function ($logs) use (&$deleted) {
                $ids = $logs->pluck('id');
                ActivityLog::whereIn('id', $ids)->delete();
                $deleted += $ids->count();
            });

        $this->info("Successfully pruned {$deleted} activity log(s).");

        return self::SUCCESS;
    }
}
