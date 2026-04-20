<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class GenerateReportRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "required|exists:farms,id",
            "type" => "required|in:monthly,quarterly,yearly",
            "month" => "nullable|integer|min:1|max:12",
            "year" => "required|integer|min:2000",
        ];
    }
}
