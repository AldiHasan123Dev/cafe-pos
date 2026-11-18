<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $ownerRole = Role::where('name', 'owner')->firstOrFail();

        User::updateOrCreate(
            ['email' => 'owner@cafepos.test'],
            [
                'role_id' => $ownerRole->id,
                'name' => 'Cafe Owner',
                'phone' => '081234567890',
                'password' => 'ChangeThisPassword123!',
                'is_active' => true,
            ]
        );
    }
}