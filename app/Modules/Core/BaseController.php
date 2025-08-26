<?php

namespace App\Modules\Core;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return success response
     */
    protected function successResponse($data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return error response
     */
    protected function errorResponse(string $message, $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'timestamp' => now()->toISOString(),
            ]
        ];

        if ($errors) {
            $response['error']['details'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return validation error response
     */
    protected function validationErrorResponse($validator): JsonResponse
    {
        return $this->errorResponse(
            'خطأ في التحقق من صحة البيانات',
            [
                'validation_errors' => $validator->errors()
            ],
            422
        );
    }

    /**
     * Return paginated response
     */
    protected function paginatedResponse($data, $resource = null): JsonResponse
    {
        if ($resource) {
            $data = $resource::collection($data);
        }

        return $this->successResponse([
            'items' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'total_pages' => $data->lastPage(),
                'has_next' => $data->hasMorePages(),
                'has_prev' => $data->currentPage() > 1,
            ]
        ]);
    }
}
