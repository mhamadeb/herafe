<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalRating extends Model
{
    use HasFactory;

    protected $table = 'professional_ratings';

    protected $fillable = [
        'professional_id',
        'rating',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    // العلاقات

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
