<?php

namespace App\Http\Requests\Farm;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area' => 'sometimes|numeric|min:0.01',
            'unit' => 'sometimes|in:hectares,acres,square_meters',
            'farm_type' => 'sometimes|string|max:255',
            'soil_type' => 'sometimes|string|max:255',
            'climate_zone' => 'sometimes|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
