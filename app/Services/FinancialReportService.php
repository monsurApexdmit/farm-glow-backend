<?php
namespace App\Services;
use App\Models\FinancialReport;
use App\Models\FinancialTransaction;

class FinancialReportService {
    public function getReports($companyId) {
        return FinancialReport::whereHas("farm.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        })->with(["farm", "createdBy"])->get();
    }
    public function generateReport($userId, array $data) {
        $data["created_by"] = $userId;
        $transactions = FinancialTransaction::whereHas("account.company", function ($q) use ($userId) {
            $q->where("id", auth()->user()->company_id);
        })->get();
        $data["total_income"] = $transactions->where("type", "income")->sum("amount");
        $data["total_expenses"] = $transactions->where("type", "expense")->sum("amount");
        $data["net_profit"] = $data["total_income"] - $data["total_expenses"];
        return FinancialReport::create($data);
    }
}
