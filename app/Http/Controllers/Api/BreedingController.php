<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LivestockBreedingRecord;
use App\Http\Requests\LivestockBreeding\StoreBreedingRequest;
use App\Http\Requests\LivestockBreeding\RecordBirthRequest;
use App\Services\BreedingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BreedingController extends Controller
{
    protected BreedingService $breedingService;

    public function __construct(BreedingService $breedingService)
    {
        $this->breedingService = $breedingService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $records = $this->breedingService->getBreedingRecords(
                auth()->user()->company_id,
                $request->query("farm_id")
            );
            return response()->json([
                "message" => "Breeding records retrieved successfully",
                "data" => $records,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreBreedingRequest $request): JsonResponse
    {
        try {
            $record = $this->breedingService->createBreedingRecord(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Breeding record created successfully",
                "data" => $record,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(LivestockBreedingRecord $breeding): JsonResponse
    {
        try {
            if ($breeding->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Record not found"], 404);
            }
            return response()->json([
                "message" => "Breeding record retrieved successfully",
                "data" => $breeding,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => "Not found"], 404);
        }
    }

    public function update(Request $request, LivestockBreedingRecord $breeding): JsonResponse
    {
        try {
            if ($breeding->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Record not found"], 404);
            }
            $updated = $this->breedingService->updateBreedingRecord($breeding, $request->only([
                "observations",
                "expected_birth_date",
            ]));
            return response()->json([
                "message" => "Breeding record updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function recordBirth(RecordBirthRequest $request, LivestockBreedingRecord $breeding): JsonResponse
    {
        try {
            if ($breeding->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Record not found"], 404);
            }
            $updated = $this->breedingService->recordBirth($breeding, $request->validated());
            return response()->json([
                "message" => "Birth recorded successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(LivestockBreedingRecord $breeding): JsonResponse
    {
        try {
            if ($breeding->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Record not found"], 404);
            }
            $this->breedingService->deleteBreedingRecord($breeding);
            return response()->json([
                "message" => "Breeding record deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
