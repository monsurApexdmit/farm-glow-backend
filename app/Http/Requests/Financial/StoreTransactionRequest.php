<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class StoreTransactionRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "account_id" => "required|exists:financial_accounts,id",
            "farm_id" => "nullable|exists:farms,id",
            "type" => "required|in:income,expense",
            "category" => "required|string|max:255",
            "description" => "nullable|string",
            "amount" => "required|numeric|min:0.01",
            "reference_number" => "nullable|string|max:255",
            "transaction_date" => "required|date",
            "notes" => "nullable|string",
        ];
    }
}
