<?php
namespace App\Http\Requests\Financial;
use Illuminate\Foundation\Http\FormRequest;
class StoreAccountRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "name" => "required|string|max:255",
            "type" => "required|in:bank,cash,credit,savings",
            "description" => "nullable|string",
            "opening_balance" => "required|numeric|min:0",
            "currency" => "required|string|size:3",
        ];
    }
}
