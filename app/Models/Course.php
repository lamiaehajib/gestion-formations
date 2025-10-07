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
    ];

     protected $casts = [
        'course_date' => 'date',
        // 🔥 CORRECTION: Ma khas-ch n-castéw TIME l DATETIME!
        // Khassna nkhalliwha string bach n-akhdhowha kima hiya mn DB
        // 'start_time' => 'datetime:H:i',  // ❌ Hadi kant l-mochkila
        // 'end_time' => 'datetime:H:i',    // ❌ Hadi kant l-mochkila
        'documents' => 'array',
        'last_notification_time' => 'datetime',
    ];




    public function usersJoined()
{
    // 'course_joins' hiya smit l'table dyal pivot li khassk tcreer
    return $this->belongsToMany(User::class, 'course_joins', 'course_id', 'user_id')
                ->withTimestamps(); // Bach n3erfo imta dar l'join
}


    public function module()
    {
        return $this->belongsTo(Module::class);
    }
  public function formation() 
    { 
       
        return $this->belongsTo(Formation::class); 
    } 

    // Relations
   

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
