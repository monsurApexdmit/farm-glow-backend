<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\GenerateReportRequest;
use App\Models\FinancialReport;
use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;

class FinancialReportController extends Controller {
    protected FinancialReportService $service;
    public function __construct(FinancialReportService $service) { $this->service = $service; }
    public function index(): JsonResponse {
        try {
            $reports = $this->service->getReports(auth()->user()->company_id);
            return response()->json(["message" => "Reports retrieved", "data" => $reports]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function generate(GenerateReportRequest $request): JsonResponse {
        try {
            $report = $this->service->generateReport(auth()->id(), $request->validated());
            return response()->json(["message" => "Report generated", "data" => $report], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
    public function show(FinancialReport $report): JsonResponse {
        try {
            if ($report->farm->company_id !== auth()->user()->company_id) { return response()->json(["error" => "Not found"], 404); }
            return response()->json(["message" => "Report retrieved", "data" => $report->load(["farm"])]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
