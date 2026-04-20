<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class UpdateTransactionRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "account_id" => "sometimes|exists:financial_accounts,id",
            "farm_id" => "nullable|exists:farms,id",
            "type" => "sometimes|in:income,expense",
            "category" => "sometimes|string|max:255",
            "description" => "nullable|string",
            "amount" => "sometimes|numeric|min:0.01",
            "reference_number" => "nullable|string|max:255",
            "transaction_date" => "sometimes|date",
            "notes" => "nullable|string",
        ];
    }
}
