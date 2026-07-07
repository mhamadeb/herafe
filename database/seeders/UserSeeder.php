<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProfessionalDetail;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // التحقق من وجود المستخدمين قبل إضافتهم
        $users = [
            [
                'full_name' => 'أحمد محمد',
                'email' => 'ahmed@example.com',
                'phone' => '0500000001',
                'type' => 'professional',
            ],
            [
                'full_name' => 'سارة علي',
                'email' => 'sara@example.com',
                'phone' => '0500000002',
                'type' => 'customer',
            ],
            [
                'full_name' => 'محمد إبراهيم',
                'email' => 'mohammed@example.com',
                'phone' => '0500000003',
                'type' => 'professional',
            ],
            [
                'full_name' => 'نورة عبدالله',
                'email' => 'noura@example.com',
                'phone' => '0500000004',
                'type' => 'customer',
            ],
            [
                'full_name' => 'خالد سعيد',
                'email' => 'khalid@example.com',
                'phone' => '0500000005',
                'type' => 'professional',
            ],
        ];

        foreach ($users as $userData) {
            // استخدام firstOrCreate لتجنب التكرار
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password123'),
                    'profile_image' => null,
                    'joined_at' => now(),
                ])
            );

            // إضافة تفاصيل إضافية إذا كان حرفي (تجنب التكرار)
            if ($user->type === 'professional' && !$user->professionalDetail) {
                ProfessionalDetail::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'biography' => 'حرفي محترف بخبرة تزيد عن 10 سنوات في هذا المجال',
                        'years_of_experience' => rand(5, 15),
                        'is_available' => true,
                    ]
                );
            }

            // إضافة موقعين لكل مستخدم (تجنب التكرار)
            if ($user->locations()->count() === 0) {
                Location::create([
                    'user_id' => $user->id,
                    'city' => 'الرياض',
                    'address' => 'حي الملقا، شارع الأمير محمد بن سلمان',
                    'latitude' => 24.774265,
                    'longitude' => 46.738586,
                ]);

                Location::create([
                    'user_id' => $user->id,
                    'city' => 'الرياض',
                    'address' => 'حي النخيل، شارع التخصصي',
                    'latitude' => 24.754265,
                    'longitude' => 46.718586,
                ]);
            }
        }

        // إنشاء 30 مستخدم عشوائي فقط (قلل العدد لتجنب المشاكل)
        User::factory(30)
            ->create()
            ->each(function ($user) {
                if ($user->type === 'professional' && !$user->professionalDetail) {
                    ProfessionalDetail::factory()->create(['user_id' => $user->id]);
                }
                
                if ($user->locations()->count() === 0) {
                    Location::factory(rand(1, 2))->create(['user_id' => $user->id]);
                }
            });
    }
}