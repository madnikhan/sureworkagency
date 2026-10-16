<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $keys = [
            'leads.view', 'leads.manage',
            'customers.view', 'customers.manage',
            'projects.view', 'projects.manage', 'projects.assigned',
            'quotes.manage',
            'invoices.view', 'invoices.manage', 'payments.manage',
            'appointments.manage',
            'marketing.manage', 'insights.publish',
            'tickets.view', 'tickets.manage', 'tickets.assigned',
            'services.manage', 'files.manage',
            'users.manage', 'settings.manage', 'settings.billing',
            'audit.view', 'portal.access',
        ];

        foreach ($keys as $key) {
            Permission::query()->firstOrCreate(['key' => $key], ['name' => str_replace('.', ' ', $key)]);
        }

        $map = [
            'owner' => $keys,
            'administrator' => array_values(array_filter($keys, fn ($key) => $key !== 'settings.billing')),
            'sales' => ['leads.view', 'leads.manage', 'customers.view', 'customers.manage', 'quotes.manage', 'appointments.manage'],
            'marketing' => ['marketing.manage', 'insights.publish'],
            'developer' => ['projects.assigned', 'tickets.assigned'],
            'finance' => ['invoices.view', 'invoices.manage', 'payments.manage', 'audit.view'],
            'client' => ['portal.access'],
        ];

        foreach ($map as $roleKey => $permissionKeys) {
            $role = Role::query()->firstOrCreate(['key' => $roleKey], ['name' => ucfirst($roleKey)]);
            $role->permissions()->sync(
                Permission::query()->whereIn('key', $permissionKeys)->pluck('id'),
            );
        }
    }
}
