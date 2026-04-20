<?php

namespace App\Http\Requests\LivestockBreeding;

use Illuminate\Foundation\Http\FormRequest;

class StoreBreedingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "farm_id" => "required|exists:farms,id",
            "male_id" => "required|exists:livestocks,id",
            "female_id" => "required|exists:livestocks,id|different:male_id",
            "breeding_date" => "required|date",
            "expected_birth_date" => "nullable|date|after:breeding_date",
            "observations" => "nullable|string",
        ];
    }
}
