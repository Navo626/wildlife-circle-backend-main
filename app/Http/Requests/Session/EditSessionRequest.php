<?php

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;

class EditSessionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'link' => 'required|string|url',
            'host' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
    }
}
