<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "farm_id" => "required|exists:farms,id",
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "email" => "required|email|unique:workers,email",
            "phone" => "nullable|string|max:20",
            "position" => "required|string|max:100",
            "employment_type" => "required|in:full-time,part-time,contract,seasonal",
            "national_id" => "nullable|string|max:50",
            "date_of_birth" => "nullable|date",
            "address" => "nullable|string",
            "city" => "nullable|string|max:100",
            "state" => "nullable|string|max:100",
            "postal_code" => "nullable|string|max:20",
            "emergency_contact_name" => "nullable|string|max:100",
            "emergency_contact_phone" => "nullable|string|max:20",
            "hourly_rate" => "nullable|numeric|min:0",
            "monthly_salary" => "nullable|numeric|min:0",
            "hiring_date" => "required|date",
        ];
    }
}
