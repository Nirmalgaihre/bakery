<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_type',
        'bill_id',
        'payment_voucher_id',
        'allocated_amount',
        'allocated_date',
        'adjustment_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'allocated_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the payment voucher.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'payment_voucher_id');
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calculate outstanding amount.
     */
    public function getOutstandingAmount(): float
    {
        return $this->allocated_amount - $this->adjustment_amount;
    }

    /**
     * Scope to get pending allocations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope to get partially paid allocations.
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'Partially Paid');
    }

    /**
     * Scope to get paid allocations.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }
}
