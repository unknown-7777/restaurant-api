<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
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

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful.');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'customer',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Registration successful.', 201);
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
