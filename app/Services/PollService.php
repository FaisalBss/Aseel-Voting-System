<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PollService
{

    public function createPoll(array $data): Poll
    {
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
}
