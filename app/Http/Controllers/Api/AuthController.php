<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    use ApiResponseTrait;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try{
            $user = $this->authService->register($request->validated());

            return $this->successResponse(
                [
                    'user_id' => $user->id,
                ],
                'Registration successful. Please verify your OTP sent to your mobile number.',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try{
            $this->authService->verifyOtp($request->validated());

            return $this->successResponse(
                null,
                'OTP verification successful. You can now log in.',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try{
        $data = $this->authService->login($request->validated());

        return $this->successResponse(
            $data,
            'Login successful.',
            200
        );
    } catch (Exception $e) {
        return $this->errorResponse($e->getMessage(), $e->getCode() ?: 401);
    }
    }
}
