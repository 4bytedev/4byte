<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        return $request->authenticate();
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        return $request->logout();
    }

    public function register(RegisterRequest $request)
    {
        return $request->register();
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $request->forgotPassword();
    }

    public function viewResetPassword(Request $request)
    {
        return Inertia::render('Auth/ResetPassword', $request->only(['email', 'token']));
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $request->resetPassword();
    }
}
