<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterCompanyRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registerCompany(RegisterCompanyRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->registerCompany($request->validated());

            return response()->json([
                'message' => 'Company registered successfully',
                'user' => $result['user'],
                'company' => $result['company'],
                'token' => $result['token'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->email,
                $request->password
            );

            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $result['user'],
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'user' => $user->load('company', 'roles'),
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refreshToken(): JsonResponse
    {
        try {
            $token = $this->authService->refreshToken();

            return response()->json([
                'token' => $token,
                'message' => 'Token refreshed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to refresh token'], 401);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            $this->authService->changePassword(
                $user,
                $request->current_password,
                $request->new_password
            );

            return response()->json(['message' => 'Password changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
