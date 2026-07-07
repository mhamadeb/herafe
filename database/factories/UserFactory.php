<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'password' => Hash::make('password123'),
            'profile_image' => $this->faker->optional(0.7)->imageUrl(200, 200, 'people', true, 'Profile'),
            'type' => $this->faker->randomElement(['professional', 'customer']),
            'joined_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // حالة خاصة: مستخدم حرفي
    public function professional()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'professional',
            ];
        });
    }

    // حالة خاصة: مستخدم عميل
    public function customer()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'customer',
            ];
        });
    }
}