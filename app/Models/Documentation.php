<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documentation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'documentation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id',
        'consultant_id',
        'file_path',
        'description',
        'files',
        'status',
        'admin_comment',
        'verified_by',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'files' => 'array',
        'verified_at' => 'datetime',
    ];

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope l documentation pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope l documentation approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope l documentation rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check ila documentation approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check ila documentation rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check ila documentation pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Approve documentation
     */
    public function approve($adminId, $comment = null)
    {
        $this->update([
            'status' => 'approved',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_comment' => $comment,
        ]);
    }

    /**
     * Reject documentation
     */
    public function reject($adminId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_comment' => $reason,
        ]);
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Module li kaykhoss had documentation
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Consultant li dar had documentation
     */
    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Admin li dar verification
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}