<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\RecordAttendanceRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->getAttendanceRecords($request->query("worker_id"));
            return response()->json([
                "message" => "Attendance records retrieved successfully",
                "data" => $attendance,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function record(RecordAttendanceRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->recordAttendance(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Attendance recorded successfully",
                "data" => $attendance,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getMonthly(Request $request): JsonResponse
    {
        try {
            $workerId = $request->query("worker_id");
            $year = $request->query("year");
            $month = $request->query("month");

            if (!$workerId || !$year || !$month) {
                return response()->json(["error" => "worker_id, year, and month are required"], 400);
            }

            $attendance = $this->attendanceService->getMonthlyAttendance($workerId, $year, $month);
            $percentage = $this->attendanceService->getAttendancePercentage($workerId, $year, $month);

            return response()->json([
                "message" => "Monthly attendance retrieved successfully",
                "data" => $attendance,
                "attendance_percentage" => round($percentage, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
