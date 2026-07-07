<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'professional_id',
        'service_id',
        'location_id',
        'order_date',
        'status',
        'appointment_date',
        'appointment_time',
        'notes',
        'order_rating',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i:s',
        'order_rating' => 'decimal:1',
    ];

    // حالات الطلب
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DELAYED = 'delayed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_IN_PROGRESS = 'in_progress';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_CANCELLED => 'ملغية',
            self::STATUS_DELAYED => 'مؤجلة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
        ];
    }

    // العلاقات

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // تحديث التقييم العام للحرفي بعد إضافة تقييم جديد
    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->wasChanged('order_rating') && $order->status === self::STATUS_COMPLETED) {
                // يمكن تحديث جدول professional_ratings هنا
                $averageRating = $order->professional->average_rating;
                
                ProfessionalRating::updateOrCreate(
                    ['professional_id' => $order->professional_id],
                    ['rating' => $averageRating]
                );
            }
        });
    }

    // هل يمكن تقييم الطلب؟
    public function canBeRated()
    {
        return $this->status === self::STATUS_COMPLETED && is_null($this->order_rating);
    }

    // هل يمكن إلغاء الطلب؟
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    // النص العربي للحالة
    public function getStatusTextAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}