<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class EditProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|array|max:4',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1024',
            'color' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ];
    }
}
