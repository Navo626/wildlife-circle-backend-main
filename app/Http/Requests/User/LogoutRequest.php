<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'logout_all' => 'required|boolean',
        ];
    }
}
