<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "theme" => "sometimes|in:light,dark",
            "language" => "sometimes|string|max:5",
            "timezone" => "sometimes|timezone",
            "notifications_enabled" => "sometimes|boolean",
            "email_notifications" => "sometimes|boolean",
        ];
    }
}
