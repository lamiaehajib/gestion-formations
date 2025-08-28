<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'title',
        'description',
        'course_date',
        'start_time',
        'end_time',
        'zoom_link',
        'consultant_id', 
        'recording_url',
        'documents',
        
    ];

    protected $casts = [
        'course_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'documents' => 'array',
        
    ];

    // Relations
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function reschedules()
    {
        return $this->hasMany(CourseReschedule::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function consultant() // Define the new relationship
    {
        return $this->belongsTo(User::class, 'consultant_id'); // 'consultant_id' is the foreign key
    }
}
