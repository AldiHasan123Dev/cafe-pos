<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'owner',
                'description' => 'Pemilik cafe',
            ],
            [
                'name' => 'admin',
                'description' => 'Administrator cafe',
            ],
            [
                'name' => 'cashier',
                'description' => 'Kasir cafe',
            ],
            [
                'name' => 'barista',
                'description' => 'Barista cafe',
            ],
            [
                'name' => 'kitchen',
                'description' => 'Staff dapur',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}