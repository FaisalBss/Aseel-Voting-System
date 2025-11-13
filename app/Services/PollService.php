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
        if ($poll->start_time->isPast() && $poll->status !== 'draft') {
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
                'status' => $data['status'] ?? 'draft',
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
        $now = Carbon::now();

        return Poll::where('status', 'scheduled')->where('start_time', '<=', $now)
        ->where('end_time', '>', $now)->with('options')->latest()->paginate(10);
    }

    public function submitVote(Poll $poll, array $data, User $user): UserVote
    {
        if (!$user->is_verified) {
            throw new Exception('Only verified users can vote.', 403);
        }

        $now = Carbon::now();
        if (!($poll->status === 'scheduled' && $poll->start_time <= $now && $poll->end_time > $now)) {
            throw new Exception('Poll is not active or has expired.', 400);
        }

        $existingVote = UserVote::where('user_id', $user->id)->where('poll_id', $poll->id)->exists();

        if ($existingVote) {
            throw new Exception('You have already voted in this poll.', 409);
        }

        return DB::transaction(function () use ($poll, $data, $user) {

            $vote = UserVote::create([
                'user_id' => $user->id,
                'poll_id' => $poll->id,
                'poll_option_id' => $data['poll_option_id'],
            ]);
            PollOption::where('id', $data['poll_option_id'])->increment('vote_count');

            return $vote;
        });
    }
}
