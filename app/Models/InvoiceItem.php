<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'qty',
        'unit',
        'price',
        'total',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}