<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "position" => "sometimes|string|max:100",
            "phone" => "nullable|string|max:20",
            "address" => "nullable|string",
            "city" => "nullable|string|max:100",
            "state" => "nullable|string|max:100",
            "postal_code" => "nullable|string|max:20",
            "emergency_contact_name" => "nullable|string|max:100",
            "emergency_contact_phone" => "nullable|string|max:20",
            "hourly_rate" => "nullable|numeric|min:0",
            "monthly_salary" => "nullable|numeric|min:0",
            "termination_date" => "nullable|date",
            "status" => "nullable|in:active,inactive,on-leave,terminated",
        ];
    }
}
