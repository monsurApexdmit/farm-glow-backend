<?php

namespace App\Services;

use App\Models\Livestock;
use App\Models\LivestockHealthRecord;
use App\Models\Farm;

class LivestockService
{
    public function getLivestock($companyId, $farmId = null, $shedId = null)
    {
        $query = Livestock::query()
            ->active()
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->byFarm($farmId);
        }

        if ($shedId) {
            $query->byShed($shedId);
        }

        return $query->with(["farm", "livestockType", "shed", "createdBy", "healthRecords"])->get();
    }

    public function createLivestock($userId, array $data)
    {
        $data["created_by"] = $userId;
        return Livestock::create($data);
    }

    public function updateLivestock(Livestock $livestock, array $data)
    {
        $livestock->update($data);
        return $livestock;
    }

    public function deleteLivestock(Livestock $livestock)
    {
        $livestock->update(["deleted_by" => auth()->id()]);
        return $livestock->delete();
    }

    public function recordHealthStatus(Livestock $livestock, array $data)
    {
        $data["livestock_id"] = $livestock->id;
        $data["created_by"] = auth()->id();
        return LivestockHealthRecord::create($data);
    }

    public function getHealthHistory(Livestock $livestock)
    {
        return $livestock->healthRecords()
            ->latest()
            ->with("createdBy")
            ->get();
    }
}
