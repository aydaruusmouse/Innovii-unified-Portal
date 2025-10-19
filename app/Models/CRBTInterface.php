<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRBTInterface extends Model
{
    use HasFactory;

    protected $connection = 'crbt';
    protected $table = 'INTERFACE_WISE_SUB_UNSUB_MIS';
    
    protected $fillable = [
        'interface_name',
        'subscriptions',
        'unsubscriptions',
        'tone_usage',
        'date'
    ];

    protected $casts = [
        'subscriptions' => 'integer',
        'unsubscriptions' => 'integer',
        'tone_usage' => 'integer',
        'date' => 'date'
    ];

    public $timestamps = false;

    public static function getInterfaceSubUnsubData($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        
        return $query->select('interface_name', 
                            DB::raw('SUM(subscriptions) as total_subscriptions'),
                            DB::raw('SUM(unsubscriptions) as total_unsubscriptions'))
                    ->groupBy('interface_name')
                    ->orderBy('total_subscriptions', 'desc')
                    ->get();
    }

    public static function getInterfaceToneUsage($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        
        return $query->select('interface_name', 
                            DB::raw('SUM(tone_usage) as total_tone_usage'))
                    ->groupBy('interface_name')
                    ->orderBy('total_tone_usage', 'desc')
                    ->get();
    }
}
