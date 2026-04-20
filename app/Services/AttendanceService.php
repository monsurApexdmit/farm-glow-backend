<?php

namespace App\Services;

use App\Models\WorkerAttendance;
use Carbon\Carbon;

class AttendanceService
{
    public function getAttendanceRecords($workerId = null)
    {
        $query = WorkerAttendance::query();

        if ($workerId) {
            $query->byWorker($workerId);
        }

        return $query->with(["worker", "createdBy"])->get();
    }

    public function recordAttendance($userId, array $data)
    {
        $attendanceData = [
            "worker_id" => $data["worker_id"],
            "attendance_date" => $data["attendance_date"],
            "check_in_time" => $data["check_in_time"],
            "check_out_time" => $data["check_out_time"] ?? null,
            "status" => $data["status"],
            "notes" => $data["notes"] ?? null,
            "created_by" => $userId,
        ];

        if (($data["check_in_time"] ?? null) && ($data["check_out_time"] ?? null)) {
            $attendanceData["hours_worked"] = $this->calculateHoursWorked(
                $data["check_in_time"],
                $data["check_out_time"]
            );
        }

        return WorkerAttendance::create($attendanceData);
    }

    public function getMonthlyAttendance($workerId, $year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return WorkerAttendance::where("worker_id", $workerId)
            ->whereBetween("attendance_date", [$startDate, $endDate])
            ->with(["worker"])
            ->get();
    }

    public function getAttendancePercentage($workerId, $year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $workingDays = $endDate->diffInDaysFiltered(
            function (Carbon $date) {
                return $date->isWeekday();
            },
            $startDate
        );

        $presentDays = WorkerAttendance::where("worker_id", $workerId)
            ->whereBetween("attendance_date", [$startDate, $endDate])
            ->where("status", "present")
            ->count();

        return $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;
    }

    private function calculateHoursWorked($checkIn, $checkOut)
    {
        $in = new \DateTime($checkIn);
        $out = new \DateTime($checkOut);
        return $in->diff($out)->h + ($in->diff($out)->i / 60);
    }
}
