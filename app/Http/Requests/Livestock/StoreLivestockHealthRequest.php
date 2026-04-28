<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestockHealthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "health_status" => "required|in:healthy,sick,treatment,quarantine,injured,recovering",
            "observations" => "nullable|string",
            "treatment" => "nullable|string",
            "disease_name" => "nullable|string|max:100",
            "disease_start_date" => "nullable|date",
            "recovery_date" => "nullable|date",
            "temperature" => "nullable|numeric",
            "weight" => "nullable|numeric|min:0",
            "weight_unit" => "nullable|string|max:50",
            "notes" => "nullable|string",
            "veterinarian" => "nullable|string|max:100",
        ];
    }
}
