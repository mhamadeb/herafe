<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $cities = ['الرياض', 'جدة', 'مكة', 'المدينة', 'الدمام', 'الخبر', 'تبوك', 'أبها'];
        
        return [
            'user_id' => User::factory(),
            'city' => $this->faker->randomElement($cities),
            'address' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(21.3, 31.0),
            'longitude' => $this->faker->longitude(35.0, 55.0),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}