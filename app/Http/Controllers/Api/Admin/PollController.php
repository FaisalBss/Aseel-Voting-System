<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StorePollRequest;
use App\Services\PollService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

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

            return $this->successResponse($poll, 'Poll created successfully.', 201);

        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
