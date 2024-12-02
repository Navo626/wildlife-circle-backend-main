<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class AddMemberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'honorary_title' => 'required|string',
            'name' => 'required|string',
            'position' => 'required|string',
            'category' => 'required|string|in:Executive Team,Advisor,Past President',
            'image' => 'required',
            'email' => 'nullable|email',
            'social_facebook' => 'nullable|string',
            'social_researchgate' => 'nullable|string',
            'social_scholar' => 'nullable|string',
        ];
    }
}
