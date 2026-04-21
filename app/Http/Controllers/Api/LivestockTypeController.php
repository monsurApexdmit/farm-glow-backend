<?php

namespace App\Http\Controllers\Api;

use App\Models\LivestockType;
use Illuminate\Http\JsonResponse;

class LivestockTypeController
{
    public function index(): JsonResponse
    {
        $types = LivestockType::all();
        return response()->json([
            "message" => "Livestock types retrieved successfully",
            "data" => $types,
        ]);
    }

    public function show(LivestockType $livestockType): JsonResponse
    {
        return response()->json([
            "message" => "Livestock type retrieved successfully",
            "data" => $livestockType,
        ]);
    }
}
