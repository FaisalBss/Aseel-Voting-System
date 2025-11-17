<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\UserVote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class PollService
{

    private function validatePollModifiable(Poll $poll)
    {
        if ($poll->start_time->isPast() && $poll->status !== 'scheduled' && $poll->status !== 'draft') {
            throw new Exception('Cannot modify a poll that has already started.', 403);
        }
    }
      private function convertDates(array $data): array
    {
        $customFormat = 'Y/m/d/H:i';

        if (!empty($data['start_time'])) {
            $data['start_time'] = Carbon::createFromFormat($customFormat, $data['start_time'])->toDateTimeString();
        }

        if (!empty($data['end_time'])) {
            $data['end_time'] = Carbon::createFromFormat($customFormat, $data['end_time'])->toDateTimeString();
        }

        return $data;
    }

    public function createPoll(array $data): Poll
    {
        $data = $this->convertDates($data);

        return DB::transaction(function () use ($data) {

            $poll = Poll::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'created_by' => Auth::id(),
                'status' => $data['status'] ?? 'draft' ?? 'scheduled',
            ]);

            $uniqueOptions = array_unique($data['options']);
            foreach ($uniqueOptions as $optionText) {
                $poll->options()->create([
                    'option_text' => $optionText,
                ]);
            }

            return $poll->load('options');
        });
    }

    public function updatePoll(Poll $poll, array $data): Poll
    {
        $this->validatePollModifiable($poll);
        $data = $this->convertDates($data);
        $poll->update($data);
        return $poll->fresh();
    }

    public function deletePoll(Poll $poll): bool
    {
        $this->validatePollModifiable($poll);
        return $poll->delete();

    }

    public function getAllPolls() {
        return Poll::where('created_by', Auth::id())->with('options')->latest()->paginate(10);
    }

    public function getActivePolls()
    {
        return Poll::where('status', 'active')->with('options')->latest()->get();
    }

    public function submitVote(Poll $poll, array $data, User $user): UserVote
    {
        if (!$user->is_verified) {
            throw new Exception('Only verified users can vote.', 403);
        }

        if ($poll->status !== 'active' || $poll->end_time->isPast()) {
            throw new Exception('Poll is not active or has expired.', 400);
        }

        $newOptionId = $data['poll_option_id'];

        return DB::transaction(function () use ($poll, $newOptionId, $user) {

            $existingVote = UserVote::where('user_id', $user->id)->where('poll_id', $poll->id)->first();

            if($existingVote){

                if($existingVote->poll_option_id == $newOptionId){
                    throw new Exception('You have already voted for this option.', 409);
                }

                PollOption::where('id', $existingVote->poll_option_id)->decrement('vote_count');
                $existingVote->update(['poll_option_id' => $newOptionId]);
                $vote = $existingVote;

            }else{
               $vote = UserVote::create([
                'user_id' => $user->id,
                'poll_id' => $poll->id,
                'poll_option_id' => $newOptionId,
            ]);
                }
            PollOption::where('id', $newOptionId)->increment('vote_count');

            return $vote;
        });
    }

    public function getAllResult(Poll $poll, User $user) {
        $hasVoted = UserVote::where('user_id', $user->id)->where('poll_id', $poll->id)->exists();

        if($user->role != 1 && !$hasVoted && $poll->status !== 'closed') {
            throw new Exception('Results are not available until you vote or the poll is closed.', 403);
        }

        $options = $poll->options;

        $totalVotes = $options->sum('vote_count');

        $formattedOptions = $options->map(function ($option) use ($totalVotes) {

            $count = $option->vote_count;
            $percentage = ($totalVotes > 0) ? round(($count / $totalVotes) * 100, 2) : 0;

            return [
                'id' => $option->id,
                'option_text' => $option->option_text,
                'vote_count' => $count,
                'percentage' => $percentage
            ];
        });

        return [
            'poll_title' => $poll->title,
            'total_votes' => $totalVotes,
            'options' => $formattedOptions
        ];
    }

    public function getPollResult(Poll $poll, User $user) {

        $hasVoted = UserVote::where('user_id', $user->id)->where('poll_id', $poll->id)->exists();

        if($user->role != 1 && !$hasVoted && $poll->status !== 'closed') {
            throw new Exception('Results are not available until you vote or the poll is closed.', 403);
        }

        $options = $poll->options;
        $totalVotes = $options->sum('vote_count');
        $formattedOptions = $options->map(function ($option) use ($totalVotes) {

            $count = $option->vote_count;

            $percentage = ($totalVotes > 0) ? ($count / $totalVotes) * 100 : 0;
            return [
                'id' => $option->id,
                'text' => $option->option_text,
                'votes' => $count,
                'percentage' => round($percentage, 2)
            ];
        });

        return [
            'poll_title' => $poll->title,
            'total_votes' => $totalVotes,
            'options' => $formattedOptions
        ];
    }
}
