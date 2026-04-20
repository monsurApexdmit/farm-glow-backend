<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Http\Requests\Farm\StoreFarmRequest;
use App\Http\Requests\Farm\UpdateFarmRequest;
use App\Services\FarmService;
use Illuminate\Http\JsonResponse;

class FarmController extends Controller
{
    protected FarmService $farmService;

    public function __construct(FarmService $farmService)
    {
        $this->farmService = $farmService;
    }

    public function index(): JsonResponse
    {
        try {
            $farms = $this->farmService->getUserFarms(auth()->id());
            return response()->json([
                "message" => "Farms retrieved successfully",
                "data" => $farms,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to retrieve farms",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreFarmRequest $request): JsonResponse
    {
        try {
            $farm = $this->farmService->createFarm(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Farm created successfully",
                "data" => $farm,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to create farm",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Farm $farm): JsonResponse
    {
        try {
            if ($farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Farm not found",
                ], 404);
            }
            return response()->json([
                "message" => "Farm retrieved successfully",
                "data" => $farm,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to retrieve farm",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Farm $farm, UpdateFarmRequest $request): JsonResponse
    {
        try {
            if ($farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Farm not found",
                ], 404);
            }
            $updated = $this->farmService->updateFarm($farm, $request->validated());
            return response()->json([
                "message" => "Farm updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to update farm",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Farm $farm): JsonResponse
    {
        try {
            if ($farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Farm not found",
                ], 404);
            }
            $this->farmService->deleteFarm($farm);
            return response()->json([
                "message" => "Farm deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to delete farm",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function summary(Farm $farm): JsonResponse
    {
        try {
            if ($farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Farm not found",
                ], 404);
            }
            $summary = $this->farmService->getFarmSummary($farm);
            return response()->json([
                "message" => "Farm summary retrieved successfully",
                "data" => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to retrieve farm summary",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function stats(Farm $farm): JsonResponse
    {
        try {
            if ($farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Farm not found",
                ], 404);
            }
            $stats = $this->farmService->getFarmStats($farm);
            return response()->json([
                "message" => "Farm statistics retrieved successfully",
                "data" => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to retrieve farm statistics",
                "message" => $e->getMessage(),
            ], 500);
        }
    }
}
