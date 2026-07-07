<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            OrderSeeder::class,
            NotificationSeeder::class, // أضف هذا السطر
            ProfessionalServiceSeeder::class, // أضف هذا السطر

        ]);
    }
}