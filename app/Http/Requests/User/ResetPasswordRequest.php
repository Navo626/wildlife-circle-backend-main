<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'reset_token' => 'required',
            'email' => 'required|email|exists:password_reset_tokens,email',
            'password' => 'required|min:8|max:32|confirmed',
        ];
    }
}
