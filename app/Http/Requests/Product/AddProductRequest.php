<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => 'required|array|max:3',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1024',
            'color' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ];
    }
}
