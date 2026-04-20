<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Worker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Farm $farm;

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
    }

    public function test_list_workers(): void
    {
        Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john@example.com",
            "position" => "Farmworker",
            "employment_type" => "full-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/workers");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_create_worker(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/workers", [
                "farm_id" => $this->farm->id,
                "first_name" => "Jane",
                "last_name" => "Smith",
                "email" => "jane@example.com",
                "position" => "Manager",
                "employment_type" => "full-time",
                "hiring_date" => now()->toDateString(),
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("workers", ["email" => "jane@example.com"]);
    }

    public function test_create_worker_missing_required(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/workers", [
                "farm_id" => $this->farm->id,
            ]);

        $response->assertStatus(422);
    }

    public function test_get_worker(): void
    {
        $worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "Tom",
            "last_name" => "Brown",
            "email" => "tom@example.com",
            "position" => "Worker",
            "employment_type" => "part-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/workers/{$worker->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.id", $worker->id);
    }

    public function test_update_worker(): void
    {
        $worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "Original",
            "last_name" => "Name",
            "email" => "original@example.com",
            "position" => "Worker",
            "employment_type" => "full-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/workers/{$worker->id}", [
                "position" => "Senior Worker",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("workers", ["position" => "Senior Worker"]);
    }

    public function test_delete_worker(): void
    {
        $worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "Delete",
            "last_name" => "Me",
            "email" => "delete@example.com",
            "position" => "Worker",
            "employment_type" => "seasonal",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/workers/{$worker->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("workers", ["id" => $worker->id]);
    }

    public function test_get_worker_attendance(): void
    {
        $worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "Attend",
            "last_name" => "Record",
            "email" => "attend@example.com",
            "position" => "Worker",
            "employment_type" => "full-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/workers/{$worker->id}/attendance");

        $response->assertStatus(200);
    }

    public function test_get_worker_performance(): void
    {
        $worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "Perform",
            "last_name" => "Worker",
            "email" => "perform@example.com",
            "position" => "Worker",
            "employment_type" => "full-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/workers/{$worker->id}/performance");

        $response->assertStatus(200);
    }

    public function test_get_worker_payroll(): void
    {
        $worker = Worker::create([
            "farm_id" => $this->farm->id,
            "first_name" => "Pay",
            "last_name" => "Roll",
            "email" => "payroll@example.com",
            "position" => "Worker",
            "employment_type" => "full-time",
            "hiring_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/workers/{$worker->id}/payroll");

        $response->assertStatus(200);
    }
}
