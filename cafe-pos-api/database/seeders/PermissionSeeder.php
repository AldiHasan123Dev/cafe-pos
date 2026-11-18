<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',

            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            'categories.manage',
            'orders.view',
            'orders.create',
            'orders.cancel',

            'payments.view',
            'payments.create',
            'payments.refund',

            'inventory.view',
            'inventory.manage',

            'reports.view',

            'users.manage',
            'settings.manage',
            'expenses.manage',
            'shifts.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        $owner = Role::where('name', 'owner')->first();

        if ($owner) {
            $owner->permissions()->sync(
                Permission::pluck('id')->all()
            );
        }

        $admin = Role::where('name', 'admin')->first();

        if ($admin) {
            $adminPermissions = Permission::whereNotIn(
                'name',
                ['settings.manage']
            )->pluck('id')->all();

            $admin->permissions()->sync($adminPermissions);
        }

        $cashier = Role::where('name', 'cashier')->first();

        if ($cashier) {
            $cashier->permissions()->sync(
                Permission::whereIn('name', [
                    'dashboard.view',
                    'products.view',
                    'orders.view',
                    'orders.create',
                    'payments.view',
                    'payments.create',
                    'shifts.manage',
                ])->pluck('id')->all()
            );
        }

        $barista = Role::where('name', 'barista')->first();

        if ($barista) {
            $barista->permissions()->sync(
                Permission::whereIn('name', [
                    'orders.view',
                ])->pluck('id')->all()
            );
        }

        $kitchen = Role::where('name', 'kitchen')->first();

        if ($kitchen) {
            $kitchen->permissions()->sync(
                Permission::whereIn('name', [
                    'orders.view',
                ])->pluck('id')->all()
            );
        }
    }
}