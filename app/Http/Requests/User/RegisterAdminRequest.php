<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|regex:/^(0)([1-9][0-9]{1})[0-9]{7}$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:32|confirmed',
        ];
    }
}
