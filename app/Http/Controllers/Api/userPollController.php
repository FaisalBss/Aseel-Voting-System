<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PollResource;
use App\Services\PollService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class userPollController extends Controller
{
    use ApiResponseTrait;

    protected $pollService;

    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function index(): JsonResponse
    {
        try {
            $polls = $this->pollService->getActivePolls();

            return PollResource::collection($polls)->response();

        } catch (Exception $e) {
            return $this->handleException($e, 500);
        }
    }

    public function vote($poll, $voteRequest): JsonResponse
    {
        try {
            $vote = $this->pollService->castVote($poll, $voteRequest);

            return $this->successResponse($vote, 'Vote cast successfully.', 200);

        } catch (Exception $e) {
            return $this->handleException($e, 500);
        }
    }
}
