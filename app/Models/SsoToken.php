<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SsoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_account_id',
        'token',
        'ip_address',
        'user_agent',
        'expires_at',
        'used_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحساب المستهدف
     */
    public function applicationAccount(): BelongsTo
    {
        return $this->belongsTo(ApplicationAccount::class);
    }

    /**
     * إنشاء token جديد
     */
    public static function generate($userId, $accountId, $expiresInMinutes = 15)
    {
        return self::create([
            'user_id' => $userId,
            'application_account_id' => $accountId,
            'token' => Str::random(64),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    /**
     * التحقق من صلاحية Token
     */
    public function isValid(): bool
    {
        return !$this->is_used 
            && $this->expires_at->isFuture();
    }

    /**
     * تعليم Token كمستخدم
     */
    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now()
        ]);
    }

    /**
     * حذف Tokens منتهية الصلاحية (Cron Job)
     */
    public static function cleanExpired()
    {
        return self::where('expires_at', '<', now())
            ->orWhere('is_used', true)
            ->delete();
    }
}