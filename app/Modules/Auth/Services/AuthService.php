<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Core\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService extends BaseService
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * User login
     */
    public function login(array $credentials): array
    {
        $user = $this->model->where('username', $credentials['username'])
                           ->orWhere('email', $credentials['username'])
                           ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('اسم المستخدم أو كلمة المرور غير صحيحة');
        }

        if (!$user->is_active) {
            throw new \Exception('الحساب غير مفعل');
        }

        // Delete old tokens
        $user->tokens()->delete();

        // Create new tokens
        $accessToken = $user->createToken('access_token', ['*'], now()->addHours(1))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(30))->plainTextToken;

        // Update last login
        $user->update(['last_login_at' => now()]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => $this->formatUserData($user)
        ];
    }

    /**
     * User logout
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Reset password
     */
    public function resetPassword(User $user, array $data): array
    {
        if (!Hash::check($data['old_password'], $user->password)) {
            throw new \Exception('كلمة المرور الحالية غير صحيحة');
        }

        // Handle both confirm_password and new_password_confirmation
        $confirmPassword = $data['new_password_confirmation'] ?? $data['confirm_password'] ?? null;
        
        if ($data['new_password'] !== $confirmPassword) {
            throw new \Exception('كلمة المرور الجديدة غير متطابقة');
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
            'password_changed_at' => now()
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        return [
            'password_changed_at' => $user->password_changed_at->toISOString()
        ];
    }

    /**
     * Refresh token
     */
    public function refreshToken(string $refreshToken): array
    {
        $token = PersonalAccessToken::findToken($refreshToken);

        if (!$token || !$token->can('refresh') || $token->expires_at < now()) {
            throw new \Exception('رمز التحديث غير صالح أو منتهي الصلاحية');
        }

        $user = $token->tokenable;

        // Delete old access tokens (keep refresh token)
        $user->tokens()->where('name', 'access_token')->delete();

        // Create new access token
        $accessToken = $user->createToken('access_token', ['*'], now()->addHours(1))->plainTextToken;

        return [
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => $this->formatUserData($user)
        ];
    }

    /**
     * Get user profile
     */
    public function getUserProfile(User $user): array
    {
        return $this->formatUserData($user);
    }

    /**
     * Format user data for API response
     */
    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'profile_image' => $user->profile_image ? url('storage/' . $user->profile_image) : null,
            'created_at' => $user->created_at->toISOString(),
            'last_login_at' => $user->last_login_at?->toISOString(),
        ];
    }
}
