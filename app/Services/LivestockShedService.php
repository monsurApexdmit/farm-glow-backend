<?php

namespace App\Services;

use App\Models\LivestockShed;

class LivestockShedService
{
    public function getSheds($companyId, $farmId = null)
    {
        $query = LivestockShed::query()
            ->active()
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->byFarm($farmId);
        }

        return $query->with(["farm", "createdBy", "livestocks"])->get();
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

    public function getShedStats(LivestockShed $shed)
    {
        $livestocks = $shed->livestocks()->where("is_active", true)->get();
        
        return [
            "total_capacity" => $shed->capacity,
            "current_occupancy" => count($livestocks),
            "available_capacity" => $shed->getAvailableCapacity(),
            "occupancy_percentage" => round($shed->getOccupancy(), 2),
            "livestock_types" => $livestocks->groupBy(function ($item) {
                return $item->livestockType->name ?? "Unknown";
            })->map->count(),
            "healthy_count" => $livestocks->filter(function ($item) {
                $latest = $item->getLatestHealthRecord();
                return $latest && $latest->health_status === "healthy";
            })->count(),
            "sick_count" => $livestocks->filter(function ($item) {
                $latest = $item->getLatestHealthRecord();
                return $latest && $latest->health_status !== "healthy";
            })->count(),
        ];
    }

    public function getShedGrid(LivestockShed $shed, $gridSize = 10)
    {
        $livestocks = $shed->livestocks()->where("is_active", true)->get();
        $grid = [];

        for ($i = 0; $i < $gridSize; $i++) {
            $grid[$i] = [];
            for ($j = 0; $j < $gridSize; $j++) {
                $grid[$i][$j] = null;
            }
        }

        $index = 0;
        foreach ($livestocks as $livestock) {
            if ($index < ($gridSize * $gridSize)) {
                $row = intdiv($index, $gridSize);
                $col = $index % $gridSize;
                $grid[$row][$col] = [
                    "id" => $livestock->id,
                    "tag_number" => $livestock->tag_number,
                    "type" => $livestock->livestockType->name,
                    "status" => $livestock->status,
                ];
                $index++;
            }
        }

        return $grid;
    }
}
