<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(["name" => $permission]);
        }

        $ownerPermissions = $permissions;

        $managerPermissions = array_diff($permissions, [
            "manage_payroll",
            "generate_reports",
            "manage_users",
        ]);

        $farmerPermissions = [
            "view_farms",
            "create_crops",
            "edit_crops",
            "log_crop_health",
            "view_livestock",
            "create_livestock",
            "edit_livestock",
            "log_livestock_health",
            "view_workers",
            "view_worker_schedules",
            "record_attendance",
            "view_attendance",
            "review_worker_performance",
            "view_inventory",
            "view_finances",
        ];

        $workerPermissions = [
            "view_farms",
            "view_inventory",
            "view_finances",
        ];

        $ownerRole = Role::firstOrCreate(["name" => "owner"]);
        $ownerRole->syncPermissions($ownerPermissions);

        $managerRole = Role::firstOrCreate(["name" => "manager"]);
        $managerRole->syncPermissions($managerPermissions);

        $farmerRole = Role::firstOrCreate(["name" => "farmer"]);
        $farmerRole->syncPermissions($farmerPermissions);

        $workerRole = Role::firstOrCreate(["name" => "worker"]);
        $workerRole->syncPermissions($workerPermissions);
    }
}
