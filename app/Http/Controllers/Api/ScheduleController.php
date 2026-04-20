<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkerSchedule;
use App\Http\Requests\Schedule\StoreScheduleRequest;
use App\Services\ScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $schedules = $this->scheduleService->getSchedules(auth()->user()->company_id, $request->query("farm_id"));
            return response()->json([
                "message" => "Schedules retrieved successfully",
                "data" => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function store(StoreScheduleRequest $request): JsonResponse
    {
        try {
            $schedule = $this->scheduleService->createSchedule(auth()->id(), $request->validated());
            return response()->json([
                "message" => "Schedule created successfully",
                "data" => $schedule,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function show(WorkerSchedule $schedule): JsonResponse
    {
        try {
            if ($schedule->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Schedule not found"], 404);
            }
            return response()->json([
                "message" => "Schedule retrieved successfully",
                "data" => $schedule,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 403);
        }
    }

    public function update(StoreScheduleRequest $request, WorkerSchedule $schedule): JsonResponse
    {
        try {
            if ($schedule->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Schedule not found"], 404);
            }
            $updated = $this->scheduleService->updateSchedule($schedule, $request->validated());
            return response()->json([
                "message" => "Schedule updated successfully",
                "data" => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function destroy(WorkerSchedule $schedule): JsonResponse
    {
        try {
            if ($schedule->farm->company_id !== auth()->user()->company_id) {
                return response()->json(["error" => "Schedule not found"], 404);
            }
            $this->scheduleService->deleteSchedule($schedule);
            return response()->json([
                "message" => "Schedule deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

    public function getByDate(Request $request): JsonResponse
    {
        try {
            $date = $request->query("date");
            $farmId = $request->query("farm_id");
            
            if (!$date || !$farmId) {
                return response()->json(["error" => "Date and farm_id are required"], 400);
            }

            $schedules = $this->scheduleService->getSchedulesByDate($farmId, $date);
            return response()->json([
                "message" => "Daily schedules retrieved successfully",
                "data" => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }
}
