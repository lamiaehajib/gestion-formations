<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'course_date',
        'start_time',
        'end_time',
        'zoom_link',
        'consultant_id', 
        'recording_url',
        'documents',
        'module_id',
        'formation_id',
        'last_notification_time', 
        'notification_count',
        'formation_recordings', // ✅ Nouvelle colonne
    ];

    protected $casts = [
        'course_date' => 'date',
        'documents' => 'array',
        'formation_recordings' => 'array', // 🔥 CAST IMPORTANT: JSON -> PHP Array automatically
        'last_notification_time' => 'datetime',
    ];

    public function usersJoined()
    {
        return $this->belongsToMany(User::class, 'course_joins', 'course_id', 'user_id')
                    ->withTimestamps();
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
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
    
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }
}