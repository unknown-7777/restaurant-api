<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials.', 401);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return $this->error('Your account has been deactivated.', 403);
        }

        // Revoke all previous tokens
        $user->tokens()->delete();

        // Set expiry based on role
        $expiresAt = match($user->role) {
            'customer' => now()->addHours(24),
            'kitchen'  => now()->addHours(12),
            default    => now()->addHours(8),
        };

        $deviceName = $request->header('User-Agent', 'unknown-device');
        $tokenName  = $user->role . '-token|' . substr($deviceName, 0, 40);

        $token = $user->createToken(
            $tokenName,
            ['*'],
            $expiresAt
        )->plainTextToken;

        return $this->success([
            'user'       => new UserResource($user),
            'token'      => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
        ], 'Login successful.');
    }

    public function register(): JsonResponse
    {
        return $this->error(
            'Customer accounts are not required. Scan the QR code at your table to browse the menu and order.',
            403
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully.');
    }
}
