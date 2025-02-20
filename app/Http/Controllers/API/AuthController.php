<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\{
    RegisterRequest,
    LoginRequest,
    PasswordResetRequest,
    PasswordResetConfirmRequest
};
use App\Services\AuthService;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Throwable;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $token = $this->authService->register($request->validated());
            return response()->json(['token' => $token], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed to create user due to database error',
                'error' => $e->getMessage(),
            ], 500);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to generate token',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Registration failed unexpectedly',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $token = $this->authService->login($request->validated());
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                return response()->json(['message' => $e->getMessage()], 401);
            }
            return response()->json([
                'message' => 'Login failed unexpectedly',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        try {
            $this->authService->sendResetLink($request->validated()['email']);
            return response()->json(['message' => 'Password reset link sent'], 200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to process reset request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reset(PasswordResetConfirmRequest $request)
    {
        try {
            $this->authService->resetPassword($request->validated());
            return response()->json(['message' => 'Password has been reset'], 200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Password reset failed unexpectedly',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
