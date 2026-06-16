<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = ['customer_id', 'date', 'type', 'reference_no', 'debit', 'credit', 'remarks'];
}