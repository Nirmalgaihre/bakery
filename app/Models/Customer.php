<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import the correct Relationship class
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'pan_number',
        'phone_number',
        'previous_due',
        'address',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'previous_due' => 'decimal:2',
    ];

    /**
     * Get the sales associated with the customer.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class)->orderBy('transaction_date', 'desc');
    }

    /**
     * Get the invoices associated with the customer.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}