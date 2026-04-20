<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Inventory\UpdateInventoryItemRequest;
use App\Http\Requests\Inventory\RecordTransactionRequest;
use App\Models\InventoryItem;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller {
    protected InventoryService $inventoryService;
    public function __construct(InventoryService $inventoryService) { $this->inventoryService = $inventoryService; }
    public function index(Request $request): JsonResponse {
        try {
            $farmId = $request->query("farm_id");
            $items = $this->inventoryService->getItems(auth()->user()->company_id, $farmId);
            return response()->json(["message" => "Items retrieved successfully", "data" => $items]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreInventoryItemRequest $request): JsonResponse {
        try {
            $item = $this->inventoryService->createItem(auth()->id(), $request->validated());
            return response()->json(["message" => "Item created successfully", "data" => $item], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(InventoryItem $inventory): JsonResponse {
        try {
            if ($inventory->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Item not found"], 404); }
            return response()->json(["message" => "Item retrieved successfully", "data" => $inventory->load(["farm", "category", "supplier", "reorderPoint"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventory): JsonResponse {
        try {
            if ($inventory->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Item not found"], 404); }
            $updated = $this->inventoryService->updateItem($inventory, $request->validated());
            return response()->json(["message" => "Item updated successfully", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(InventoryItem $inventory): JsonResponse {
        try {
            if ($inventory->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Item not found"], 404); }
            $this->inventoryService->deleteItem($inventory, auth()->id());
            return response()->json(["message" => "Item deleted successfully"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function lowStock(): JsonResponse {
        try {
            $items = $this->inventoryService->getLowStockItems(auth()->user()->company_id);
            return response()->json(["message" => "Low stock items retrieved successfully", "data" => $items]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function expired(): JsonResponse {
        try {
            $items = $this->inventoryService->getExpiredItems(auth()->user()->company_id);
            return response()->json(["message" => "Expired items retrieved successfully", "data" => $items]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function value(): JsonResponse {
        try {
            $value = $this->inventoryService->getInventoryValue(auth()->user()->company_id);
            return response()->json(["message" => "Inventory value retrieved successfully", "data" => $value]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function transactions(): JsonResponse {
        try {
            $farmId = request()->query("farm_id");
            $query = \App\Models\InventoryTransaction::query()->whereHas("item.farm", function ($q) {
                $q->where("company_id", auth()->user()->company_id);
            });
            if ($farmId) { $query->whereHas("item", function ($q) use ($farmId) { $q->where("farm_id", $farmId); }); }
            $transactions = $query->with(["item", "createdBy"])->orderByDesc("transaction_date")->get();
            return response()->json(["message" => "Transactions retrieved successfully", "data" => $transactions]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function use(RecordTransactionRequest $request): JsonResponse {
        try {
            $data = $request->validated();
            $data["type"] = "use";
            $transaction = $this->inventoryService->recordTransaction(auth()->id(), $data);
            return response()->json(["message" => "Usage recorded successfully", "data" => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function restock(RecordTransactionRequest $request): JsonResponse {
        try {
            $data = $request->validated();
            $data["type"] = "restock";
            $transaction = $this->inventoryService->recordTransaction(auth()->id(), $data);
            return response()->json(["message" => "Restock recorded successfully", "data" => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function itemTransactions(InventoryItem $item): JsonResponse {
        try {
            if ($item->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Item not found"], 404); }
            $transactions = $this->inventoryService->getItemTransactions($item->id);
            return response()->json(["message" => "Item transactions retrieved successfully", "data" => $transactions]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
