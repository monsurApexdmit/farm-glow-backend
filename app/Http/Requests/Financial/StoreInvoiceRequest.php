<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class StoreInvoiceRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "required|exists:farms,id",
            "invoice_number" => "nullable|string|max:255|unique:financial_invoices,invoice_number",
            "client_name" => "required|string|max:255",
            "client_email" => "nullable|email",
            "description" => "nullable|string",
            "amount" => "required|numeric|min:0.01",
            "issue_date" => "required|date",
            "due_date" => "required|date|after:issue_date",
            "notes" => "nullable|string",
        ];
    }
    protected function prepareForValidation() {
        if (!$this->invoice_number) {
            $this->merge(["invoice_number" => "INV-" . date("YmdHis") . "-" . random_int(100, 999)]);
        }
    }
}
