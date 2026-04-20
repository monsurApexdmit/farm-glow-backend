<?php
namespace App\Services;
use App\Models\InventoryCategory;

class InventoryCategoryService {
    public function getCategories() {
        return InventoryCategory::query()->active()->with(["createdBy"])->get();
    }
    public function createCategory($userId, array $data) {
        $data["created_by"] = $userId;
        return InventoryCategory::create($data);
    }
    public function updateCategory(InventoryCategory $category, array $data) {
        $category->update($data);
        return $category;
    }
    public function deleteCategory(InventoryCategory $category) {
        return $category->delete();
    }
}
