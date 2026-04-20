<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "farm_id" => "required|exists:farms,id",
            "worker_id" => "required|exists:workers,id",
            "work_date" => "required|date",
            "start_time" => "required|date_format:H:i",
            "end_time" => "required|date_format:H:i|after:start_time",
            "shift_type" => "required|string|max:50",
            "notes" => "nullable|string",
        ];
    }
}
