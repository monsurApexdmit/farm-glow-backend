<?php

namespace App\Services;

use App\Models\Worker;
use App\Models\WorkerAttendance;
use App\Models\WorkerPerformance;
use App\Models\WorkerPayroll;

class WorkerService
{
    public function getWorkers($companyId, $farmId = null)
    {
        $query = Worker::query()
            ->active()
            ->whereHas("farm", function ($q) use ($companyId) {
                $q->where("company_id", $companyId);
            });

        if ($farmId) {
            $query->byFarm($farmId);
        }

        return $query->with(["farm", "createdBy"])->get();
    }

    public function createWorker($userId, array $data)
    {
        $data["created_by"] = $userId;
        return Worker::create($data);
    }

    public function updateWorker(Worker $worker, array $data)
    {
        $worker->update($data);
        return $worker;
    }

    public function deleteWorker(Worker $worker)
    {
        $worker->update(["deleted_by" => auth()->id()]);
        return $worker->delete();
    }

    public function getAttendanceHistory(Worker $worker)
    {
        return $worker->attendances()->latest()->get();
    }

    public function getPerformanceHistory(Worker $worker)
    {
        return $worker->performances()->latest()->get();
    }

    public function getPayrollHistory(Worker $worker)
    {
        return $worker->payrolls()->latest("year", "month")->get();
    }
}
