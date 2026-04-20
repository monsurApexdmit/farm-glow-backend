<?php

namespace App\Http\Requests\Field;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldRequest extends FormRequest
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
            'soil_type' => 'nullable|string|max:255',
            'area' => 'sometimes|numeric|min:0.01',
            'unit' => 'sometimes|in:hectares,acres,square_meters',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'elevation' => 'nullable|numeric',
            'status' => 'sometimes|in:available,in_use,fallow,preparation',
            'is_active' => 'nullable|boolean',
        ];
    }
}
