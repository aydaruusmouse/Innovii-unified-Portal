<?php

namespace App\Http\Controllers;

use App\Models\TransactionCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmergencyCreditController extends Controller
{
    protected $connection = 'mysql2';

    public function daily()
    {
        return view('admin.emergency_credit.daily');
    }

    public function dailyData(Request $request)
    {
        try {
            Log::info('Fetching daily emergency credit data', ['filters' => $request->all()]);
            
            $query = TransactionCredit::select(
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(units_amount_to_pay) as total_units'),
                    DB::raw('AVG(units_amount_to_pay) as avg_units')
                );

            // Apply date filter
            if ($request->has('date')) {
                $query->whereDate('created_at', $request->date);
            } else {
                $query->whereDate('created_at', DB::raw('CURDATE()'));
            }

            // Apply status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $dailyStats = $query->first();

            Log::info('Daily stats query result:', ['stats' => $dailyStats]);

            $topUsersQuery = TransactionCredit::select('msisdn', DB::raw('COUNT(*) as txn_count'))
                ->whereDate('created_at', $request->has('date') ? $request->date : DB::raw('CURDATE()'));

            // Apply status filter to top users
            if ($request->has('status') && $request->status !== '') {
                $topUsersQuery->where('status', $request->status);
            }

            $topUsers = $topUsersQuery->groupBy('msisdn')
                ->orderBy('txn_count', 'desc')
                ->limit(10)
                ->get();

            Log::info('Top users query result:', ['count' => $topUsers->count()]);

            // Handle case where no data is found
            if (!$dailyStats) {
                Log::info('No daily stats found, using default values');
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

            $response = [
                'dailyStats' => $dailyStats,
                'topUsers' => $topUsers
            ];

            Log::info('Sending response:', $response);

            return response()->json($response);

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

    public function topUsersData()
    {
        $topUsers = TransactionCredit::select(
                'msisdn',
                DB::raw('COUNT(*) as txn_count'),
                DB::raw('SUM(units_amount_to_pay) as total_amount'),
                DB::raw('MAX(created_at) as last_transaction')
            )
            ->groupBy('msisdn')
            ->orderBy('txn_count', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'topUsers' => $topUsers
        ]);
    }

    public function weekly()
    {
        return view('admin.emergency_credit.weekly');
    }

    public function weeklyData(Request $request)
    {
        try {
            Log::info('Fetching weekly emergency credit data', ['filters' => $request->all()]);
            
            $query = TransactionCredit::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(units_amount_to_pay) as total_units')
                );

            // Apply date range filter
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
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

            Log::info('Weekly stats query result:', ['count' => $weeklyStats->count()]);

            return response()->json([
                'weeklyStats' => $weeklyStats
            ]);

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
            Log::info('Fetching monthly emergency credit data', ['filters' => $request->all()]);
            
            $query = TransactionCredit::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(units_amount_to_pay) as total_units'),
                    DB::raw('SUM(CASE WHEN status = "SUCCESS" THEN 1 ELSE 0 END) as successful_transactions')
                );

            // Apply month range filter
            if ($request->has('start_month') && $request->has('end_month')) {
                $startDate = $request->start_month . '-01';
                $endDate = date('Y-m-t', strtotime($request->end_month . '-01'));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                $query->where('created_at', '>=', DB::raw('CURDATE() - INTERVAL 12 MONTH'));
            }

            // Apply status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $monthlyStats = $query->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();

            Log::info('Monthly stats query result:', ['count' => $monthlyStats->count()]);

            if ($monthlyStats->isEmpty()) {
                Log::info('No monthly statistics found');
                return response()->json([
                    'monthlyStats' => [],
                    'message' => 'No data available for the selected period'
                ]);
            }

            return response()->json([
                'monthlyStats' => $monthlyStats
            ]);

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
        return view('admin.emergency_credit.status');
    }

    public function statusData(Request $request)
    {
        try {
            Log::info('Fetching status data', ['filters' => $request->all()]);
            
            $query = TransactionCredit::select(
                    'status',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('SUM(units_amount_to_pay) as total_units')
                );

            // Apply date range filter
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            } else {
                $query->where('created_at', '>=', DB::raw('CURDATE() - INTERVAL 30 DAY'));
            }

            // Apply credit type filter
            if ($request->has('credit_type') && $request->credit_type !== '') {
                $query->where('credit_type', $request->credit_type);
            }

            $statusStats = $query->groupBy('status')
                ->get();

            Log::info('Status stats query result:', ['count' => $statusStats->count()]);

            return response()->json([
                'statusStats' => $statusStats
            ]);

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
            Log::info('Fetching credit type data', ['filters' => $request->all()]);
            
            $query = TransactionCredit::select(
                    'credit_type',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COUNT(DISTINCT msisdn) as unique_users'),
                    DB::raw('SUM(units_amount_to_pay) as total_units')
                );

            // Apply date range filter
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            } else {
                $query->where('created_at', '>=', DB::raw('CURDATE() - INTERVAL 30 DAY'));
            }

            // Apply status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            $creditTypeStats = $query->groupBy('credit_type')
                ->get();

            Log::info('Credit type stats query result:', ['count' => $creditTypeStats->count()]);

            return response()->json([
                'creditTypeStats' => $creditTypeStats
            ]);

        } catch (\Exception $e) {
            Log::error('Error in creditTypeData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'An error occurred while fetching credit type data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 