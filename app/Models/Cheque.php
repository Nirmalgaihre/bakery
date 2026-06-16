<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $fillable = [
        'cheque_no', 'bank_name', 'party_name', 'amount', 
        'issue_date_ad', 'maturity_date_ad', 
        'issue_date_bs', 'maturity_date_bs', 
        'status', 'remarks', 'email_sent_at'
    ];

    protected $casts = [
        'issue_date_ad' => 'date',
        'maturity_date_ad' => 'date',
        'email_sent_at' => 'datetime',
    ];
}