<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // الخدمات الأساسية
        $services = [
            ['name' => 'سباكة', 'type' => 'صيانة', 'color' => '#3490dc'],
            ['name' => 'كهرباء', 'type' => 'صيانة', 'color' => '#e67e22'],
            ['name' => 'نجارة', 'type' => 'أثاث', 'color' => '#8e44ad'],
            ['name' => 'دهان', 'type' => 'تشطيب', 'color' => '#e74c3c'],
            ['name' => 'تبريد وتكييف', 'type' => 'صيانة', 'color' => '#2ecc71'],
            ['name' => 'حدادة', 'type' => 'أعمال معدنية', 'color' => '#f1c40f'],
            ['name' => 'زراعة', 'type' => 'حدائق', 'color' => '#27ae60'],
            ['name' => 'تنظيف', 'type' => 'خدمات منزلية', 'color' => '#95a5a6'],
            ['name' => 'نقل أثاث', 'type' => 'نقل', 'color' => '#2980b9'],
            ['name' => 'تصميم داخلي', 'type' => 'ديكور', 'color' => '#9b59b6'],
        ];

        foreach ($services as $serviceData) {
            Service::firstOrCreate(
                ['name' => $serviceData['name']], // تجنب التكرار باستخدام name
                $serviceData
            );
        }

        // الحصول على جميع الخدمات الموجودة
        $allServices = Service::all();
        
        // ربط الحرفيين بالخدمات
        $professionals = User::where('type', 'professional')->get();
        
        foreach ($professionals as $professional) {
            // كل حرفي يقدم 2-4 خدمات عشوائية
            $servicesForProfessional = $allServices->random(min(rand(2, 4), $allServices->count()));
            $professional->services()->syncWithoutDetaching($servicesForProfessional->pluck('id'));
        }
    }
}