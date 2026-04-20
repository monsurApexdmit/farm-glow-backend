<?php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "name" => "required|string|max:255",
            "email" => "nullable|email",
            "phone" => "nullable|string",
            "address" => "nullable|string",
            "city" => "nullable|string",
            "state" => "nullable|string",
            "country" => "nullable|string",
            "website" => "nullable|url",
            "notes" => "nullable|string",
        ];
    }
}
