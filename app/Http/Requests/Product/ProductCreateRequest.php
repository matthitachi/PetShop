<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

final class ProductCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_uuid' => 'bail|required|exists:categories,uuid',
            'title' => 'required|string|unique:products,title',
            'price' => 'required|numeric',
            'description' => 'required',
            'metadata' => 'required',
        ];
    }
}
