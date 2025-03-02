<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'type' => 1,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'type' => 2,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Operator',
                'email' => 'operator@example.com',
                'type' => 3,
                'password' => bcrypt('1234'),
            ],
        ];

        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}
