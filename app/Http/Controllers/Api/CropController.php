<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Http\Requests\Crop\StoreCropRequest;
use App\Http\Requests\Crop\UpdateCropRequest;
use App\Http\Requests\Crop\StoreCropHealthRequest;
use App\Http\Requests\Crop\HarvestCropRequest;
use App\Services\CropService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CropController extends Controller
{
    protected CropService $cropService;

    public function __construct(CropService $cropService)
    {
        $this->cropService = $cropService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $crops = $this->cropService->getCrops(auth()->user()->company_id, $request->query("field_id"));
            return response()->json([
                "message" => "Crops retrieved successfully",
                "data" => $crops,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreCropRequest $request): JsonResponse
    {
        try {
            $crop = $this->cropService->createCrop(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Crop created successfully",
                "data" => $crop,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            return response()->json([
                "message" => "Crop retrieved successfully",
                "data" => $crop,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(UpdateCropRequest $request, Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            $updated = $this->cropService->updateCrop($crop, $request->validated());
            return response()->json([
                "message" => "Crop updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            $this->cropService->deleteCrop($crop);
            return response()->json([
                "message" => "Crop deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function recordHealth(StoreCropHealthRequest $request, Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            $healthRecord = $this->cropService->recordHealthStatus($crop, $request->validated());
            return response()->json([
                "message" => "Health record created successfully",
                "data" => $healthRecord,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getHealth(Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            $healthHistory = $this->cropService->getHealthHistory($crop);
            return response()->json([
                "message" => "Health history retrieved successfully",
                "data" => $healthHistory,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function recordHarvest(HarvestCropRequest $request, Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            $harvested = $this->cropService->recordHarvest($crop, $request->validated());
            return response()->json([
                "message" => "Harvest recorded successfully",
                "data" => $harvested,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getYield(Crop $crop): JsonResponse
    {
        try {
            if ($crop->field->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Crop not found"], 404);
            }
            $yieldInfo = $this->cropService->getYieldInfo($crop);
            return response()->json([
                "message" => "Yield information retrieved successfully",
                "data" => $yieldInfo,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
