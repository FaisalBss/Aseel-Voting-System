<?php

namespace App\Console\Commands;

use App\Models\PollOption;
use Illuminate\Console\Command;

class updatePollVoteCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poll:recalculate-votes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate vote counts for all poll options based on actual user votes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $voteCounts = PollOption::withCount('votes')->pluck('votes_count', 'id');

        $pollOptions = PollOption::all();

        if ($pollOptions->isEmpty()) {
            return 0;
        }

        foreach ($pollOptions as $option) {
            $actualCount = $voteCounts->get($option->id, 0);

            $option->vote_count = $actualCount;
            $option->save();
        }

        $this->info('Updated successfully');

        return 0;
    }
}
