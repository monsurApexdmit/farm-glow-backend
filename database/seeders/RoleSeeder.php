<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['owner', 'manager', 'worker'];
        
        foreach ($roles as $role) {
            Role::create(['name' => $role, 'guard_name' => 'api']);
        }
    }
}
