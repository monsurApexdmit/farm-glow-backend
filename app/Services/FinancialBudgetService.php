<?php
namespace App\Services;
use App\Models\FinancialBudget;

class FinancialBudgetService {
    public function getBudgets($companyId) {
        return FinancialBudget::whereHas("farm.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        })->with(["farm", "createdBy"])->get();
    }
    public function createBudget($userId, array $data) {
        $data["created_by"] = $userId;
        return FinancialBudget::create($data);
    }
    public function updateBudget(FinancialBudget $budget, array $data) {
        $budget->update($data);
        return $budget;
    }
    public function deleteBudget(FinancialBudget $budget) {
        return $budget->delete();
    }
    public function getSummary($companyId, $month = null, $year = null) {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $budgets = FinancialBudget::whereHas("farm.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        })->byMonth($month, $year)->get();
        return [
            "total_budgeted" => $budgets->sum("budgeted_amount"),
            "total_spent" => $budgets->sum("spent_amount"),
            "remaining" => $budgets->sum("budgeted_amount") - $budgets->sum("spent_amount"),
        ];
    }
}
