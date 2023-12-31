<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products.*.id' => 'exists:App\Models\Product,id|required',
            'products.*.quantity' => 'integer|required',
            'products.*.size_id' => 'exists:App\Models\ProductSize,id|required',
            'first_name' => 'string|required',
            'email' => 'string|required',
            'phone' => 'string|required',
            'city' => 'string|required',
            'address' => 'string|required',
            'delivery_type' => 'integer|required',
        ];
    }
}
