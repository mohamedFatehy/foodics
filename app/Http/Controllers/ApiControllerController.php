<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ApiControllerController extends Controller
{
    public static function SuccessResponse(string $message, int $statusCode = ResponseStatus::HTTP_OK, array $data = []): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $statusCode);
    }

    public static function FailureResponse(string $message, int $statusCode = ResponseStatus::HTTP_INTERNAL_SERVER_ERROR, array $data = []): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'data' => $data], $statusCode);
    }
}
