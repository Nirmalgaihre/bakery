<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustment extends Model
{
    // Ensure customer_id is inside your fillable property layout array if you use it
    protected $fillable = [
        'product_id',
        'customer_id',
        'quantity',
        'type',
        'unit_cost',
        'reference_note'
    ];

    /**
     * Get the product associated with the adjustment ledger record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer/patient associated with this sales invoice ledger row.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}