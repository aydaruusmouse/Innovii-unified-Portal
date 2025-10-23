<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            Log::info('DashboardController: Starting index method');

            // Log database connection status
            try {
                DB::connection()->getPdo();
                Log::info('DashboardController: Database connection successful');
            } catch (\Exception $e) {
                Log::error('DashboardController: Database connection failed', [
                    'error' => $e->getMessage()
                ]);
                return view('admin.dashboard')->with('error', 'Database connection failed');
            }

            // Get total active subscriptions
            $totalActive = DB::table('subscription_base')
                ->where('status', 'ACTIVE')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('base_count');
            Log::info('DashboardController: Total active subscriptions', [
                'count' => $totalActive
            ]);

            // Get total failed subscriptions
            $totalFailed = DB::table('subscription_base')
                ->where('status', 'FAILED')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('base_count');
            Log::info('DashboardController: Total failed subscriptions', [
                'count' => $totalFailed
            ]);

            // Get total canceled subscriptions
            $totalCanceled = DB::table('subs_in_out_count')
                ->where('status', 'CANCELED')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('base_count');
            Log::info('DashboardController: Total canceled subscriptions', [
                'count' => $totalCanceled
            ]);

            // Log the SQL queries for debugging
            Log::debug('DashboardController: SQL Queries', [
                'active_query' => DB::table('subscription_base')
                    ->where('status', 'ACTIVE')
                    ->toSql(),
                'failed_query' => DB::table('subscription_base')
                    ->where('status', 'FAILED')
                    ->toSql(),
                'canceled_query' => DB::table('subs_in_out_count')
                    ->where('status', 'CANCELED')
                    ->toSql()
            ]);

            // Get service-wise statistics
            $serviceStats = DB::table('subscription_base')
                ->select(
                    'name',
                    DB::raw('SUM(CASE WHEN status = "ACTIVE" THEN base_count ELSE 0 END) as active_count'),
                    DB::raw('SUM(CASE WHEN status = "FAILED" THEN base_count ELSE 0 END) as failed_count')
                )
                ->groupBy('name')
                ->get();
            Log::info('DashboardController: Service statistics', [
                'count' => $serviceStats->count(),
                'data' => $serviceStats
            ]);

            // Get canceled counts for each service
            $canceledStats = DB::table('subs_in_out_count')
                ->select(
                    'name',
                    DB::raw('SUM(base_count) as canceled_count')
                )
                ->where('status', 'CANCELED')
                ->groupBy('name')
                ->get();
            Log::info('DashboardController: Canceled statistics', [
                'count' => $canceledStats->count(),
                'data' => $canceledStats
            ]);

            // Merge the statistics
            $serviceStats = $serviceStats->map(function ($service) use ($canceledStats) {
                $canceled = $canceledStats->firstWhere('name', $service->name);
                $service->canceled_count = $canceled ? $canceled->canceled_count : 0;
                return $service;
            });
            Log::info('DashboardController: Merged service statistics', [
                'count' => $serviceStats->count(),
                'data' => $serviceStats
            ]);

            // Get monthly trends
            $monthlyTrends = DB::table('subscription_base')
                ->select(
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                    DB::raw('SUM(CASE WHEN status = "ACTIVE" THEN base_count ELSE 0 END) as active_count'),
                    DB::raw('SUM(CASE WHEN status = "FAILED" THEN base_count ELSE 0 END) as failed_count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            Log::info('DashboardController: Monthly trends', [
                'count' => $monthlyTrends->count(),
                'data' => $monthlyTrends
            ]);

            // Get canceled trends
            $canceledTrends = DB::table('subs_in_out_count')
                ->select(
                    DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                    DB::raw('SUM(base_count) as canceled_count')
                )
                ->where('status', 'CANCELED')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            Log::info('DashboardController: Canceled trends', [
                'count' => $canceledTrends->count(),
                'data' => $canceledTrends
            ]);

            // Merge the trends
            $monthlyTrends = $monthlyTrends->map(function ($trend) use ($canceledTrends) {
                $canceled = $canceledTrends->firstWhere('month', $trend->month);
                $trend->canceled_count = $canceled ? $canceled->canceled_count : 0;
                return $trend;
            });
            Log::info('DashboardController: Merged monthly trends', [
                'count' => $monthlyTrends->count(),
                'data' => $monthlyTrends
            ]);

            // Get total offers count
            $totalOffers = DB::table('offers')->count();
            $activeOffers = DB::table('offers')->where('status', 'ACTIVE')->count();
            Log::info('DashboardController: Offers counts', [
                'total' => $totalOffers,
                'active' => $activeOffers
            ]);

            // Prepare status distribution data
            $statusDistribution = [
                ['status' => 'Active', 'count' => $totalActive],
                ['status' => 'Failed', 'count' => $totalFailed],
                ['status' => 'Canceled', 'count' => $totalCanceled]
            ];

            // Prepare view data
            $viewData = compact(
                'totalActive',
                'totalFailed',
                'totalCanceled',
                'serviceStats',
                'monthlyTrends',
                'totalOffers',
                'activeOffers',
                'statusDistribution'
            );
            Log::info('DashboardController: Prepared view data', [
                'keys' => array_keys($viewData)
            ]);

            return view('admin.dashboard', $viewData);

        } catch (\Exception $e) {
            Log::error('DashboardController Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.dashboard')->with('error', 'Error loading dashboard data');
        }
    }
} 