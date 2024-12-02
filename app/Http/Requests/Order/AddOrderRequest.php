<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|string|max:255',
            'product_id' => 'required|numeric',
            'size' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:1',
            'amount' => 'required|numeric|min:0',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }
}
