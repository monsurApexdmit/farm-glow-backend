<?php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "farm_id" => "required|exists:farms,id",
            "category_id" => "required|exists:inventory_categories,id",
            "supplier_id" => "nullable|exists:inventory_suppliers,id",
            "name" => "required|string|max:255",
            "sku" => "required|string|unique:inventory_items,sku",
            "description" => "nullable|string",
            "unit" => "required|string",
            "quantity" => "nullable|numeric|min:0",
            "min_quantity" => "nullable|numeric|min:0",
            "max_quantity" => "nullable|numeric|min:0",
            "cost_per_unit" => "required|numeric|min:0",
            "expiry_date" => "nullable|date",
            "location" => "nullable|string",
        ];
    }
}
