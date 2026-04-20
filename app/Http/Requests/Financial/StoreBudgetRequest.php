<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class StoreBudgetRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "required|exists:farms,id",
            "category" => "required|string|max:255",
            "budgeted_amount" => "required|numeric|min:0.01",
            "month" => "required|integer|min:1|max:12",
            "year" => "required|integer|min:2000",
            "notes" => "nullable|string",
        ];
    }
}
