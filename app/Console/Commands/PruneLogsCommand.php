<?php

namespace App\Console\Commands;

use App\Models\LogEntry;
use App\Models\Project;
use Illuminate\Console\Command;

class PruneLogsCommand extends Command
{
    protected $signature = 'vigil:prune-logs';

    protected $description = 'Delete log entries older than their retention period';

    public function handle(): int
    {
        $globalRetention = config('vigil.log_retention_days', 30);

        Project::whereNotNull('log_retention_days')->each(function (Project $project) {
            $cutoff = now()->subDays($project->log_retention_days);
            $deleted = 0;

            do {
                $batch = $project->logEntries()
                    ->where('logged_at', '<', $cutoff)
                    ->limit(5000)
                    ->delete();

                $deleted += $batch;
            } while ($batch === 5000);

            if ($deleted > 0) {
                $this->line("Pruned {$deleted} logs from {$project->name} ({$project->log_retention_days}d retention)");
            }
        });

        $cutoff = now()->subDays($globalRetention);
        $deleted = 0;

        do {
            $batch = LogEntry::whereHas('project', fn ($q) => $q->whereNull('log_retention_days'))
                ->where('logged_at', '<', $cutoff)
                ->limit(5000)
                ->delete();

            $deleted += $batch;
        } while ($batch === 5000);

        if ($deleted > 0) {
            $this->line("Pruned {$deleted} logs using global {$globalRetention}d retention");
        }

        $this->info('Log pruning complete.');

        return self::SUCCESS;
    }
}
