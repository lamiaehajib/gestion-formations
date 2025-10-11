<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationMessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_message_id',
        'user_id',
        'inscription_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // العلاقات
    
    public function message()
    {
        return $this->belongsTo(FormationMessage::class, 'formation_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    /**
     * تعليم الرسالة كمقروءة
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}