<?php

namespace App\Http\Requests\Crop;

use Illuminate\Foundation\Http\FormRequest;

class HarvestCropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "actual_harvest_date" => "required|date",
            "actual_yield" => "required|numeric|min:0",
            "yield_unit" => "nullable|string|max:50",
            "notes" => "nullable|string",
        ];
    }
}
