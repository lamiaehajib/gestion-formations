<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'formation_id',
        'course_id',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Relations
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}