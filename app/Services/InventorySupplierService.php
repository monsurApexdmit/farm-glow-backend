<?php
namespace App\Services;
use App\Models\InventorySupplier;

class InventorySupplierService {
    public function getSuppliers($companyId) {
        return InventorySupplier::query()->byCompany($companyId)->active()->with(["createdBy"])->get();
    }
    public function createSupplier($userId, $companyId, array $data) {
        $data["created_by"] = $userId;
        $data["company_id"] = $companyId;
        return InventorySupplier::create($data);
    }
    public function updateSupplier(InventorySupplier $supplier, array $data) {
        $supplier->update($data);
        return $supplier;
    }
    public function deleteSupplier(InventorySupplier $supplier, $userId) {
        $supplier->update(["deleted_by" => $userId]);
        return $supplier->delete();
    }
    public function getSupplierItems(InventorySupplier $supplier) {
        return $supplier->items()->with(["category", "reorderPoint"])->get();
    }
}
