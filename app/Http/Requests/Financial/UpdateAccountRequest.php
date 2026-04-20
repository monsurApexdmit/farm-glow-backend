<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class UpdateAccountRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "name" => "sometimes|string|max:255",
            "type" => "sometimes|in:bank,cash,credit,savings",
            "description" => "nullable|string",
            "opening_balance" => "sometimes|numeric|min:0",
            "currency" => "sometimes|string|size:3",
        ];
    }
}
