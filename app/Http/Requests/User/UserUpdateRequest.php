<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string|Password>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'avatar' => 'nullable',
            'email' => 'email|required|unique:users',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'is_marketing' => 'boolean|nullable',
        ];
    }

}
