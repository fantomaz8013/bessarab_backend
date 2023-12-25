<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'title' => 'string',
            'description' => 'string',
            'result' => 'string',
            'Purpose' => 'string',
            'price' => 'integer',
            'sizes.*.unit' => 'string',
            'sizes.*.value' => 'string',
            'product_category_id' => 'exists:App\Models\ProductCategory,id',
            'images.*' => "image|mimes:jpeg,png,jpg",
            'avatar' => "image|mimes:jpeg,png,jpg",
            'delete_images.*' => 'exists:App\Models\ProductImage,id',
            'delete_sizes.*' => 'exists:App\Models\ProductSize,id',
        ];
    }
}
