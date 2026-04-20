<?php

namespace App\Http\Requests\LivestockShed;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivestockShedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "sometimes|string|max:255",
            "description" => "nullable|string",
            "shed_type" => "sometimes|string|max:100",
            "capacity" => "nullable|integer|min:1",
            "length" => "nullable|numeric|min:0",
            "width" => "nullable|numeric|min:0",
            "area" => "nullable|numeric|min:0",
            "temperature_min" => "nullable|numeric",
            "temperature_max" => "nullable|numeric",
            "humidity_level" => "nullable|numeric|min:0|max:100",
            "status" => "nullable|in:operational,maintenance,inactive",
        ];
    }
}
