<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StorePollRequest;
use App\Http\Resources\PollResource;
use App\Services\PollService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Admin\UpdatePollRequest;
use App\Models\Poll;

class PollController extends Controller
{
    use ApiResponseTrait;

    protected $pollService;

    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
    }


    public function store(StorePollRequest $request): JsonResponse
    {
        try {
            $poll = $this->pollService->createPoll($request->validated());

            return $this->successResponse(new PollResource($poll), 'Poll created successfully.', 201);

        } catch (Exception $e) {
            return $this->handleException($e, 500);
        }
    }

    public function update(UpdatePollRequest $request, $poll): JsonResponse
    {
        try {
            $updatePoll = $this->pollService->updatePoll($poll, $request->validated());

            return $this->successResponse(new PollResource($updatePoll), 'Poll updated successfully.', 200);
        } catch (Exception $e) {
            return $this->handleException($e, 403);
        }
    }

    public function destroy(Poll $poll): JsonResponse
    {
        try {
            $deleted = $this->pollService->deletePoll($poll);
            if ($deleted) {
                return $this->successResponse(null, 'Poll deleted successfully.');
            } else {
                return $this->errorResponse('Poll could not be deleted.', 500);
            }
        } catch (Exception $e) {
            return $this->handleException($e, 403);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $polls = $this->pollService->getAllPolls();
            return PollResource::collection($polls)->response();
        } catch (Exception $e) {
            return $this->handleException($e, 500);
        }
    }
}
