<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierRequest;
use App\Http\Requests\Inventory\UpdateSupplierRequest;
use App\Models\InventorySupplier;
use App\Services\InventorySupplierService;
use Illuminate\Http\JsonResponse;

class InventorySupplierController extends Controller {
    protected InventorySupplierService $supplierService;
    public function __construct(InventorySupplierService $supplierService) { $this->supplierService = $supplierService; }
    public function index(): JsonResponse {
        try {
            $suppliers = $this->supplierService->getSuppliers(auth()->user()->company_id);
            return response()->json(["message" => "Suppliers retrieved successfully", "data" => $suppliers]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreSupplierRequest $request): JsonResponse {
        try {
            $supplier = $this->supplierService->createSupplier(auth()->id(), auth()->user()->company_id, $request->validated());
            return response()->json(["message" => "Supplier created successfully", "data" => $supplier], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(InventorySupplier $supplier): JsonResponse {
        try {
            if ($supplier->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Supplier not found"], 404); }
            return response()->json(["message" => "Supplier retrieved successfully", "data" => $supplier->load(["items", "createdBy"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateSupplierRequest $request, InventorySupplier $supplier): JsonResponse {
        try {
            if ($supplier->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Supplier not found"], 404); }
            $updated = $this->supplierService->updateSupplier($supplier, $request->validated());
            return response()->json(["message" => "Supplier updated successfully", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(InventorySupplier $supplier): JsonResponse {
        try {
            if ($supplier->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Supplier not found"], 404); }
            $this->supplierService->deleteSupplier($supplier, auth()->id());
            return response()->json(["message" => "Supplier deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function items(InventorySupplier $supplier): JsonResponse {
        try {
            if ($supplier->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Supplier not found"], 404); }
            $items = $this->supplierService->getSupplierItems($supplier);
            return response()->json(["message" => "Supplier items retrieved successfully", "data" => $items]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
