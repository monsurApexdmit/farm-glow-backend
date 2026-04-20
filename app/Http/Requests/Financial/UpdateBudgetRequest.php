<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class UpdateBudgetRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "sometimes|exists:farms,id",
            "category" => "sometimes|string|max:255",
            "budgeted_amount" => "sometimes|numeric|min:0.01",
            "month" => "sometimes|integer|min:1|max:12",
            "year" => "sometimes|integer|min:2000",
            "notes" => "nullable|string",
        ];
    }
}
