<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LogoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Attempt to logout the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function logout(): JsonResponse
    {
        event(new Logout('web', Auth::user()));
        Auth::logout();
        $this->session()->invalidate();
        $this->session()->regenerateToken();

        return response()->json(['message' => 'Logout successful'], 200);
    }
}
