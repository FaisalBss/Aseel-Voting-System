<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Poll;
use Carbon\Carbon;

class UpdatePollStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polls:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update poll statuses based on start time & end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Updating poll statuses at $now...");

        $startedCount = Poll::where('status', 'scheduled')
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->update(['status' => 'active']);

        if ($startedCount > 0) {
            $this->info("Activated $startedCount scheduled polls.");
        }

        $closedCount = Poll::whereIn('status', ['scheduled', 'active'])
            ->where('end_time', '<', $now)
            ->update(['status' => 'closed']);

        if ($closedCount > 0) {
            $this->info("Closed $closedCount active polls.");
        }

        $this->info('Poll status update complete.');
        return 0;
    }

}
