<?php

namespace App\Http\Requests\Crop;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "sometimes|string|max:255",
            "type" => "sometimes|string|max:100",
            "description" => "nullable|string",
            "variety" => "nullable|string|max:100",
            "planting_date" => "sometimes|date",
            "expected_harvest_date" => "nullable|date|after:planting_date",
            "actual_harvest_date" => "nullable|date",
            "estimated_yield" => "nullable|numeric|min:0",
            "actual_yield" => "nullable|numeric|min:0",
            "yield_unit" => "nullable|string|max:50",
            "status" => "nullable|in:planning,growing,harvested",
        ];
    }
}
