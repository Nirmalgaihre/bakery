<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

   protected $fillable = [
        'invoice_no',
        'invoice_number',
        'invoice_date',
        'nepali_date',
        'customer_id',
        'patient_name',
        'patient_address',
        'subtotal',
        'discount',
        'taxable_amount',
        'vat_amount',
        'grand_total',
        'paid_amount',
        'payment_method',
        'status',
        'remarks'
    ];

    protected $casts = [
        'invoice_date'   => 'date',
        'subtotal'       => 'decimal:2',
        'discount'       => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'vat_amount'     => 'decimal:2',
        'grand_total'    => 'decimal:2',
        'paid_amount'    => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}