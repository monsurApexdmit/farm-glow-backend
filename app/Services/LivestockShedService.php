<?php

namespace App\Services;

use App\Models\LivestockShed;

class LivestockShedService
{
    public function getSheds($companyId, $farmId = null)
    {
        $query = LivestockShed::with("farm")
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->where("farm_id", $farmId);
        }

        return $query->get();
    }

    public function createShed($userId, array $data)
    {
        $data["created_by"] = $userId;
        return LivestockShed::create($data);
    }

    public function updateShed(LivestockShed $shed, array $data)
    {
        $shed->update($data);
        return $shed;
    }

    public function deleteShed(LivestockShed $shed)
    {
        $shed->update(["deleted_by" => auth()->id()]);
        return $shed->delete();
    }

    public function recordCleaning(LivestockShed $shed)
    {
        $shed->update(["last_cleaned_at" => now()]);
        return $shed;
    }

    public function getShedGrid(LivestockShed $shed)
    {
        $livestocks = $shed->livestocks()->get();
        $grid = [];

        foreach ($livestocks as $index => $livestock) {
            $grid[] = [
                "position" => $index,
                "livestock_id" => $livestock->id,
                "status" => $livestock->status,
            ];
        }

        return $grid;
    }

    public function getShedStats(LivestockShed $shed)
    {
        $livestocks = $shed->livestocks()->get();
        $healthyCount = $livestocks->where("status", "healthy")->count();
        $sickCount = $livestocks->where("status", "sick")->count();

        return [
            "total" => $livestocks->count(),
            "healthy" => $healthyCount,
            "sick" => $sickCount,
            "capacity_used" => round(($livestocks->count() / $shed->capacity) * 100),
        ];
    }
}
