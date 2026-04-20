<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreCategoryRequest;
use App\Http\Requests\Inventory\UpdateCategoryRequest;
use App\Models\InventoryCategory;
use App\Services\InventoryCategoryService;
use Illuminate\Http\JsonResponse;

class InventoryCategoryController extends Controller {
    protected InventoryCategoryService $categoryService;
    public function __construct(InventoryCategoryService $categoryService) { $this->categoryService = $categoryService; }
    public function index(): JsonResponse {
        try {
            $categories = $this->categoryService->getCategories();
            return response()->json(["message" => "Categories retrieved successfully", "data" => $categories]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreCategoryRequest $request): JsonResponse {
        try {
            $category = $this->categoryService->createCategory(auth()->id(), $request->validated());
            return response()->json(["message" => "Category created successfully", "data" => $category], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(InventoryCategory $category): JsonResponse {
        try {
            return response()->json(["message" => "Category retrieved successfully", "data" => $category->load(["items"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateCategoryRequest $request, InventoryCategory $category): JsonResponse {
        try {
            $updated = $this->categoryService->updateCategory($category, $request->validated());
            return response()->json(["message" => "Category updated successfully", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(InventoryCategory $category): JsonResponse {
        try {
            $this->categoryService->deleteCategory($category);
            return response()->json(["message" => "Category deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
