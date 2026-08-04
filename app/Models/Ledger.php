<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'ledger_group_id',
        'opening_balance',
        'balance_type',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the ledger group this ledger belongs to.
     */
    public function group()
    {
        return $this->belongsTo(LedgerGroup::class, 'ledger_group_id');
    }

    /**
     * Get all voucher entries for this ledger.
     */
    public function entries()
    {
        return $this->hasMany(VoucherEntry::class);
    }

    /**
     * Get the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calculate current balance of the ledger.
     */
    public function getCurrentBalance()
    {
        $totalDebit = $this->entries()->sum('debit_amount');
        $totalCredit = $this->entries()->sum('credit_amount');

        $currentBalance = $this->opening_balance;

        if ($this->balance_type === 'Debit') {
            $currentBalance += $totalDebit - $totalCredit;
        } else {
            $currentBalance += $totalCredit - $totalDebit;
        }

        return $currentBalance;
    }

    /**
     * Scope to get only active ledgers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to get only inactive ledgers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }
}
