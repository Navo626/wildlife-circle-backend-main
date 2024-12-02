<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class EditMemberRequest extends FormRequest
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'email' => 'nullable|email',
            'social_facebook' => 'nullable|string',
            'social_researchgate' => 'nullable|string',
            'social_scholar' => 'nullable|string',
        ];
    }
}
