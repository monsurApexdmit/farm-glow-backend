<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class RecordAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "worker_id" => "required|exists:workers,id",
            "attendance_date" => "required|date",
            "check_in_time" => "required|date_format:H:i:s",
            "check_out_time" => "nullable|date_format:H:i:s",
            "status" => "required|in:present,absent,late,excused",
            "notes" => "nullable|string",
        ];
    }
}
