<?php

namespace App\Services;

use App\Models\WorkerSchedule;

class ScheduleService
{
    public function getSchedules($companyId, $farmId = null)
    {
        $query = WorkerSchedule::query()
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->byFarm($farmId);
        }

        return $query->with(["farm", "worker", "createdBy"])->get();
    }

    public function createSchedule($userId, array $data)
    {
        $data["created_by"] = $userId;
        return WorkerSchedule::create($data);
    }

    public function updateSchedule(WorkerSchedule $schedule, array $data)
    {
        $schedule->update($data);
        return $schedule;
    }

    public function deleteSchedule(WorkerSchedule $schedule)
    {
        return $schedule->delete();
    }

    public function getSchedulesByDate($farmId, $date)
    {
        return WorkerSchedule::where("farm_id", $farmId)
            ->where("work_date", $date)
            ->with(["worker", "farm"])
            ->get();
    }
}
