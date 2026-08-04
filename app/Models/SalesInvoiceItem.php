<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'item_name',
        'item_code',
        'quantity',
        'unit',
        'rate',
        'line_amount',
        'discount',
        'net_amount',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'rate' => 'decimal:2',
        'line_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id');
    }

    public function item()
    {
        return $this->belongsTo(SalesItem::class, 'item_id');
    }
}
