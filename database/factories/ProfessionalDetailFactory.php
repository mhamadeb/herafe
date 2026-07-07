<?php

namespace Database\Factories;

use App\Models\ProfessionalDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalDetailFactory extends Factory
{
    protected $model = ProfessionalDetail::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->professional(),
            'biography' => $this->faker->paragraphs(3, true),
            'years_of_experience' => $this->faker->numberBetween(0, 30),
            'is_available' => $this->faker->boolean(80), // 80% متاح
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}