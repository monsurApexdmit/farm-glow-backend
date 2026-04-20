<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreInvoiceRequest;
use App\Http\Requests\Financial\UpdateInvoiceRequest;
use App\Models\FinancialInvoice;
use App\Services\FinancialInvoiceService;
use Illuminate\Http\JsonResponse;

class FinancialInvoiceController extends Controller {
    protected FinancialInvoiceService $service;
    public function __construct(FinancialInvoiceService $service) { $this->service = $service; }
    public function index(): JsonResponse {
        try {
            $invoices = $this->service->getInvoices(auth()->user()->company_id);
            return response()->json(["message" => "Invoices retrieved", "data" => $invoices]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function store(StoreInvoiceRequest $request): JsonResponse {
        try {
            $invoice = $this->service->createInvoice(auth()->id(), $request->validated());
            return response()->json(["message" => "Invoice created", "data" => $invoice], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(FinancialInvoice $invoice): JsonResponse {
        try {
            if ($invoice->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            return response()->json(["message" => "Invoice retrieved", "data" => $invoice->load(["farm"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function update(UpdateInvoiceRequest $request, FinancialInvoice $invoice): JsonResponse {
        try {
            if ($invoice->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $updated = $this->service->updateInvoice($invoice, $request->validated());
            return response()->json(["message" => "Invoice updated", "data" => $updated]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function destroy(FinancialInvoice $invoice): JsonResponse {
        try {
            if ($invoice->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $this->service->deleteInvoice($invoice, auth()->id());
            return response()->json(["message" => "Invoice deleted"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function markPaid(FinancialInvoice $invoice): JsonResponse {
        try {
            if ($invoice->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            $this->service->markAsPaid($invoice);
            return response()->json(["message" => "Invoice marked as paid", "data" => $invoice->fresh()]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function overdue(): JsonResponse {
        try {
            $invoices = $this->service->getOverdueInvoices(auth()->user()->company_id);
            return response()->json(["message" => "Overdue invoices retrieved", "data" => $invoices]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
