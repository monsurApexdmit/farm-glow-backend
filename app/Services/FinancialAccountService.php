<?php
namespace App\Services;
use App\Models\FinancialAccount;

class FinancialAccountService {
    public function getAccounts($companyId) {
        return FinancialAccount::byCompany($companyId)->active()->with(["createdBy"])->get();
    }
    public function createAccount($userId, $companyId, array $data) {
        $data["created_by"] = $userId;
        $data["company_id"] = $companyId;
        return FinancialAccount::create($data);
    }
    public function updateAccount(FinancialAccount $account, array $data) {
        $account->update($data);
        return $account;
    }
    public function deleteAccount(FinancialAccount $account, $userId) {
        $account->update(["deleted_by" => $userId]);
        return $account->delete();
    }
    public function getAccountBalance(FinancialAccount $account) {
        $income = $account->transactions()->where("type", "income")->sum("amount");
        $expenses = $account->transactions()->where("type", "expense")->sum("amount");
        return $account->opening_balance + $income - $expenses;
    }
}
