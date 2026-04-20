<?php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "name" => "sometimes|string|max:255",
            "slug" => "sometimes|string|max:255|unique:inventory_categories,slug," . $this->route('inventory_category.id'),
            "description" => "nullable|string",
            "icon" => "nullable|string",
            "color" => "nullable|string",
        ];
    }
}
