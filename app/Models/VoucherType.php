<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all vouchers of this type.
     */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    /**
     * Scope to get only active voucher types.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to get only inactive voucher types.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }
}
