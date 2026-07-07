<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalDetail extends Model
{
    use HasFactory;

    protected $table = 'professional_details';

    protected $fillable = [
        'user_id',
        'biography',
        'years_of_experience',
        'is_available',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'is_available' => 'boolean',
    ];

    // العلاقات

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}