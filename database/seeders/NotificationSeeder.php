<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // إنشاء 5-15 إشعار لكل مستخدم
            Notification::factory(rand(5, 15))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}