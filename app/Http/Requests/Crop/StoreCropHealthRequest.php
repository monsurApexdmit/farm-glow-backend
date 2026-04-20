<?php

namespace App\Http\Requests\Crop;

use Illuminate\Foundation\Http\FormRequest;

class StoreCropHealthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "health_status" => "required|in:healthy,at_risk,diseased,infested",
            "disease_count" => "nullable|integer|min:0",
            "disease_notes" => "nullable|string",
            "pest_count" => "nullable|integer|min:0",
            "pest_notes" => "nullable|string",
            "weed_count" => "nullable|integer|min:0",
            "weed_notes" => "nullable|string",
            "moisture_level" => "nullable|numeric|min:0|max:100",
            "nitrogen_level" => "nullable|integer|min:0|max:1000",
            "phosphorus_level" => "nullable|integer|min:0|max:1000",
            "potassium_level" => "nullable|integer|min:0|max:1000",
            "observations" => "nullable|string",
            "weather_condition" => "nullable|string|max:100",
            "temperature" => "nullable|numeric",
            "humidity" => "nullable|numeric|min:0|max:100",
        ];
    }
}
