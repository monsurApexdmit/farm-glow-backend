<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class UpdateInvoiceRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "sometimes|exists:farms,id",
            "client_name" => "sometimes|string|max:255",
            "client_email" => "nullable|email",
            "description" => "nullable|string",
            "amount" => "sometimes|numeric|min:0.01",
            "issue_date" => "sometimes|date",
            "due_date" => "sometimes|date|after:issue_date",
            "notes" => "nullable|string",
        ];
    }
}
