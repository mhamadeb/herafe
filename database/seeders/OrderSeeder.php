<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\Location;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('type', 'customer')->get();
        $professionals = User::where('type', 'professional')->get();
        $services = Service::all();
        $locations = Location::all();

        // إنشاء 100 طلب عشوائي
        foreach (range(1, 100) as $i) {
            $professional = $professionals->random();
            $service = $professional->services->random() ?? $services->random();
            
            Order::create([
                'customer_id' => $customers->random()->id,
                'professional_id' => $professional->id,
                'service_id' => $service->id,
                'location_id' => $locations->random()->id,
                'order_date' => fake()->dateTimeBetween('-2 months', 'now'),
                'status' => fake()->randomElement(['pending', 'cancelled', 'delayed', 'completed', 'in_progress']),
                'appointment_date' => fake()->dateTimeBetween('now', '+1 month'),
                'appointment_time' => fake()->time('H:i:s'),
                'notes' => fake()->optional(0.6)->sentence(),
                'order_rating' => fake()->optional(0.4)->randomFloat(1, 1, 5),
            ]);
        }

        // إنشاء 20 طلب مكتمل مع تقييمات عالية
        foreach (range(1, 20) as $i) {
            $professional = $professionals->random();
            $service = $professional->services->random() ?? $services->random();
            
            Order::create([
                'customer_id' => $customers->random()->id,
                'professional_id' => $professional->id,
                'service_id' => $service->id,
                'location_id' => $locations->random()->id,
                'order_date' => fake()->dateTimeBetween('-1 month', '-1 day'),
                'status' => 'completed',
                'appointment_date' => fake()->dateTimeBetween('-1 month', '-1 day'),
                'appointment_time' => fake()->time('H:i:s'),
                'notes' => 'شكراً على الخدمة الممتازة',
                'order_rating' => fake()->randomFloat(1, 4, 5), // تقييمات من 4-5
            ]);
        }
    }
}