<?php
namespace App\Services;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Carbon\Carbon;

class InventoryService {
    public function getItems($companyId, $farmId = null) {
        $query = InventoryItem::query()->active()->whereHas("farm", function ($q) use ($companyId) {
            $q->where("company_id", $companyId);
        });
        if ($farmId) { $query->byFarm($farmId); }
        return $query->with(["farm", "category", "supplier", "reorderPoint", "createdBy"])->get();
    }
    public function createItem($userId, array $data) {
        $data["created_by"] = $userId;
        if (isset($data["quantity"]) && isset($data["cost_per_unit"])) {
            $data["total_value"] = $data["quantity"] * $data["cost_per_unit"];
        }
        return InventoryItem::create($data);
    }
    public function updateItem(InventoryItem $item, array $data) {
        $item->update($data);
        if (isset($data["quantity"]) || isset($data["cost_per_unit"])) {
            $quantity = $data["quantity"] ?? $item->quantity;
            $costPerUnit = $data["cost_per_unit"] ?? $item->cost_per_unit;
            $item->update(["total_value" => $quantity * $costPerUnit]);
        }
        return $item->fresh();
    }
    public function deleteItem(InventoryItem $item, $userId) {
        $item->update(["deleted_by" => $userId]);
        return $item->delete();
    }
    public function getLowStockItems($companyId) {
        return InventoryItem::query()->active()->whereHas("farm", function ($q) use ($companyId) {
            $q->where("company_id", $companyId);
        })->whereHas("reorderPoint")->with(["farm", "category", "reorderPoint"])->get()->filter(function ($item) {
            return $item->quantity <= $item->reorderPoint->reorder_point;
        });
    }
    public function getExpiredItems($companyId) {
        return InventoryItem::query()->active()->whereHas("farm", function ($q) use ($companyId) {
            $q->where("company_id", $companyId);
        })->whereNotNull("expiry_date")->whereDate("expiry_date", "<", now())->with(["farm", "category"])->get();
    }
    public function getInventoryValue($companyId) {
        $items = $this->getItems($companyId);
        $value = [];
        foreach ($items as $item) {
            $farmId = $item->farm_id;
            if (!isset($value[$farmId])) { $value[$farmId] = 0; }
            $value[$farmId] += $item->total_value;
        }
        return $value;
    }
    public function recordTransaction($userId, array $data) {
        $item = InventoryItem::findOrFail($data["inventory_item_id"]);
        $quantityBefore = (float)$item->quantity;
        $quantity = (float)$data["quantity"];
        $quantityAfter = match($data["type"]) {
            "use" => $quantityBefore - $quantity,
            "restock" => $quantityBefore + $quantity,
            "adjustment" => $quantity,
            "loss" => $quantityBefore - $quantity,
            default => $quantityBefore,
        };
        $transactionData = [
            "inventory_item_id" => $item->id,
            "type" => $data["type"],
            "quantity" => $quantity,
            "quantity_before" => $quantityBefore,
            "quantity_after" => $quantityAfter,
            "cost_per_unit" => $data["cost_per_unit"] ?? null,
            "notes" => $data["notes"] ?? null,
            "reference_number" => $data["reference_number"] ?? null,
            "transaction_date" => $data["transaction_date"],
            "created_by" => $userId,
        ];
        $transaction = InventoryTransaction::create($transactionData);
        $item->update(["quantity" => $quantityAfter, "total_value" => $quantityAfter * $item->cost_per_unit]);
        return $transaction->load(["item", "createdBy"]);
    }
    public function getItemTransactions($itemId) {
        return InventoryTransaction::where("inventory_item_id", $itemId)->with(["item", "createdBy"])->orderByDesc("transaction_date")->get();
    }
}
