<?php

namespace App\Services;

use App\Models\LivestockBreedingRecord;

class BreedingService
{
    public function getBreedingRecords($companyId, $farmId = null)
    {
        $query = LivestockBreedingRecord::query()
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->byFarm($farmId);
        }

        return $query->with(["farm", "male", "female", "createdBy"])->get();
    }

    public function createBreedingRecord($userId, array $data)
    {
        $data["created_by"] = $userId;
        return LivestockBreedingRecord::create($data);
    }

    public function updateBreedingRecord(LivestockBreedingRecord $record, array $data)
    {
        $record->update($data);
        return $record;
    }

    public function recordBirth(LivestockBreedingRecord $record, array $data)
    {
        $record->update([
            "actual_birth_date" => $data["actual_birth_date"],
            "offspring_count" => $data["offspring_count"],
            "observations" => $data["observations"] ?? null,
            "status" => "completed",
        ]);
        return $record;
    }

    public function deleteBreedingRecord(LivestockBreedingRecord $record)
    {
        return $record->delete();
    }
}
