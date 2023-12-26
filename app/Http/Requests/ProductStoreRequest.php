<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'required|string',
            'result' => 'string',
            'Purpose' => 'string',
            'sizes.*.price' => 'decimal:2',
            'sizes.*.unit' => 'string',
            'sizes.*.value' => 'string',
            'product_category_id' => 'exists:App\Models\ProductCategory,id',
            'product_line_id' => 'exists:App\Models\ProductLines,id',
            'images.*' => "image|mimes:jpeg,png,jpg",
            'avatar' => "image|mimes:jpeg,png,jpg",
        ];
    }
}
