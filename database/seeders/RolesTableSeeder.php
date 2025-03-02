<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'id' => 1,
            'nm_roles' => 'Super Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Role::create([
            'id' => 2,
            'nm_roles' => 'Manager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Role::create([
            'id' => 3,
            'nm_roles' => 'Operator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
