<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "shed_id" => "nullable|exists:livestock_sheds,id",
            "name" => "nullable|string|max:255",
            "description" => "nullable|string",
            "breed" => "nullable|string|max:100",
            "gender" => "nullable|in:male,female",
            "date_of_birth" => "nullable|date",
            "acquisition_date" => "nullable|date",
            "weight" => "nullable|numeric|min:0",
            "weight_unit" => "nullable|string|max:50",
            "livestock_type_id" => "nullable|exists:livestock_types,id",
            "status" => "nullable|in:active,inactive,sold,deceased",
        ];
    }
}
