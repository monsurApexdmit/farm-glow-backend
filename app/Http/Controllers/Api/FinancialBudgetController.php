<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreBudgetRequest;
use App\Http\Requests\Financial\UpdateBudgetRequest;
use App\Models\FinancialBudget;
use App\Services\FinancialBudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialBudgetController extends Controller {
    protected FinancialBudgetService $service;
    public function __construct(FinancialBudgetService $service) { $this->service = $service; }
    public function index(): JsonResponse {
        try {
            $budgets = $this->service->getBudgets(auth()->user()->company_id);
            return response()->json(["message" => "Budgets retrieved", "data" => $budgets]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreBudgetRequest $request): JsonResponse {
        try {
            $budget = $this->service->createBudget(auth()->id(), $request->validated());
            return response()->json(["message" => "Budget created", "data" => $budget], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(FinancialBudget $budget): JsonResponse {
        try {
            if ($budget->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            return response()->json(["message" => "Budget retrieved", "data" => $budget->load(["farm"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateBudgetRequest $request, FinancialBudget $budget): JsonResponse {
        try {
            if ($budget->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $updated = $this->service->updateBudget($budget, $request->validated());
            return response()->json(["message" => "Budget updated", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(FinancialBudget $budget): JsonResponse {
        try {
            if ($budget->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $this->service->deleteBudget($budget);
            return response()->json(["message" => "Budget deleted"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function summary(Request $request): JsonResponse {
        try {
            $month = $request->query("month");
            $year = $request->query("year");
            $summary = $this->service->getSummary(auth()->user()->company_id, $month, $year);
            return response()->json(["message" => "Budget summary retrieved", "data" => $summary]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
