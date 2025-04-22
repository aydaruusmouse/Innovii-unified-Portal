<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = [
        'date',
        'grace_hours',
        'name',
        'status',
        'validity',
        'app_id',
        'short_code',
        'message',
        'max_charge_units',
        'renewal_reminder',
        'promotion_end_date',
        'promotion_start_date',
        'multi_offer',
        'multiplier',
        'title',
        'command_id',
        'sub_sms_short_code',
        'driver',
        'billing_cycle',
        'product_id',
    ];

    public $timestamps = false;
}
