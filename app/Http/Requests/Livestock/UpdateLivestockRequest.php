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
            "weight" => "nullable|numeric|min:0",
            "weight_unit" => "nullable|string|max:50",
            "status" => "nullable|in:active,inactive,sold,deceased",
        ];
    }
}
