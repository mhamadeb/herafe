<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    // قائمة الخدمات المتاحة
    protected static $services = [
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

    protected static $index = 0;

    public function definition(): array
    {
        // استخدام مؤشر متزايد بدلاً من unique()
        $service = self::$services[self::$index % count(self::$services)];
        self::$index++;

        return [
            'name' => $service['name'],
            'image' => $this->faker->imageUrl(800, 600, 'service', true, $service['name']),
            'type' => $service['type'],
            'color' => $service['color'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}