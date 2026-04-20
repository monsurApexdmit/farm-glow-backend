<?php

namespace App\Http\Requests\Field;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'soil_type' => 'nullable|string|max:255',
            'area' => 'required|numeric|min:0.01',
            'unit' => 'required|in:hectares,acres,square_meters',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'elevation' => 'nullable|numeric',
            'status' => 'required|in:available,in_use,fallow,preparation',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'farm_id.required' => 'Farm selection is required',
            'farm_id.exists' => 'Selected farm does not exist',
            'name.required' => 'Field name is required',
            'area.required' => 'Field area is required',
            'area.min' => 'Field area must be greater than 0',
            'status.required' => 'Field status is required',
        ];
    }
}
