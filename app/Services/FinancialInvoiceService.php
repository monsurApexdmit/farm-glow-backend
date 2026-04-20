<?php
namespace App\Services;
use App\Models\FinancialInvoice;

class FinancialInvoiceService {
    public function getInvoices($companyId) {
        return FinancialInvoice::whereHas("farm.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        })->with(["farm", "createdBy"])->get();
    }
    public function createInvoice($userId, array $data) {
        $data["created_by"] = $userId;
        return FinancialInvoice::create($data);
    }
    public function updateInvoice(FinancialInvoice $invoice, array $data) {
        $invoice->update($data);
        return $invoice;
    }
    public function deleteInvoice(FinancialInvoice $invoice, $userId) {
        $invoice->update(["deleted_by" => $userId]);
        return $invoice->delete();
    }
    public function markAsPaid(FinancialInvoice $invoice) {
        return $invoice->update(["status" => "paid", "paid_date" => now()]);
    }
    public function getOverdueInvoices($companyId) {
        return FinancialInvoice::whereHas("farm.company", function ($q) use ($companyId) {
            $q->where("id", $companyId);
        })->where("status", "pending")->where("due_date", "<", now())->with(["farm"])->get();
    }
}
