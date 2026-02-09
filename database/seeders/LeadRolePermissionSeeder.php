<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class LeadRolePermissionSeeder extends Seeder
{
    public function run()
    {
        $role = Role::where('slug', 'super-admin')->first();

        if (!$role) {
            return;
        }

        $permissions = Permission::where('slug', 'like', 'lead-%')->pluck('id');

        $role->permissions()->syncWithoutDetaching($permissions);
    }
}
