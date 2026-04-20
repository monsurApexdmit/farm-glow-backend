<?php

namespace App\Http\Requests\LivestockBreeding;

use Illuminate\Foundation\Http\FormRequest;

class RecordBirthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "actual_birth_date" => "required|date",
            "offspring_count" => "required|integer|min:1",
            "observations" => "nullable|string",
        ];
    }
}
