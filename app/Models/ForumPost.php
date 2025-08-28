<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_id',
        'user_id',
        'parent_id',
        'content',
        'attachments',
        'likes_count',
        'is_pinned',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_pinned' => 'boolean',
    ];

    // Relations
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id');
    }
}