<?php

namespace App\Modules\Core\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Success response with data
     */
    protected function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = ['success' => true];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response
     */
    protected function error(string $message, $details = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'timestamp' => now()->toISOString(),
            ]
        ];

        if ($details) {
            $response['error']['details'] = $details;
        }

        return response()->json($response, $code);
    }

    /**
     * Not found response
     */
    protected function notFound(string $message = 'غير موجود'): JsonResponse
    {
        return $this->error($message, null, 404);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorized(string $message = 'غير مصرح'): JsonResponse
    {
        return $this->error($message, null, 401);
    }

    /**
     * Forbidden response
     */
    protected function forbidden(string $message = 'ممنوع الوصول'): JsonResponse
    {
        return $this->error($message, null, 403);
    }

    /**
     * Validation error response
     */
    protected function validationError($validator): JsonResponse
    {
        return $this->error(
            'خطأ في التحقق من صحة البيانات',
            ['validation_errors' => $validator->errors()],
            422
        );
    }
}
