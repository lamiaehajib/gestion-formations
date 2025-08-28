<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'formation_id',
        'course_id',
        'rating',
        'comment',
        'detailed_scores',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'detailed_scores' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}