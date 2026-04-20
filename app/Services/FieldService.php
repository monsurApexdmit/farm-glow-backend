<?php

namespace App\Services;

use App\Models\Field;

class FieldService
{
    public function getFields($companyId, $farmId = null)
    {
        $query = Field::query()
            ->active()
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->byFarm($farmId);
        }

        return $query->with(["farm", "createdBy"])->get();
    }

    public function createField($userId, array $data)
    {
        $data["created_by"] = $userId;

        return Field::create($data);
    }

    public function updateField(Field $field, array $data)
    {
        $field->update($data);
        return $field;
    }

    public function deleteField(Field $field, $userId)
    {
        $field->update(["deleted_by" => $userId]);
        return $field->delete();
    }

    public function getFieldMap(Field $field)
    {
        return [
            "latitude" => $field->latitude,
            "longitude" => $field->longitude,
            "elevation" => $field->elevation,
        ];
    }
}
