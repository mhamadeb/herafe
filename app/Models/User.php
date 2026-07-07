<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'profile_image',
        'type',
        'joined_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // العلاقات

    public function professionalDetail()
    {
        return $this->hasOne(ProfessionalDetail::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'professional_service', 'user_id', 'service_id')
                    ->withTimestamps();
    }

    // الطلبات التي قام بها كعميل
    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    // الطلبات التي يعمل بها كحرفي
    public function ordersAsProfessional()
    {
        return $this->hasMany(Order::class, 'professional_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProfessionalRating::class, 'professional_id');
    }

    // حساب متوسط التقييم من الطلبات المكتملة
    public function getAverageRatingAttribute()
    {
        return $this->ordersAsProfessional()
                    ->where('status', 'completed')
                    ->whereNotNull('order_rating')
                    ->avg('order_rating');
    }

    // حساب عدد التقييمات
    public function getRatingsCountAttribute()
    {
        return $this->ordersAsProfessional()
                    ->where('status', 'completed')
                    ->whereNotNull('order_rating')
                    ->count();
    }

    // هل المستخدم حرفي؟
    public function isProfessional()
    {
        return $this->type === 'professional';
    }

    // هل المستخدم عميل؟
    public function isCustomer()
    {
        return $this->type === 'customer';
    }
}