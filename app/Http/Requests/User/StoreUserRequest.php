<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8|confirmed",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "phone" => "nullable|string|max:20",
            "roles" => "nullable|array",
            "roles.*" => "string|exists:roles,name",
        ];
    }
}
