<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LivestockShed;
use App\Http\Requests\LivestockShed\StoreLivestockShedRequest;
use App\Http\Requests\LivestockShed\UpdateLivestockShedRequest;
use App\Services\LivestockShedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivestockShedController extends Controller
{
    protected LivestockShedService $shedService;

    public function __construct(LivestockShedService $shedService)
    {
        $this->shedService = $shedService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $sheds = $this->shedService->getSheds(
                auth()->user()->company_id,
                $request->query("farm_id")
            );
            return response()->json([
                "message" => "Sheds retrieved successfully",
                "data" => $sheds,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreLivestockShedRequest $request): JsonResponse
    {
        try {
            $shed = $this->shedService->createShed(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Shed created successfully",
                "data" => $shed,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(LivestockShed $shed): JsonResponse
    {
        try {
            if ($shed->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Shed not found"], 404);
            }
            return response()->json([
                "message" => "Shed retrieved successfully",
                "data" => $shed,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(UpdateLivestockShedRequest $request, LivestockShed $shed): JsonResponse
    {
        try {
            if ($shed->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Shed not found"], 404);
            }
            $updated = $this->shedService->updateShed($shed, $request->validated());
            return response()->json([
                "message" => "Shed updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(LivestockShed $shed): JsonResponse
    {
        try {
            if ($shed->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Shed not found"], 404);
            }
            $this->shedService->deleteShed($shed);
            return response()->json([
                "message" => "Shed deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function recordCleaning(LivestockShed $shed): JsonResponse
    {
        try {
            if ($shed->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Shed not found"], 404);
            }
            $updated = $this->shedService->recordCleaning($shed);
            return response()->json([
                "message" => "Cleaning recorded successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getGrid(LivestockShed $shed): JsonResponse
    {
        try {
            if ($shed->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Shed not found"], 404);
            }
            $grid = $this->shedService->getShedGrid($shed);
            return response()->json([
                "message" => "Shed grid retrieved successfully",
                "data" => $grid,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getStats(LivestockShed $shed): JsonResponse
    {
        try {
            if ($shed->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Shed not found"], 404);
            }
            $stats = $this->shedService->getShedStats($shed);
            return response()->json([
                "message" => "Shed statistics retrieved successfully",
                "data" => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
