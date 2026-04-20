<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Http\Requests\Field\StoreFieldRequest;
use App\Http\Requests\Field\UpdateFieldRequest;
use App\Services\FieldService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    protected FieldService $fieldService;

    public function __construct(FieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $fields = $this->fieldService->getFields(auth()->user()->company_id, $request->query("farm_id"));
            return response()->json([
                "message" => "Fields retrieved successfully",
                "data" => $fields,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreFieldRequest $request): JsonResponse
    {
        try {
            $field = $this->fieldService->createField(
                auth()->id(),
                $request->validated()
            );
            return response()->json([
                "message" => "Field created successfully",
                "data" => $field,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(Field $field): JsonResponse
    {
        try {
            if ($field->farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Field not found",
                ], 404);
            }
            return response()->json([
                "message" => "Field retrieved successfully",
                "data" => $field,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(UpdateFieldRequest $request, Field $field): JsonResponse
    {
        try {
            if ($field->farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Field not found",
                ], 404);
            }
            $updated = $this->fieldService->updateField($field, $request->validated());
            return response()->json([
                "message" => "Field updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(Field $field): JsonResponse
    {
        try {
            if ($field->farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Field not found",
                ], 404);
            }
            $this->fieldService->deleteField($field, auth()->id());
            return response()->json([
                "message" => "Field deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function map(Field $field): JsonResponse
    {
        try {
            if ($field->farm->company_id !== auth()->user()->company_id) {
                return response()->json([
                    "error" => "Field not found",
                ], 404);
            }
            $mapData = $this->fieldService->getFieldMap($field);
            return response()->json([
                "message" => "Field map retrieved successfully",
                "data" => $mapData,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
