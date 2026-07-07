<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $titles = [
            'تم قبول طلبك',
            'تم إلغاء الطلب',
            'تم تعديل موعد الطلب',
            'طلب جديد',
            'تذكير بموعد الخدمة',
            'تم إكمال الخدمة بنجاح',
        ];

        $bodies = [
            'تم قبول طلب الخدمة الخاص بك وسيتم التواصل معك قريباً',
            'تم إلغاء الطلب بناءً على طلبك',
            'تم تعديل موعد الخدمة يرجى مراجعة التفاصيل',
            'لديك طلب جديد يرجى الدخول للتطبيق لمراجعته',
            'موعد الخدمة بعد غد يرجى التأكيد',
            'تم إكمال الخدمة بنجاح نرجو تقييم الخدمة',
        ];

        $index = $this->faker->numberBetween(0, count($titles) - 1);

        return [
            'user_id' => User::factory(),
            'order_id' => $this->faker->optional(0.7)->randomElement([null, Order::factory()]),
            'title' => $titles[$index],
            'body' => $bodies[$index],
            'is_read' => $this->faker->boolean(30),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}