<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get total active subscriptions
            $totalActive = DB::table('subscription_base')
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            // Get total failed subscriptions
            $totalFailed = DB::table('subscription_base')
                ->where('status', 'FAILED')
                ->sum('base_count');

            // Get total canceled subscriptions
            $totalCanceled = DB::table('subs_in_out_count')
                ->where('status', 'CANCELED')
                ->sum('base_count');

            // Get service-wise statistics
            $serviceStats = DB::table('subscription_base')
                ->select(
                    'name',
                    DB::raw('SUM(CASE WHEN status = "ACTIVE" THEN base_count ELSE 0 END) as active_count'),
                    DB::raw('SUM(CASE WHEN status = "FAILED" THEN base_count ELSE 0 END) as failed_count')
                )
                ->groupBy('name')
                ->get();

            // Get canceled counts for each service
            $canceledStats = DB::table('subs_in_out_count')
                ->select(
                    'name',
                    DB::raw('SUM(base_count) as canceled_count')
                )
                ->where('status', 'CANCELED')
                ->groupBy('name')
                ->get();

            // Merge the statistics
            $serviceStats = $serviceStats->map(function ($service) use ($canceledStats) {
                $canceled = $canceledStats->firstWhere('name', $service->name);
                $service->canceled_count = $canceled ? $canceled->canceled_count : 0;
                return $service;
            });

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

            // Merge the trends
            $monthlyTrends = $monthlyTrends->map(function ($trend) use ($canceledTrends) {
                $canceled = $canceledTrends->firstWhere('month', $trend->month);
                $trend->canceled_count = $canceled ? $canceled->canceled_count : 0;
                return $trend;
            });

            return view('admin.dashboard', compact(
                'totalActive',
                'totalFailed',
                'totalCanceled',
                'serviceStats',
                'monthlyTrends'
            ));

        } catch (\Exception $e) {
            \Log::error('Dashboard Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.dashboard')->with('error', 'Error loading dashboard data');
        }
    }
} 