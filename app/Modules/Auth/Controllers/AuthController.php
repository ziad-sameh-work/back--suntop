<?php

namespace App\Modules\Auth\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\FlexibleResetPasswordRequest;
use App\Modules\Auth\Requests\RefreshTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $result = $this->authService->login($credentials);

            return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 401);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());
            return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(FlexibleResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->resetPassword($request->user(), $data);

            return $this->successResponse($result, 'تم تغيير كلمة المرور بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->refreshToken($data['refresh_token']);

            return $this->successResponse($result, 'تم تحديث الرمز المميز بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 401);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getUserProfile($request->user());
            return $this->successResponse($user);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
