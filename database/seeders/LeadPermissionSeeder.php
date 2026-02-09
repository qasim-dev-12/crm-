<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;          // ✅ REQUIRED
use Illuminate\Support\Str;         // ✅ REQUIRED

class LeadPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'lead-list',
            'lead-create',
            'lead-view',
            'lead-edit',
            'lead-delete',
            'lead-convert',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission],
                [
                    'name' => Str::title(str_replace('-', ' ', $permission)),

                ]
            );
        }
    }
}
