<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    protected SeoService $seoService;

    public function __construct()
    {
        $this->seoService       = app(SeoService::class);
    }

    /**
     * Authenticate a user using login credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $request->authenticate();
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        return $request->logout();
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $request->register();
    }

    /**
     * Send a password reset link to the user's email.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        return $request->forgotPassword();
    }

    /**
     * Display the reset password page.
     */
    public function viewResetPassword(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', $request->only(['email', 'token']))
            ->withViewData(['seo' => $this->seoService->getResetPasswordSEO()]);
    }

    /**
     * Reset the user's password using a valid token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $request->resetPassword();
    }
}
