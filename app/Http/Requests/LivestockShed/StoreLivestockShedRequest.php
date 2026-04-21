<?php

namespace App\Http\Requests\LivestockShed;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestockShedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "farm_id" => "required|exists:farms,id",
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "shed_type" => "required|string|max:100",
            "capacity" => "nullable|integer|min:1",
            "length" => "nullable|numeric|min:0",
            "width" => "nullable|numeric|min:0",
            "height" => "nullable|numeric|min:0",
            "area" => "nullable|numeric|min:0",
            "temperature_min" => "nullable|numeric",
            "temperature_max" => "nullable|numeric",
            "humidity_level" => "nullable|numeric|min:0|max:100",
            "feed_schedule" => "nullable|string|max:255",
        ];
    }
}
