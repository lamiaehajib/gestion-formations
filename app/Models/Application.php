<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'url',
        'vps_location',
        'icon',
        'description',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * حسابات التطبيق
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(ApplicationAccount::class);
    }

    /**
     * الحسابات النشطة فقط
     */
    public function activeAccounts(): HasMany
    {
        return $this->accounts()->where('is_active', true);
    }

    /**
     * جلب حسابات المستخدم الحالي
     */
    public function userAccounts($userId): HasMany
    {
        return $this->accounts()->where('user_id', $userId);
    }

    /**
     * Scope للتطبيقات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}