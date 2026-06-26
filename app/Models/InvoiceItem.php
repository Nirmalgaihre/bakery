<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    // ... existing code ...

    /**
     * Get the invoice that owns the item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function product()
    {
        // Adjust 'product_id' if your foreign key is named differently
        return $this->belongsTo(Product::class, 'product_id');
    }
}