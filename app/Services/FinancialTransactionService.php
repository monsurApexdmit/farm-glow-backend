<?php
namespace App\Services;
use App\Models\FinancialTransaction;
use Carbon\Carbon;

class FinancialTransactionService {
    public function getTransactions($companyId, $farmId = null) {
        $query = FinancialTransaction::whereHas("account.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        });
        if ($farmId) { $query->where("farm_id", $farmId); }
        return $query->with(["account", "farm", "createdBy"])->get();
    }
    public function createTransaction($userId, array $data) {
        $data["created_by"] = $userId;
        return FinancialTransaction::create($data);
    }
    public function updateTransaction(FinancialTransaction $transaction, array $data) {
        $transaction->update($data);
        return $transaction;
    }
    public function deleteTransaction(FinancialTransaction $transaction, $userId) {
        $transaction->update(["deleted_by" => $userId]);
        return $transaction->delete();
    }
    public function getSummary($companyId, $startDate = null, $endDate = null) {
        $query = FinancialTransaction::whereHas("account.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        });
        if ($startDate) { $query->whereDate("transaction_date", ">=", $startDate); }
        if ($endDate) { $query->whereDate("transaction_date", "<=", $endDate); }
        return [
            "total_income" => $query->where("type", "income")->sum("amount"),
            "total_expenses" => $query->where("type", "expense")->sum("amount"),
            "net" => ($query->where("type", "income")->sum("amount") - $query->where("type", "expense")->sum("amount")),
        ];
    }
}
