<?php

namespace App\Http\Controllers;

use App\Models\TransactionCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmergencyCreditController extends Controller
{
    protected $connection = 'mysql2';
    protected $cacheTime = 15; // minutes

    public function daily()
    {
        return view('admin.emergency_credit.daily');
    }

    public function dailyData(Request $request)
    {
        try {
            // Create a more specific cache key
            $cacheKey = 'emergency_credit_daily_' . 
                       ($request->date ?? date('Y-m-d')) . '_' . 
                       ($request->status ?? 'all') . '_' . 
                       md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching daily emergency credit data', ['filters' => $request->all()]);
                
                // Set date range
                $date = $request->date ?? date('Y-m-d');
                $startDate = $date . ' 00:00:00';
                $endDate = $date . ' 23:59:59';

                // Build base query with date filter
                $baseQuery = TransactionCredit::query()
                    ->whereBetween('created_at', [$startDate, $endDate]);

                // Apply status filter if provided
                if ($request->has('status') && $request->status !== '') {
                    $baseQuery->where('status', $request->status);
                }

                // Get daily stats in a single query
                $dailyStats = $baseQuery->select([
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('COALESCE(SUM(units_amount_to_pay), 0) as total_units'),
                    DB::raw('ROUND(AVG(units_amount_to_pay), 2) as avg_units')
                ])->first();

                // Get top users in a separate optimized query
                $topUsers = TransactionCredit::select([
                    'msisdn',
                    DB::raw('COUNT(*) as txn_count')
                ])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($request->has('status') && $request->status !== '', function($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->groupBy('msisdn')
                ->orderBy('txn_count', 'desc')
                ->limit(10)
                ->get();

                // Handle case where no data is found
                if (!$dailyStats) {
                    $dailyStats = [
                        'unique_users' => 0,
                        'total_transactions' => 0,
                        'total_units' => 0,
                        'avg_units' => 0
                    ];
                }

                // Convert any null values to 0
                $dailyStats = collect($dailyStats)->map(function ($value) {
                    return $value ?? 0;
                });

                Log::info('Query execution time', [
                    'date' => $date,
                    'status' => $request->status,
                    'unique_users' => $dailyStats['unique_users'],
                    'total_transactions' => $dailyStats['total_transactions']
                ]);

                return response()->json([
                    'dailyStats' => $dailyStats,
                    'topUsers' => $topUsers
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in dailyData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'An error occurred while fetching daily data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function topUsers()
    {
        return view('admin.emergency_credit.top_users');
    }

    public function topUsersData(Request $request)
    {
        try {
            $cacheKey = 'emergency_credit_top_users_' . md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching top users data', ['filters' => $request->all()]);
                
                // Set date range with proper time components
                $startDate = ($request->start_date ?? date('Y-m-d', strtotime('-30 days'))) . ' 00:00:00';
                $endDate = ($request->end_date ?? date('Y-m-d')) . ' 23:59:59';

                Log::info('Date range', [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);

                // Build optimized query
                $query = TransactionCredit::select([
                    'msisdn',
                    DB::raw('COUNT(*) as txn_count'),
                    DB::raw('COALESCE(SUM(units_amount_to_pay), 0) as total_amount'),
                    DB::raw('MAX(created_at) as last_transaction')
                ])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('msisdn')
                ->orderBy('txn_count', 'desc');

                // Apply status filter if provided
                if ($request->has('status') && $request->status !== '') {
                    $query->where('status', $request->status);
                }

                // Get paginated results
                $results = $query->paginate(20);

                Log::info('Query result', [
                    'total' => $results->total(),
                    'current_page' => $results->currentPage(),
                    'per_page' => $results->perPage()
                ]);

                return $results;
            });

        } catch (\Exception $e) {
            Log::error('Error in topUsersData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching top users data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function weekly()
    {
        return view('admin.emergency_credit.weekly');
    }

    public function weeklyData(Request $request)
    {
        try {
            $cacheKey = 'emergency_credit_weekly_' . md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching weekly emergency credit data', [
                    'filters' => $request->all(),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
                
                $query = TransactionCredit::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                        DB::raw('COUNT(*) as total_transactions'),
                        DB::raw('SUM(units_amount_to_pay) as total_units')
                    );

                // Apply date range filter
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = $request->start_date . ' 00:00:00';
                    $endDate = $request->end_date . ' 23:59:59';
                    
                    Log::info('Applying date range filter', [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]);
                    
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } else {
                    $query->where('created_at', '>=', DB::raw('CURDATE() - INTERVAL 7 DAY'));
                }

                // Apply status filter
                if ($request->has('status') && $request->status !== '') {
                    $query->where('status', $request->status);
                }

                $weeklyStats = $query->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();

                Log::info('Query result', [
                    'count' => $weeklyStats->count(),
                    'first_record' => $weeklyStats->first(),
                    'last_record' => $weeklyStats->last()
                ]);

                return response()->json([
                    'weeklyStats' => $weeklyStats
                ]);

            });

        } catch (\Exception $e) {
            Log::error('Error in weeklyData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching weekly data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function monthly()
    {
        return view('admin.emergency_credit.monthly');
    }

    public function monthlyData(Request $request)
    {
        try {
            // Create a more specific cache key
            $cacheKey = 'emergency_credit_monthly_' . 
                       ($request->start_month ?? date('Y-m', strtotime('-1 month'))) . '_' . 
                       ($request->end_month ?? date('Y-m')) . '_' . 
                       ($request->status ?? 'all') . '_' . 
                       md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching monthly emergency credit data', ['filters' => $request->all()]);
                
                // Optimize the main query by using a single query for all metrics
                $query = TransactionCredit::select([
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(units_amount_to_pay) as total_units'),
                    DB::raw('ROUND((SUM(CASE WHEN status = "SUCCESS" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as success_rate')
                ]);

                // Apply month range filter with default to last month
                if ($request->has('start_month') && $request->has('end_month')) {
                    $startDate = $request->start_month . '-01';
                    $endDate = date('Y-m-t', strtotime($request->end_month . '-01'));
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } else {
                    // Default to last month if no dates provided
                    $lastMonth = date('Y-m', strtotime('-1 month'));
                    $startDate = $lastMonth . '-01';
                    $endDate = date('Y-m-t', strtotime($lastMonth . '-01'));
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }

                // Apply status filter
                if ($request->has('status') && $request->status !== '') {
                    $query->where('status', $request->status);
                }

                $monthlyStats = $query->groupBy('month')
                    ->orderBy('month', 'desc')
                    ->get();

                Log::info('Query result', [
                    'count' => $monthlyStats->count(),
                    'first_record' => $monthlyStats->first(),
                    'last_record' => $monthlyStats->last()
                ]);

                return response()->json([
                    'monthlyStats' => $monthlyStats
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in monthlyData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching monthly data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function status()
    {
        // Get unique statuses from the database with caching
        $cacheKey = 'emergency_credit_statuses';
        $statuses = Cache::remember($cacheKey, now()->addHours(24), function () {
            return TransactionCredit::select('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status')
                ->filter()
                ->values();
        });

        return view('admin.emergency_credit.status', compact('statuses'));
    }

    public function statusData(Request $request)
    {
        try {
            // Set default dates
            $defaultStartDate = date('Y-m-d', strtotime('-30 days'));
            $defaultEndDate = date('Y-m-d');

            // Create a more specific cache key
            $cacheKey = 'emergency_credit_status_' . 
                       ($request->start_date ?? $defaultStartDate) . '_' . 
                       ($request->end_date ?? $defaultEndDate) . '_' . 
                       ($request->status ?? 'all') . '_' . 
                       ($request->credit_type ?? 'all') . '_' . 
                       md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request, $defaultStartDate, $defaultEndDate) {
                try {
                    Log::info('Fetching status data', [
                        'filters' => $request->all(),
                        'start_date' => $request->start_date ?? $defaultStartDate,
                        'end_date' => $request->end_date ?? $defaultEndDate,
                        'status' => $request->status,
                        'credit_type' => $request->credit_type
                    ]);
                    
                    // Set date range
                    $startDate = ($request->start_date ?? $defaultStartDate) . ' 00:00:00';
                    $endDate = ($request->end_date ?? $defaultEndDate) . ' 23:59:59';

                    // Build base query for total count
                    $baseQuery = TransactionCredit::query()
                        ->whereBetween('created_at', [$startDate, $endDate]);

                    // Apply credit type filter to base query
                    if ($request->has('credit_type') && $request->credit_type !== '') {
                        $baseQuery->where('credit_type', $request->credit_type);
                    }

                    // Get total count for percentage calculation
                    $totalCount = $baseQuery->count();

                    // If no records found, return empty result
                    if ($totalCount === 0) {
                        $message = 'No records found';
                        if ($request->has('credit_type') && $request->credit_type !== '') {
                            $message .= ' for the selected credit type';
                        }
                        $message .= ' in the selected period';

                        Log::info($message, [
                            'credit_type' => $request->credit_type,
                            'start_date' => $startDate,
                            'end_date' => $endDate
                        ]);

                        return response()->json([
                            'statusStats' => [],
                            'message' => $message
                        ]);
                    }

                    // Main query for status statistics
                    $query = clone $baseQuery;
                    $query->select([
                        'status',
                        DB::raw('COUNT(*) as count'),
                        DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                        DB::raw('COALESCE(SUM(units_amount_to_pay), 0) as total_units'),
                        DB::raw("CASE WHEN {$totalCount} > 0 THEN ROUND((COUNT(*) * 100.0 / {$totalCount}), 2) ELSE 0 END as percentage")
                    ]);

                    // Apply status filter
                    if ($request->has('status') && $request->status !== '') {
                        $query->where('status', $request->status);
                    }

                    $statusStats = $query->groupBy('status')
                        ->orderBy('count', 'desc')
                        ->get();

                    // If no records found for the specific status, return empty result
                    if ($statusStats->isEmpty()) {
                        $message = 'No records found';
                        if ($request->has('status') && $request->status !== '') {
                            $message .= ' for the selected status';
                        }
                        if ($request->has('credit_type') && $request->credit_type !== '') {
                            $message .= ' and credit type';
                        }
                        $message .= ' in the selected period';

                        Log::info($message, [
                            'status' => $request->status,
                            'credit_type' => $request->credit_type,
                            'start_date' => $startDate,
                            'end_date' => $endDate
                        ]);

                        return response()->json([
                            'statusStats' => [],
                            'message' => $message
                        ]);
                    }

                    // Format the results
                    $statusStats = $statusStats->map(function ($stat) {
                        return [
                            'status' => $stat->status,
                            'count' => number_format($stat->count),
                            'unique_users' => number_format($stat->unique_users),
                            'total_units' => $stat->total_units > 0 ? number_format($stat->total_units) : '0',
                            'percentage' => $stat->percentage > 0 ? number_format($stat->percentage, 2) : '0.00'
                        ];
                    });

                    Log::info('Query result', [
                        'count' => $statusStats->count(),
                        'first_record' => $statusStats->first(),
                        'last_record' => $statusStats->last(),
                        'total_count' => $totalCount
                    ]);

                    return response()->json([
                        'statusStats' => $statusStats
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error in statusData inner function: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    throw $e;
                }
            });

        } catch (\Exception $e) {
            Log::error('Error in statusData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching status data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function creditType()
    {
        return view('admin.emergency_credit.credit_type');
    }

    public function creditTypeData(Request $request)
    {
        try {
            $cacheKey = 'emergency_credit_type_' . md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching credit type data', ['filters' => $request->all()]);
                
                // Format dates properly
                $startDate = $request->start_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date))) : date('Y-m-d', strtotime('-30 days'));
                $endDate = $request->end_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date))) : date('Y-m-d');
                
                // Add time components
                $startDate = $startDate . ' 00:00:00';
                $endDate = $endDate . ' 23:59:59';

                Log::info('Formatted dates', [
                    'original_start' => $request->start_date,
                    'original_end' => $request->end_date,
                    'formatted_start' => $startDate,
                    'formatted_end' => $endDate
                ]);

                // Build base query for total count
                $baseQuery = TransactionCredit::query()
                    ->whereBetween('created_at', [$startDate, $endDate]);

                // Get total count for percentage calculation
                $totalCount = $baseQuery->count();

                // If no records found, return empty result
                if ($totalCount === 0) {
                    return response()->json([
                        'creditTypeStats' => [],
                        'message' => 'No records found in the selected period'
                    ]);
                }

                // Main query for credit type statistics
                $query = clone $baseQuery;
                $query->select([
                    'credit_type',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('COALESCE(SUM(units_amount_to_pay), 0) as total_units'),
                    DB::raw("CASE WHEN {$totalCount} > 0 THEN ROUND((COUNT(*) * 100.0 / {$totalCount}), 2) ELSE 0 END as percentage")
                ]);

                // Apply status filter if provided
                if ($request->has('status') && $request->status !== '') {
                    $query->where('status', $request->status);
                }

                $creditTypeStats = $query->groupBy('credit_type')
                    ->orderBy('count', 'desc')
                    ->get();

                // Format the results
                $creditTypeStats = $creditTypeStats->map(function ($stat) {
                    return [
                        'credit_type' => $stat->credit_type,
                        'count' => number_format($stat->count),
                        'unique_users' => number_format($stat->unique_users),
                        'total_units' => $stat->total_units > 0 ? number_format($stat->total_units) : '0',
                        'percentage' => $stat->percentage > 0 ? number_format($stat->percentage, 2) : '0.00'
                    ];
                });

                Log::info('Query result', [
                    'count' => $creditTypeStats->count(),
                    'first_record' => $creditTypeStats->first(),
                    'last_record' => $creditTypeStats->last(),
                    'total_count' => $totalCount
                ]);

                return response()->json([
                    'creditTypeStats' => $creditTypeStats
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in creditTypeData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching credit type data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function statusWiseService()
    {
        $services = DB::connection('mysql2')
            ->table('subscription_base')
            ->select('name')
            ->distinct()
            ->get();

        return view('admin.status_wise_services', compact('services'));
    }

    public function statusWiseServiceData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $serviceName = $request->input('service_name');
            $status = $request->input('status');

            $query = DB::connection('mysql2')
                ->table('subs_in_out as sio')
                ->join('subscription_base as sb', 'sio.subscription_id', '=', 'sb.id')
                ->select(
                    'sb.name as name',
                    'sio.status',
                    DB::raw('COUNT(*) as total_subs'),
                    DB::raw('COUNT(DISTINCT sio.msisdn) as unique_users'),
                    DB::raw('DATE(sio.created_at) as date')
                )
                ->whereIn('sio.status', ['ACTIVE', 'CANCELED'])
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('sio.created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('sio.created_at', '<=', $endDate);
                })
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('sb.name', $serviceName);
                })
                ->when($status && $status !== 'all', function ($query) use ($status) {
                    return $query->where('sio.status', $status);
                })
                ->groupBy('sb.name', 'sio.status', 'date')
                ->orderBy('date', 'desc');

            $data = $query->get();

            // Calculate status totals
            $statusTotals = [
                'active' => 0,
                'canceled' => 0
            ];

            $dates = [];
            $subscriptionTotals = [];

            foreach ($data as $row) {
                $status = strtolower($row->status);
                if (isset($statusTotals[$status])) {
                    $statusTotals[$status] += $row->total_subs;
                }

                if (!in_array($row->date, $dates)) {
                    $dates[] = $row->date;
                    $subscriptionTotals[] = $row->total_subs;
                }
            }

            // Format the data for the table
            $tableData = $data->map(function ($row) {
                return [
                    'date' => $row->date,
                    'name' => $row->name,
                    'status' => $row->status,
                    'total_subs' => $row->total_subs
                ];
            });

            return response()->json([
                'table_data' => $tableData,
                'status_totals' => $statusTotals,
                'dates' => $dates,
                'subscription_totals' => $subscriptionTotals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Revenue Reports Methods
    public function revenueSummary()
    {
        return view('admin.emergency_credit.revenue_summary');
    }

    public function revenueSummaryData(Request $request)
    {
        try {
            $cacheKey = 'emergency_credit_revenue_summary_' . md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching revenue summary data', ['filters' => $request->all()]);
                
                // Set date range
                $startDate = $request->start_date ?? date('Y-m-01'); // Default to first day of current month
                $endDate = $request->end_date ?? date('Y-m-t'); // Default to last day of current month
                
                $startDateTime = $startDate . ' 00:00:00';
                $endDateTime = $endDate . ' 23:59:59';

                // Query for revenue summary (both minutes and data)
                $query = DB::connection('mysql2')
                    ->table('transaction_credit as c')
                    ->leftJoin(DB::raw('(
                        SELECT 
                            rc.id as credit_id,
                            SUM(r.amount) as repaid_amount
                        FROM transaction_repayment r
                        INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id
                        WHERE r.status = "SUCCESS"
                        GROUP BY rc.id
                    ) as r'), 'c.id', '=', 'r.credit_id')
                    ->select([
                        DB::raw('DATE(c.created_at) as date_label'),
                        DB::raw('ROUND(SUM(c.units_amount_to_pay) / 10000.0, 2) as total_credit'),
                        DB::raw('ROUND(SUM(COALESCE(r.repaid_amount, 0)) / 10000.0, 2) as total_paid'),
                        DB::raw('CASE 
                            WHEN SUM(c.units_amount_to_pay) = 0 THEN 0
                            ELSE ROUND((SUM(COALESCE(r.repaid_amount, 0)) / SUM(c.units_amount_to_pay)) * 100, 2)
                        END as repayment_percentage')
                    ])
                    ->whereBetween('c.created_at', [$startDateTime, $endDateTime])
                    ->whereIn('c.status', ['CREDIT', 'REPAID'])
                    ->groupBy(DB::raw('DATE(c.created_at)'))
                    ->orderBy('date_label');

                $results = $query->get();

                Log::info('Revenue summary query result', [
                    'count' => $results->count(),
                    'start_date' => $startDateTime,
                    'end_date' => $endDateTime
                ]);

                return response()->json([
                    'revenueData' => $results
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in revenueSummaryData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching revenue summary data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function revenueDataOnly()
    {
        return view('admin.emergency_credit.revenue_data_only');
    }

    public function revenueDataOnlyData(Request $request)
    {
        try {
            $cacheKey = 'emergency_credit_revenue_data_only_v2_' . md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching revenue data only', ['filters' => $request->all()]);
                
                // Set date range
                $startDate = $request->start_date ?? date('Y-m-01'); // Default to first day of current month
                $endDate = $request->end_date ?? date('Y-m-t'); // Default to last day of current month
                
                $startDateTime = $startDate . ' 00:00:00';
                $endDateTime = $endDate . ' 23:59:59';

                // First, check what credit types exist in the database
                $creditTypes = DB::connection('mysql2')
                    ->table('transaction_credit')
                    ->select('credit_type')
                    ->distinct()
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->whereIn('status', ['CREDIT', 'REPAID'])
                    ->pluck('credit_type')
                    ->filter()
                    ->values();

                Log::info('Available credit types', ['types' => $creditTypes->toArray(), 'isEmpty' => $creditTypes->isEmpty()]);

                // If no credit types found, return empty result with message
                if ($creditTypes->isEmpty()) {
                    Log::info('No credit types found for the specified date range - returning empty result');
                    return response()->json([
                        'revenueData' => [
                            (object)[
                                'date_label' => 'No Data Available',
                                'total_credit' => '0.00',
                                'total_paid' => '0.00',
                                'repayment_percentage' => '0.00'
                            ]
                        ],
                        'message' => 'No credit transactions found for the specified date range'
                    ]);
                }

                // For now, let's use the same query as revenue summary but add a note that it's for "data only"
                // This will show all credit types until we can determine the correct credit type structure
                $query = DB::connection('mysql2')
                    ->table('transaction_credit as c')
                    ->leftJoin(DB::raw('(
                        SELECT 
                            rc.id as credit_id,
                            SUM(r.amount) as repaid_amount
                        FROM transaction_repayment r
                        INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id
                        WHERE r.status = "SUCCESS"
                        GROUP BY rc.id
                    ) as r'), 'c.id', '=', 'r.credit_id')
                    ->select([
                        DB::raw('DATE(c.created_at) as date_label'),
                        DB::raw('ROUND(SUM(c.units_amount_to_pay) / 10000.0, 2) as total_credit'),
                        DB::raw('ROUND(SUM(COALESCE(r.repaid_amount, 0)) / 10000.0, 2) as total_paid'),
                        DB::raw('CASE 
                            WHEN SUM(c.units_amount_to_pay) = 0 THEN 0
                            ELSE ROUND((SUM(COALESCE(r.repaid_amount, 0)) / SUM(c.units_amount_to_pay)) * 100, 2)
                        END as repayment_percentage')
                    ])
                    ->whereBetween('c.created_at', [$startDateTime, $endDateTime])
                    ->whereIn('c.status', ['CREDIT', 'REPAID'])
                    ->groupBy(DB::raw('DATE(c.created_at)'))
                    ->orderBy(DB::raw('DATE(c.created_at)'));

                // Get daily data
                $dailyData = $query->get();

                // Get grand total
                $grandTotalQuery = DB::connection('mysql2')
                    ->table('transaction_credit as c')
                    ->leftJoin(DB::raw('(
                        SELECT 
                            rc.id as credit_id,
                            SUM(r.amount) as repaid_amount
                        FROM transaction_repayment r
                        INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id
                        WHERE r.status = "SUCCESS"
                        GROUP BY rc.id
                    ) as r'), 'c.id', '=', 'r.credit_id')
                    ->select([
                        DB::raw('"Grand Total" as date_label'),
                        DB::raw('ROUND(SUM(c.units_amount_to_pay) / 10000.0, 2) as total_credit'),
                        DB::raw('ROUND(SUM(COALESCE(r.repaid_amount, 0)) / 10000.0, 2) as total_paid'),
                        DB::raw('CASE 
                            WHEN SUM(c.units_amount_to_pay) = 0 THEN 0
                            ELSE ROUND((SUM(COALESCE(r.repaid_amount, 0)) / SUM(c.units_amount_to_pay)) * 100, 2)
                        END as repayment_percentage')
                    ])
                    ->whereBetween('c.created_at', [$startDateTime, $endDateTime])
                    ->whereIn('c.status', ['CREDIT', 'REPAID']);

                $grandTotal = $grandTotalQuery->first();
                
                // If no data found, create empty grand total
                if (!$grandTotal || $grandTotal->total_credit === null) {
                    $grandTotal = (object)[
                        'date_label' => 'Grand Total',
                        'total_credit' => '0.00',
                        'total_paid' => '0.00',
                        'repayment_percentage' => '0.00'
                    ];
                }

                // Combine results
                $results = $dailyData->concat(collect([$grandTotal]));

                Log::info('Revenue data only query result', [
                    'daily_count' => $dailyData->count(),
                    'start_date' => $startDateTime,
                    'end_date' => $endDateTime,
                    'grand_total' => $grandTotal
                ]);

                return response()->json([
                    'revenueData' => $results
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in revenueDataOnlyData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching revenue data only',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function revenueWithBalance()
    {
        return view('admin.emergency_credit.revenue_with_balance');
    }

    public function revenueWithBalanceData(Request $request)
    {
        try {
            $cacheKey = 'emergency_credit_revenue_with_balance_' . md5(json_encode($request->all()));
            
            return Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () use ($request) {
                Log::info('Fetching revenue with balance data', ['filters' => $request->all()]);
                
                // Set date range
                $startDate = $request->start_date ?? date('Y-m-d'); // Default to today
                $endDate = $request->end_date ?? date('Y-m-d'); // Default to today
                
                $startDateTime = $startDate . ' 00:00:00';
                $endDateTime = $endDate . ' 23:59:59';

                // Query for revenue with balance (both minutes and data)
                $query = DB::connection('mysql2')
                    ->table('transaction_credit as c')
                    ->leftJoin(DB::raw('(
                        SELECT 
                            rc.id as credit_id,
                            SUM(r.amount) as repaid_amount
                        FROM transaction_repayment r
                        INNER JOIN transaction_credit rc ON r.credit_transaction_id = rc.id
                        WHERE r.status = "SUCCESS"
                        GROUP BY rc.id
                    ) as r'), 'c.id', '=', 'r.credit_id')
                    ->select([
                        DB::raw('DATE_FORMAT(c.created_at, "%Y-%m-%d") as date_label'),
                        DB::raw('ROUND(SUM(c.units_amount_to_pay) / 10000.0, 2) as total_credit'),
                        DB::raw('ROUND(SUM(COALESCE(r.repaid_amount, 0)) / 10000.0, 2) as total_paid'),
                        DB::raw('ROUND((SUM(c.units_amount_to_pay) - SUM(COALESCE(r.repaid_amount, 0))) / 10000.0, 2) as balance'),
                        DB::raw('CASE 
                            WHEN SUM(c.units_amount_to_pay) = 0 THEN 0
                            ELSE ROUND((SUM(COALESCE(r.repaid_amount, 0)) / SUM(c.units_amount_to_pay)) * 100, 2)
                        END as repayment_percentage')
                    ])
                    ->whereBetween('c.created_at', [$startDateTime, $endDateTime])
                    ->whereIn('c.status', ['CREDIT', 'REPAID'])
                    ->groupBy(DB::raw('DATE_FORMAT(c.created_at, "%Y-%m-%d")'))
                    ->orderBy(DB::raw('DATE_FORMAT(c.created_at, "%Y-%m-%d")'));

                $results = $query->get();

                Log::info('Revenue with balance query result', [
                    'count' => $results->count(),
                    'start_date' => $startDateTime,
                    'end_date' => $endDateTime
                ]);

                return response()->json([
                    'revenueData' => $results
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in revenueWithBalanceData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'An error occurred while fetching revenue with balance data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 