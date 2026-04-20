<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "farm_id" => "required|exists:farms,id",
            "livestock_type_id" => "required|exists:livestock_types,id",
            "shed_id" => "nullable|exists:livestock_sheds,id",
            "tag_number" => "required|unique:livestocks,tag_number",
            "name" => "nullable|string|max:255",
            "description" => "nullable|string",
            "breed" => "nullable|string|max:100",
            "gender" => "required|in:male,female",
            "date_of_birth" => "nullable|date",
            "acquisition_date" => "nullable|date",
            "weight" => "nullable|numeric|min:0",
            "weight_unit" => "nullable|string|max:50",
        ];
    }
}
