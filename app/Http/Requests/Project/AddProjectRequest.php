<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class AddProjectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|array|max:3',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ];
    }
}
