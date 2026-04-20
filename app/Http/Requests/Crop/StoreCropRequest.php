<?php

namespace App\Http\Requests\Crop;

use Illuminate\Foundation\Http\FormRequest;

class StoreCropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "field_id" => "required|exists:fields,id",
            "name" => "required|string|max:255",
            "type" => "required|string|max:100",
            "description" => "nullable|string",
            "variety" => "nullable|string|max:100",
            "planting_date" => "required|date",
            "expected_harvest_date" => "nullable|date|after:planting_date",
            "estimated_yield" => "nullable|numeric|min:0",
            "yield_unit" => "nullable|string|max:50",
        ];
    }
}
