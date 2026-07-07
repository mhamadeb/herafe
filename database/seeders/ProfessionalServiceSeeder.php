<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ProfessionalServiceSeeder extends Seeder
{
    public function run(): void
    {
        $professionals = User::where('type', 'professional')->get();
        $services = Service::all();

        foreach ($professionals as $professional) {
            // حذف العلاقات القديمة
            $professional->services()->detach();
            
            // إضافة خدمات جديدة (كل حرفي يقدم 3-5 خدمات)
            $randomServices = $services->random(rand(3, 5));
            $professional->services()->attach($randomServices->pluck('id'));
        }
    }
}