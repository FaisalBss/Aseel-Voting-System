<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Exception;
trait ApiResponseTrait
{

    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400, $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }


    public function handleException(Exception $e, int $defaultStatusCode = 500): JsonResponse
    {
        $statusCode = $e->getCode() ?: $defaultStatusCode;
        return $this->errorResponse($e->getMessage(), $statusCode);
    }
}
