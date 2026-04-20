<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $owner;
    private User $manager;
    private User $farmer;
    private User $worker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan("db:seed", ["--class" => "PermissionSeeder"]);

        $this->company = Company::create([
            "name" => "Test Company",
            "email" => "test@example.com",
        ]);

        $this->owner = User::create([
            "company_id" => $this->company->id,
            "email" => "owner@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Owner",
            "last_name" => "User",
            "is_active" => true,
        ]);
        $this->owner->assignRole("owner");

        $this->manager = User::create([
            "company_id" => $this->company->id,
            "email" => "manager@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Manager",
            "last_name" => "User",
            "is_active" => true,
        ]);
        $this->manager->assignRole("manager");

        $this->farmer = User::create([
            "company_id" => $this->company->id,
            "email" => "farmer@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Farmer",
            "last_name" => "User",
            "is_active" => true,
        ]);
        $this->farmer->assignRole("farmer");

        $this->worker = User::create([
            "company_id" => $this->company->id,
            "email" => "worker@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Worker",
            "last_name" => "User",
            "is_active" => true,
        ]);
        $this->worker->assignRole("worker");
    }

    public function test_owner_has_all_permissions(): void
    {
        $ownerRole = Role::findByName("owner");
        $totalPermissions = Permission::count();
        
        $this->assertEquals($totalPermissions, $ownerRole->permissions->count());
        $this->assertTrue($this->owner->hasPermissionTo("manage_users"));
        $this->assertTrue($this->owner->hasPermissionTo("generate_reports"));
        $this->assertTrue($this->owner->hasPermissionTo("manage_payroll"));
    }

    public function test_manager_missing_critical_permissions(): void
    {
        $this->assertFalse($this->manager->hasPermissionTo("manage_payroll"));
        $this->assertFalse($this->manager->hasPermissionTo("generate_reports"));
        $this->assertFalse($this->manager->hasPermissionTo("manage_users"));
    }

    public function test_manager_has_operational_permissions(): void
    {
        $this->assertTrue($this->manager->hasPermissionTo("view_farms"));
        $this->assertTrue($this->manager->hasPermissionTo("create_crops"));
        $this->assertTrue($this->manager->hasPermissionTo("manage_inventory"));
        $this->assertTrue($this->manager->hasPermissionTo("record_attendance"));
    }

    public function test_farmer_has_limited_permissions(): void
    {
        $this->assertTrue($this->farmer->hasPermissionTo("view_farms"));
        $this->assertTrue($this->farmer->hasPermissionTo("create_crops"));
        $this->assertTrue($this->farmer->hasPermissionTo("view_livestock"));
        $this->assertTrue($this->farmer->hasPermissionTo("view_inventory"));
        
        $this->assertFalse($this->farmer->hasPermissionTo("manage_users"));
        $this->assertFalse($this->farmer->hasPermissionTo("generate_reports"));
        $this->assertFalse($this->farmer->hasPermissionTo("delete_farms"));
    }

    public function test_worker_has_view_only_permissions(): void
    {
        $this->assertTrue($this->worker->hasPermissionTo("view_farms"));
        $this->assertTrue($this->worker->hasPermissionTo("view_inventory"));
        $this->assertTrue($this->worker->hasPermissionTo("view_finances"));
        
        $this->assertFalse($this->worker->hasPermissionTo("create_crops"));
        $this->assertFalse($this->worker->hasPermissionTo("edit_livestock"));
        $this->assertFalse($this->worker->hasPermissionTo("record_attendance"));
    }

    public function test_manager_has_27_permissions(): void
    {
        $managerRole = Role::findByName("manager");
        $this->assertEquals(27, $managerRole->permissions->count());
    }

    public function test_farmer_role_count(): void
    {
        $farmerRole = Role::findByName("farmer");
        $this->assertEquals(15, $farmerRole->permissions->count());
    }

    public function test_worker_role_count(): void
    {
        $workerRole = Role::findByName("worker");
        $this->assertEquals(3, $workerRole->permissions->count());
    }

    public function test_all_permissions_exist(): void
    {
        $expectedPermissions = [
            "view_farms",
            "create_farms",
            "edit_farms",
            "delete_farms",
            "view_crops",
            "create_crops",
            "edit_crops",
            "delete_crops",
            "log_crop_health",
            "view_livestock",
            "create_livestock",
            "edit_livestock",
            "delete_livestock",
            "log_livestock_health",
            "manage_livestock_breeding",
            "view_workers",
            "manage_workers",
            "view_worker_schedules",
            "manage_worker_schedules",
            "record_attendance",
            "view_attendance",
            "review_worker_performance",
            "view_inventory",
            "manage_inventory",
            "view_finances",
            "manage_finances",
            "manage_payroll",
            "generate_reports",
            "view_users",
            "manage_users",
        ];

        foreach ($expectedPermissions as $permission) {
            $this->assertDatabaseHas("permissions", ["name" => $permission]);
        }
    }

    public function test_assign_role_to_user(): void
    {
        $user = User::create([
            "company_id" => $this->company->id,
            "email" => "newuser@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "New",
            "last_name" => "User",
            "is_active" => true,
        ]);

        $user->assignRole("farmer");
        $this->assertTrue($user->hasRole("farmer"));
        $this->assertTrue($user->hasPermissionTo("view_farms"));
    }

    public function test_sync_roles_on_user(): void
    {
        $user = User::create([
            "company_id" => $this->company->id,
            "email" => "sync@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Sync",
            "last_name" => "User",
            "is_active" => true,
        ]);

        $user->assignRole("farmer");
        $this->assertTrue($user->hasRole("farmer"));

        $user->syncRoles(["manager"]);
        $this->assertFalse($user->hasRole("farmer"));
        $this->assertTrue($user->hasRole("manager"));
    }

    public function test_direct_permission_assignment(): void
    {
        $user = User::create([
            "company_id" => $this->company->id,
            "email" => "direct@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Direct",
            "last_name" => "User",
            "is_active" => true,
        ]);

        $user->givePermissionTo("view_farms");
        $this->assertTrue($user->hasPermissionTo("view_farms"));
    }
}
