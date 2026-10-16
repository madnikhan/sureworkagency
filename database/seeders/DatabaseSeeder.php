<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            ServiceCatalogueSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call(LocalOwnerSeeder::class);
        }
    }
}
