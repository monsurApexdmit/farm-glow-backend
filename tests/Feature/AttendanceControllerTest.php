<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Worker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceControllerTest extends TestCase
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

    public function test_record_attendance(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/attendance/record", [
                "worker_id" => $this->worker->id,
                "attendance_date" => now()->toDateString(),
                "check_in_time" => now()->format("H:i:s"),
                "check_out_time" => now()->addHours(8)->format("H:i:s"),
                "status" => "present",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("worker_attendances", [
            "worker_id" => $this->worker->id,
            "status" => "present",
        ]);
    }

    public function test_get_attendance_records(): void
    {
        $this->worker->attendances()->create([
            "attendance_date" => now(),
            "check_in_time" => now()->format("H:i:s"),
            "check_out_time" => now()->addHours(8)->format("H:i:s"),
            "status" => "present",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/attendance?worker_id={$this->worker->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_get_monthly_attendance(): void
    {
        $this->worker->attendances()->create([
            "attendance_date" => now(),
            "check_in_time" => now()->format("H:i:s"),
            "check_out_time" => now()->addHours(8)->format("H:i:s"),
            "status" => "present",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/attendance/monthly?worker_id={$this->worker->id}&year=" . now()->year . "&month=" . now()->month);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data",
            "attendance_percentage",
        ]);
    }

    public function test_record_absent(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/attendance/record", [
                "worker_id" => $this->worker->id,
                "attendance_date" => now()->addDay()->toDateString(),
                "check_in_time" => now()->format("H:i:s"),
                "status" => "absent",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("worker_attendances", [
            "status" => "absent",
        ]);
    }

    public function test_record_late(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/attendance/record", [
                "worker_id" => $this->worker->id,
                "attendance_date" => now()->addDays(2)->toDateString(),
                "check_in_time" => now()->addHours(1)->format("H:i:s"),
                "status" => "late",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("worker_attendances", [
            "status" => "late",
        ]);
    }
}
