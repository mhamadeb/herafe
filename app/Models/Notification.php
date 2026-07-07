<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'order_id',
        'title',
        'body',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // العلاقات

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // تحديد الإشعار كمقروء
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    // نطاق الإشعارات غير المقروءة
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}