<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReschedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'consultant_id',
        'original_date',
        'new_date',
        'reason',
    ];

    protected $casts = [
        'original_date' => 'datetime',
        'new_date' => 'datetime',
    ];

    // Relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }
}