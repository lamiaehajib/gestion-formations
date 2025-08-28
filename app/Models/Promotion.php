<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'name',
        'year',
        'description',
        'formation_id',
    ];

    // Relations
    // 1. One Promotion belongs to one Formation
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    // 2. One Promotion has many Users (students)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}