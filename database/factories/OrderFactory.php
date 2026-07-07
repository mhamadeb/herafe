<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $statuses = ['pending', 'cancelled', 'delayed', 'completed', 'in_progress'];
        $status = $this->faker->randomElement($statuses);
        
        // تجنب المشاكل عن طريق الحصول على IDs موجودة فعلاً
        $customer = User::where('type', 'customer')->inRandomOrder()->first();
        $professional = User::where('type', 'professional')->inRandomOrder()->first();
        $service = Service::inRandomOrder()->first();
        $location = Location::inRandomOrder()->first();
        
        // إذا لم تكن هناك بيانات كافية، استخدم factory مؤقتة
        if (!$customer) $customer = User::factory()->customer()->create();
        if (!$professional) $professional = User::factory()->professional()->create();
        if (!$service) $service = Service::factory()->create();
        if (!$location) $location = Location::factory()->create();
        
        return [
            'customer_id' => $customer->id,
            'professional_id' => $professional->id,
            'service_id' => $service->id,
            'location_id' => $location->id,
            'order_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'status' => $status,
            'appointment_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'appointment_time' => $this->faker->time('H:i:s'),
            'notes' => $this->faker->optional(0.5)->sentence(),
            'order_rating' => $status === 'completed' ? $this->faker->optional(0.8)->randomFloat(1, 1, 5) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}