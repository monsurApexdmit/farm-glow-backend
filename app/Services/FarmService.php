<?php

namespace App\Services;

use App\Models\Farm;
use App\Models\User;

class FarmService
{
    public function getUserFarms($userId)
    {
        $user = User::findOrFail($userId);
        return Farm::byCompany($user->company_id)
            ->active()
            ->with(["fields", "createdBy"])
            ->get();
    }

    public function createFarm($userId, array $data)
    {
        $user = User::findOrFail($userId);
        $data["company_id"] = $user->company_id;
        $data["created_by"] = $userId;

        return Farm::create($data);
    }

    public function updateFarm(Farm $farm, array $data)
    {
        $farm->update($data);
        return $farm;
    }

    public function deleteFarm(Farm $farm)
    {
        $farm->update(["deleted_by" => auth()->id()]);
        return $farm->delete();
    }

    public function getFarmSummary(Farm $farm)
    {
        return [
            "total_fields" => $farm->getFieldsCount(),
            "total_area" => $farm->getTotalFieldsArea(),
            "unit" => $farm->unit,
        ];
    }

    public function getFarmStats(Farm $farm)
    {
        $fields = $farm->fields()->get();

        return [
            "total_fields" => count($fields),
            "area_by_status" => $this->getAreaByStatus($fields),
        ];
    }

    private function getAreaByStatus($fields)
    {
        $statusArea = [];
        foreach ($fields as $field) {
            if (!isset($statusArea[$field->status])) {
                $statusArea[$field->status] = 0;
            }
            $statusArea[$field->status] += $field->area;
        }
        return $statusArea;
    }
}
