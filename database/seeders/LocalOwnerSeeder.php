<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class LocalOwnerSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $role = Role::query()->where('key', 'owner')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'owner@surework.test'],
            [
                'name' => 'Local owner',
                'password' => env('LOCAL_OWNER_PASSWORD', 'local-only-change-me'),
                'role_id' => $role->id,
            ],
        );
    }
}
