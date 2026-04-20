<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Worker;
use App\Models\WorkerSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Farm $farm;
    private Worker $worker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::create([
            "name" => "Test Farm Company",
            "email" => "farm@example.com",
        ]);
        $this->user = User::create([
            "company_id" => $this->company->id,
            "email" => "farmer@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "John",
            "last_name" => "Farmer",
        ]);
        $this->farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Test Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);
        $this->worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "John",
            "last_name" => "Worker",
            "email" => "worker@example.com",
            "position" => "Farmworker",
            "employment_type" => "full-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);
    }

    public function test_list_schedules(): void
    {
        WorkerSchedule::create([
            "farm_id" => $this->farm->id,
            "worker_id" => $this->worker->id,
            "work_date" => now(),
            "start_time" => "08:00",
            "end_time" => "17:00",
            "shift_type" => "morning",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/schedules");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_create_schedule(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/schedules", [
                "farm_id" => $this->farm->id,
                "worker_id" => $this->worker->id,
                "work_date" => now()->toDateString(),
                "start_time" => "09:00",
                "end_time" => "17:00",
                "shift_type" => "full-day",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("worker_schedules", [
            "worker_id" => $this->worker->id,
        ]);
    }

    public function test_get_schedule(): void
    {
        $schedule = WorkerSchedule::create([
            "farm_id" => $this->farm->id,
            "worker_id" => $this->worker->id,
            "work_date" => now(),
            "start_time" => "08:00",
            "end_time" => "16:00",
            "shift_type" => "morning",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/schedules/{$schedule->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.id", $schedule->id);
    }

    public function test_update_schedule(): void
    {
        $schedule = WorkerSchedule::create([
            "farm_id" => $this->farm->id,
            "worker_id" => $this->worker->id,
            "work_date" => now(),
            "start_time" => "08:00",
            "end_time" => "16:00",
            "shift_type" => "original",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->patchJson("/api/v1/schedules/{$schedule->id}", [
                "farm_id" => $this->farm->id,
                "worker_id" => $this->worker->id,
                "work_date" => now()->toDateString(),
                "shift_type" => "modified",
                "start_time" => "09:00",
                "end_time" => "17:00",
            ]);

        $response->assertStatus(200);
    }

    public function test_delete_schedule(): void
    {
        $schedule = WorkerSchedule::create([
            "farm_id" => $this->farm->id,
            "worker_id" => $this->worker->id,
            "work_date" => now(),
            "start_time" => "08:00",
            "end_time" => "16:00",
            "shift_type" => "morning",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/schedules/{$schedule->id}");

        $response->assertStatus(200);
    }

    public function test_list_by_farm(): void
    {
        WorkerSchedule::create([
            "farm_id" => $this->farm->id,
            "worker_id" => $this->worker->id,
            "work_date" => now(),
            "start_time" => "08:00",
            "end_time" => "16:00",
            "shift_type" => "morning",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/schedules?farm_id=" . $this->farm->id);

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
}
