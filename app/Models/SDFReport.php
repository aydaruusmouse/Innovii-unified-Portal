<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SDFReport extends Model
{
    protected $connection = 'sdf_live';
    protected $table = 'reports'; // Replace with the actual table name
    protected $fillable = [
        'date', 'name', 'total_subs', 'active', 'failed', 'new', 'canceled',
    ];
}

