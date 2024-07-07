<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

final class BrandUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
        ];
    }
}
