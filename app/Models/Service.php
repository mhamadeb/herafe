<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'name',
        'image',
        'type',
        'color',
    ];

    // العلاقات

    public function professionals()
    {
        return $this->belongsToMany(User::class, 'professional_service', 'service_id', 'user_id')
                    ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // الحصول على الحرفيين المتاحين لهذه الخدمة
    public function getAvailableProfessionalsAttribute()
    {
        return $this->professionals()
                    ->whereHas('professionalDetail', function($query) {
                        $query->where('is_available', true);
                    })
                    ->get();
    }
}