<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'voucher_number',
        'voucher_type_id',
        'reference_number',
        'voucher_date',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'posted_date',
        'posted_by',
        'cancelled_date',
        'cancelled_by',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'voucher_date' => 'date',
        'posted_date' => 'datetime',
        'cancelled_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the voucher type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(VoucherType::class, 'voucher_type_id');
    }

    /**
     * Get all entries in this voucher.
     */
    public function entries(): HasMany
    {
        return $this->hasMany(VoucherEntry::class);
    }

    /**
     * Get the user who created this voucher.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this voucher.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who posted this voucher.
     */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Get the user who cancelled this voucher.
     */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if voucher is balanced (debit = credit).
     */
    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    /**
     * Get balance difference.
     */
    public function getBalanceDifference(): float
    {
        return $this->total_debit - $this->total_credit;
    }

    /**
     * Scope to get only draft vouchers.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    /**
     * Scope to get only posted vouchers.
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'Posted');
    }

    /**
     * Scope to get only cancelled vouchers.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }
}
