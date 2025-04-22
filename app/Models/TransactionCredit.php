<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCredit extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'transaction_credit';

    protected $fillable = [
        'msisdn',
        'units_amount_to_pay',
        'status',
        'credit_type',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'units_amount_to_pay' => 'float'
    ];
} 