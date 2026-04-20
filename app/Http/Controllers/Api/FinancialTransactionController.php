<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreTransactionRequest;
use App\Http\Requests\Financial\UpdateTransactionRequest;
use App\Models\FinancialTransaction;
use App\Services\FinancialTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialTransactionController extends Controller {
    protected FinancialTransactionService $service;
    public function __construct(FinancialTransactionService $service) { $this->service = $service; }
    public function index(Request $request): JsonResponse {
        try {
            $farmId = $request->query("farm_id");
            $transactions = $this->service->getTransactions(auth()->user()->company_id, $farmId);
            return response()->json(["message" => "Transactions retrieved", "data" => $transactions]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreTransactionRequest $request): JsonResponse {
        try {
            $transaction = $this->service->createTransaction(auth()->id(), $request->validated());
            return response()->json(["message" => "Transaction created", "data" => $transaction], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(FinancialTransaction $transaction): JsonResponse {
        try {
            if ($transaction->account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            return response()->json(["message" => "Transaction retrieved", "data" => $transaction->load(["account", "farm"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateTransactionRequest $request, FinancialTransaction $transaction): JsonResponse {
        try {
            if ($transaction->account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $updated = $this->service->updateTransaction($transaction, $request->validated());
            return response()->json(["message" => "Transaction updated", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(FinancialTransaction $transaction): JsonResponse {
        try {
            if ($transaction->account->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $this->service->deleteTransaction($transaction, auth()->id());
            return response()->json(["message" => "Transaction deleted"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function summary(): JsonResponse {
        try {
            $summary = $this->service->getSummary(auth()->user()->company_id);
            return response()->json(["message" => "Summary retrieved", "data" => $summary]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
