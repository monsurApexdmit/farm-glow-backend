<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "sometimes|email|unique:users,email," . $this->route("user")->id,
            "first_name" => "sometimes|string|max:255",
            "last_name" => "sometimes|string|max:255",
            "phone" => "sometimes|nullable|string|max:20",
            "roles" => "sometimes|array",
            "roles.*" => "string|exists:roles,name",
        ];
    }
}
