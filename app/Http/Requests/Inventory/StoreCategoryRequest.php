<?php
namespace App\Http\Requests\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            "name" => "required|string|max:255",
            "slug" => "nullable|string|max:255|unique:inventory_categories,slug",
            "description" => "nullable|string",
            "icon" => "nullable|string",
            "color" => "nullable|string",
        ];
    }
}
