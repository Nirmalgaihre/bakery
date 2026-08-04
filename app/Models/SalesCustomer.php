<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'customer_code',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'billing_address',
        'shipping_address',
        'gstin',
        'credit_limit',
        'opening_balance',
        'balance_type',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoices()
    {
        return $this->hasMany(SalesInvoice::class, 'customer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getTotalOutstanding()
    {
        return $this->invoices()
            ->where('status', '!=', 'Cancelled')
            ->sum('outstanding_amount');
    }
}
