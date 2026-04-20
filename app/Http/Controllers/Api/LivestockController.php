<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livestock;
use App\Http\Requests\Livestock\StoreLivestockRequest;
use App\Http\Requests\Livestock\UpdateLivestockRequest;
use App\Http\Requests\Livestock\StoreLivestockHealthRequest;
use App\Services\LivestockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivestockController extends Controller
{
    protected LivestockService $livestockService;

    public function __construct(LivestockService $livestockService)
    {
        $this->livestockService = $livestockService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $livestock = $this->livestockService->getLivestock(
                auth()->user()->company_id,
                $request->query("farm_id"),
                $request->query("shed_id")
            );
            return response()->json([
                "message" => "Livestock retrieved successfully",
                "data" => $livestock,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreLivestockRequest $request): JsonResponse
    {
        try {
            $livestock = $this->livestockService->createLivestock(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Livestock created successfully",
                "data" => $livestock,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            return response()->json([
                "message" => "Livestock retrieved successfully",
                "data" => $livestock,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(UpdateLivestockRequest $request, Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $updated = $this->livestockService->updateLivestock($livestock, $request->validated());
            return response()->json([
                "message" => "Livestock updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $this->livestockService->deleteLivestock($livestock);
            return response()->json([
                "message" => "Livestock deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function recordHealth(StoreLivestockHealthRequest $request, Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $healthRecord = $this->livestockService->recordHealthStatus($livestock, $request->validated());
            return response()->json([
                "message" => "Health record created successfully",
                "data" => $healthRecord,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getHealth(Livestock $livestock): JsonResponse
    {
        try {
            if ($livestock->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Livestock not found"], 404);
            }
            $healthHistory = $this->livestockService->getHealthHistory($livestock);
            return response()->json([
                "message" => "Health history retrieved successfully",
                "data" => $healthHistory,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
