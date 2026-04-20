<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\CropHealthRecord;
use App\Models\Field;

class CropService
{
    public function getCrops($companyId, $fieldId = null)
    {
        $query = Crop::query()
            ->active()
            ->whereHas("field.farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($fieldId) {
            $query->byField($fieldId);
        }

        return $query->with(["field", "createdBy", "healthRecords"])->get();
    }

    public function createCrop($userId, array $data)
    {
        $data["created_by"] = $userId;
        return Crop::create($data);
    }

    public function updateCrop(Crop $crop, array $data)
    {
        $crop->update($data);
        return $crop;
    }

    public function deleteCrop(Crop $crop)
    {
        $crop->update(["deleted_by" => auth()->id()]);
        return $crop->delete();
    }

    public function recordHealthStatus(Crop $crop, array $data)
    {
        $data["crop_id"] = $crop->id;
        $data["created_by"] = auth()->id();
        return CropHealthRecord::create($data);
    }

    public function getHealthHistory(Crop $crop)
    {
        return $crop->healthRecords()
            ->latest()
            ->with("createdBy")
            ->get();
    }

    public function recordHarvest(Crop $crop, array $data)
    {
        $crop->update([
            "actual_harvest_date" => $data["actual_harvest_date"],
            "actual_yield" => $data["actual_yield"],
            "yield_unit" => $data["yield_unit"] ?? $crop->yield_unit,
            "status" => "harvested",
        ]);
        return $crop;
    }

    public function getYieldInfo(Crop $crop)
    {
        return [
            "estimated_yield" => $crop->estimated_yield,
            "actual_yield" => $crop->actual_yield,
            "yield_unit" => $crop->yield_unit,
            "planting_date" => $crop->planting_date,
            "expected_harvest_date" => $crop->expected_harvest_date,
            "actual_harvest_date" => $crop->actual_harvest_date,
            "days_to_harvest" => $crop->expected_harvest_date ? $crop->expected_harvest_date->diffInDays(now()) : null,
            "status" => $crop->status,
        ];
    }
}
