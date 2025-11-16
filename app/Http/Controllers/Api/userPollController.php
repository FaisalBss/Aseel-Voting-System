<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PollResource;
use App\Services\PollService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\Api\VoteRequest;
use App\Models\Poll;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class userPollController extends Controller
{
    use ApiResponseTrait;

    protected $pollService;

    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    public function vote(VoteRequest $request, Poll $poll): JsonResponse
    {
        try {
            $vote = $this->pollService->submitVote($poll, $request->validated(), $request->user());
            $messege = $vote-> wasRecentlyCreated ? 'Vote submitted successfully.' : 'Vote updated successfully.';
            $statusCode = $vote-> wasRecentlyCreated ? 201 : 200;
            return $this->successResponse(null, $messege , $statusCode);
        } catch (Exception $e) {
            return $this->handleException($e, 400);
        }
    }

    public function getActivePolls(): JsonResponse
    {
        try {
            $polls = $this->pollService->getAllPolls();
            return PollResource::collection($polls)->response();
        } catch (Exception $e) {
            return $this->handleException($e, 500);
        }
    }

    public function showResult(Request $request, Poll $poll): JsonResponse
    {
        try {
            $result = $this->pollService->getPollResult($poll, $request->user());
            return $this->successResponse($result, 'Poll results retrieved successfully.', 200);
        } catch (Exception $e) {
            return $this->handleException($e, 400);
        }
    }

}
