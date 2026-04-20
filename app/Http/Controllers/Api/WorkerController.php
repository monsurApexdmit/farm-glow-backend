<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Http\Requests\Worker\StoreWorkerRequest;
use App\Http\Requests\Worker\UpdateWorkerRequest;
use App\Services\WorkerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    protected WorkerService $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $workers = $this->workerService->getWorkers(auth()->user()->company_id, $request->query("farm_id"));
            return response()->json([
                "message" => "Workers retrieved successfully",
                "data" => $workers,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreWorkerRequest $request): JsonResponse
    {
        try {
            $worker = $this->workerService->createWorker(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Worker created successfully",
                "data" => $worker,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(Worker $worker): JsonResponse
    {
        try {
            if ($worker->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Worker not found"], 404);
            }
            return response()->json([
                "message" => "Worker retrieved successfully",
                "data" => $worker,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(UpdateWorkerRequest $request, Worker $worker): JsonResponse
    {
        try {
            if ($worker->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Worker not found"], 404);
            }
            $updated = $this->workerService->updateWorker($worker, $request->validated());
            return response()->json([
                "message" => "Worker updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(Worker $worker): JsonResponse
    {
        try {
            if ($worker->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Worker not found"], 404);
            }
            $this->workerService->deleteWorker($worker);
            return response()->json([
                "message" => "Worker deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getAttendance(Worker $worker): JsonResponse
    {
        try {
            if ($worker->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Worker not found"], 404);
            }
            $attendance = $this->workerService->getAttendanceHistory($worker);
            return response()->json([
                "message" => "Attendance history retrieved successfully",
                "data" => $attendance,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getPerformance(Worker $worker): JsonResponse
    {
        try {
            if ($worker->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Worker not found"], 404);
            }
            $performance = $this->workerService->getPerformanceHistory($worker);
            return response()->json([
                "message" => "Performance history retrieved successfully",
                "data" => $performance,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getPayroll(Worker $worker): JsonResponse
    {
        try {
            if ($worker->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Worker not found"], 404);
            }
            $payroll = $this->workerService->getPayrollHistory($worker);
            return response()->json([
                "message" => "Payroll history retrieved successfully",
                "data" => $payroll,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
