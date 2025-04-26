<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceReportController extends Controller
{
    public function index()
    {
        // Get unique service names from the subs_in_out_count table
        $services = DB::table('subs_in_out_count')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get();

        // Get unique offers for the dropdown
        $offers = DB::table('offers')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get();

        return view('admin.single_service', compact('services', 'offers'));
    }

    public function statusWiseServices()
    {
        // Get unique service names from the subs_in_out_count table
        $services = DB::table('subs_in_out_count')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return (object)['name' => $item->name];
            });

        return view('admin.status_wise_services', compact('services'));
    }

    public function statusAnalysis()
    {
        // Get unique service names from the subscription_base table
        $services = DB::table('subscription_base')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get();

        return view('admin.status_analysis', compact('services'));
    }

    public function getServiceReport(Request $request)
    {
        try {
            $serviceName = $request->input('service_name');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $status = $request->input('status');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            
            if (!$serviceName) {
                return response()->json([
                    'error' => 'Service name is required'
                ], 400);
            }

            // Base query
            $query = DB::table('subs_in_out_count')
                ->where('name', $serviceName);

            // Apply date filters if provided
            if ($startDate) {
                $query->where('date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('date', '<=', $endDate);
            }

            // Apply status filter if provided and not 'all'
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Get paginated data
            $paginatedData = $query->orderBy('date', 'desc')
                                 ->paginate($perPage, ['*'], 'page', $page);

            // Get all data for charts (without pagination)
            $allData = $query->orderBy('date', 'desc')->get();

            // Group data by status
            $groupedData = $allData->groupBy('status')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'date' => $item->date,
                            'count' => $item->base_count
                        ];
                    });
                });

            // Calculate totals for pie chart
            $activeCount = $allData->where('status', 'ACTIVE')->sum('base_count');
            $inactiveCount = $allData->where('status', 'INACTIVE')->sum('base_count');

            // Prepare table data
            $tableData = $paginatedData->map(function ($item) {
                return [
                    'start_date' => $item->date,
                    'end_date' => $item->date,
                    'offer' => $item->name,
                    'status' => $item->status,
                    'subscribers' => $item->base_count
                ];
            });

            return response()->json([
                'service_name' => $serviceName,
                'active' => $groupedData->get('ACTIVE', collect())->values(),
                'canceled' => $groupedData->get('CANCELED', collect())->values(),
                'active_count' => $activeCount,
                'inactive_count' => $inactiveCount,
                'dates' => $allData->pluck('date')->unique()->values(),
                'subscription_counts' => $allData->pluck('base_count')->values(),
                'table_data' => $tableData,
                'pagination' => [
                    'total' => $paginatedData->total(),
                    'per_page' => $paginatedData->perPage(),
                    'current_page' => $paginatedData->currentPage(),
                    'last_page' => $paginatedData->lastPage(),
                    'from' => $paginatedData->firstItem(),
                    'to' => $paginatedData->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch service report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatusWiseReport(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $serviceName = $request->input('service_name');
            $status = $request->input('status');
            $perPage = $request->input('per_page', 10); // Default 10 items per page

            // Base query for subs_in_out_count
            $query = DB::table('subs_in_out_count')
                ->whereIn('status', ['ACTIVE', 'CANCELED']);

            // Apply date filters if provided
            if ($startDate) {
                $query->where('date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('date', '<=', $endDate);
            }

            // Apply service filter if provided and not 'all'
            if ($serviceName && $serviceName !== 'all') {
                $query->where('name', $serviceName);
            }

            // Apply status filter if provided and not 'all'
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Get the data grouped by date and service
            $serviceData = $query->select(
                'date',
                'name',
                'status',
                DB::raw('SUM(base_count) as total_subs')
            )
            ->groupBy('date', 'name', 'status')
            ->orderBy('date', 'desc');

            // Get failed status from subscription_base
            $failedQuery = DB::table('subscription_base')
                ->select(
                    'date',
                    'name',
                    DB::raw('"FAILED" as status'),
                    'base_count as total_subs'
                )
                ->where('status', 'FAILED')
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('date', '<=', $endDate);
                })
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('name', $serviceName);
                })
                ->groupBy('date', 'name', 'status', 'base_count');

            // Union the queries
            $combinedQuery = $serviceData->union($failedQuery);

            // Get paginated results
            $paginatedData = $combinedQuery->paginate($perPage);

            // Calculate totals for the pie chart
            $totalActive = $paginatedData->where('status', 'ACTIVE')->sum('total_subs');
            $totalCanceled = $paginatedData->where('status', 'CANCELED')->sum('total_subs');
            $totalFailed = $paginatedData->where('status', 'FAILED')->sum('total_subs');

            // Prepare table data
            $tableData = $paginatedData->map(function ($item) {
                return [
                    'date' => $item->date,
                    'name' => $item->name,
                    'status' => $item->status,
                    'total_subs' => $item->total_subs
                ];
            });

            return response()->json([
                'status_totals' => [
                    'active' => $totalActive,
                    'canceled' => $totalCanceled,
                    'failed' => $totalFailed
                ],
                'table_data' => $tableData,
                'dates' => $paginatedData->pluck('date')->unique()->values(),
                'subscription_totals' => $paginatedData->pluck('total_subs')->values(),
                'pagination' => [
                    'total' => $paginatedData->total(),
                    'per_page' => $paginatedData->perPage(),
                    'current_page' => $paginatedData->currentPage(),
                    'last_page' => $paginatedData->lastPage(),
                    'from' => $paginatedData->firstItem(),
                    'to' => $paginatedData->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch status-wise report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatusAnalysisData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $serviceName = $request->input('service_name');
            $status = $request->input('status');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Log the incoming request parameters
            \Log::info('Status Analysis Request:', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'service_name' => $serviceName,
                'status' => $status
            ]);

            // Base query for subscription_base
            $baseQuery = DB::table('subscription_base')
                ->select(
                    'date',
                    'name',
                    'status',
                    DB::raw('SUM(base_count) as base_count')
                )
                ->whereIn('status', ['ACTIVE', 'FAILED'])
                ->groupBy('date', 'name', 'status');

            // Query for canceled subscriptions from subs_in_out
            $canceledQuery = DB::table('subs_in_out')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    'name',
                    DB::raw('"CANCELED" as status'),
                    DB::raw('COUNT(*) as base_count')
                )
                ->where('status', 'CANCELED')
                ->groupBy('date', 'name', 'status');

            // Apply date filters if provided
            if ($startDate && $endDate) {
                $baseQuery->whereBetween('date', [$startDate, $endDate]);
                $canceledQuery->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
                \Log::info('Applied date range filter:', [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
            }

            // Apply service filter if provided and not 'all'
            if ($serviceName && $serviceName !== 'all') {
                $baseQuery->where('name', $serviceName);
                $canceledQuery->where('name', $serviceName);
                \Log::info('Applied service filter:', ['service_name' => $serviceName]);
            }

            // Apply status filter if provided and not 'all'
            if ($status && $status !== 'all') {
                if ($status === 'CANCELED') {
                    $baseQuery->whereRaw('1=0'); // Exclude all records from base query
                } else {
                    $baseQuery->where('status', $status);
                    $canceledQuery->whereRaw('1=0'); // Exclude canceled records
                }
                \Log::info('Applied status filter:', ['status' => $status]);
            }

            // Union the queries
            $query = $baseQuery->union($canceledQuery);

            // Log the generated SQL query
            \Log::info('Generated SQL Query:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            // Get paginated data
            $paginatedData = $query->orderBy('date', 'desc')
                                 ->orderBy('name')
                                 ->paginate($perPage, ['*'], 'page', $page);

            // Get all data for charts (without pagination)
            $allData = $query->orderBy('date', 'desc')
                           ->orderBy('name')
                           ->get();

            // Log the result counts
            \Log::info('Query Results:', [
                'total_records' => $allData->count(),
                'paginated_records' => $paginatedData->count(),
                'first_record' => $allData->first(),
                'last_record' => $allData->last()
            ]);

            // Calculate status totals
            $statusTotals = [
                'active' => $allData->where('status', 'ACTIVE')->sum('base_count'),
                'failed' => $allData->where('status', 'FAILED')->sum('base_count'),
                'canceled' => $allData->where('status', 'CANCELED')->sum('base_count')
            ];

            // Group data by date for trend chart
            $dates = $allData->pluck('date')->unique()->values();
            $activeData = $allData->where('status', 'ACTIVE')
                                ->groupBy('date')
                                ->map(function ($group) {
                                    return $group->sum('base_count');
                                });
            $failedData = $allData->where('status', 'FAILED')
                                ->groupBy('date')
                                ->map(function ($group) {
                                    return $group->sum('base_count');
                                });
            $canceledData = $allData->where('status', 'CANCELED')
                                ->groupBy('date')
                                ->map(function ($group) {
                                    return $group->sum('base_count');
                                });

            return response()->json([
                'table_data' => $paginatedData->items(),
                'status_totals' => $statusTotals,
                'dates' => $dates,
                'active_data' => $activeData,
                'failed_data' => $failedData,
                'canceled_data' => $canceledData,
                'pagination' => [
                    'total' => $paginatedData->total(),
                    'per_page' => $paginatedData->perPage(),
                    'current_page' => $paginatedData->currentPage(),
                    'last_page' => $paginatedData->lastPage(),
                    'from' => $paginatedData->firstItem(),
                    'to' => $paginatedData->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Status Analysis Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function overallSubscriberReport()
    {
        // Get unique service names from the subscription_base table
        $services = DB::table('subscription_base')
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get();

        return view('admin.overall_subscriber_report', compact('services'));
    }

    public function getOverallSubscriberReport(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $serviceName = $request->input('service_name');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Log the incoming request parameters
            \Log::info('Overall Subscriber Report Request:', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'service_name' => $serviceName
            ]);

            // Get the latest date's active subscribers
            $latestActiveQuery = DB::table('subscription_base')
                ->select(
                    'date',
                    'name',
                    'status',
                    'base_count'
                )
                ->where('status', 'ACTIVE')
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('name', $serviceName);
                })
                ->orderBy('date', 'desc')
                ->first();

            // Get the latest date's failed subscribers
            $latestFailedQuery = DB::table('subscription_base')
                ->select(
                    'date',
                    'name',
                    'status',
                    'base_count'
                )
                ->where('status', 'FAILED')
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('name', $serviceName);
                })
                ->orderBy('date', 'desc')
                ->first();

            // Get canceled count from subs_in_out_count
            $canceledCount = DB::table('subs_in_out_count')
                ->select(DB::raw('SUM(base_count) as total_canceled'))
                ->where('status', 'CANCELED')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('date', [$startDate, $endDate]);
                })
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('name', $serviceName);
                })
                ->first();

            // Get main data from subscription_base
            $query = DB::table('subscription_base')
                ->select(
                    'date',
                    'name',
                    'status',
                    DB::raw('SUM(base_count) as base_count')
                )
                ->whereIn('status', ['ACTIVE', 'FAILED'])
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('date', [$startDate, $endDate]);
                })
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('name', $serviceName);
                })
                ->groupBy('date', 'name', 'status');

            // Get paginated data
            $paginatedData = $query->orderBy('date', 'desc')
                                 ->orderBy('name')
                                 ->paginate($perPage, ['*'], 'page', $page);

            // Get all data for charts (without pagination)
            $allData = $query->orderBy('date', 'desc')
                           ->orderBy('name')
                           ->get();

            // Calculate status totals
            $totals = [
                'active' => $latestActiveQuery ? $latestActiveQuery->base_count : 0,
                'failed' => $latestFailedQuery ? $latestFailedQuery->base_count : 0,
                'canceled' => $canceledCount ? $canceledCount->total_canceled : 0
            ];

            // Group data by date for trend chart
            $dates = $allData->pluck('date')->unique()->values();
            $activeData = $allData->where('status', 'ACTIVE')
                                ->groupBy('date')
                                ->map(function ($group) {
                                    return $group->sum('base_count');
                                });
            $failedData = $allData->where('status', 'FAILED')
                                ->groupBy('date')
                                ->map(function ($group) {
                                    return $group->sum('base_count');
                                });

            return response()->json([
                'table_data' => $paginatedData->items(),
                'totals' => $totals,
                'latest_active_date' => $latestActiveQuery ? $latestActiveQuery->date : null,
                'latest_failed_date' => $latestFailedQuery ? $latestFailedQuery->date : null,
                'dates' => $dates,
                'active_data' => $activeData,
                'failed_data' => $failedData,
                'pagination' => [
                    'total' => $paginatedData->total(),
                    'per_page' => $paginatedData->perPage(),
                    'current_page' => $paginatedData->currentPage(),
                    'last_page' => $paginatedData->lastPage(),
                    'from' => $paginatedData->firstItem(),
                    'to' => $paginatedData->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Overall Subscriber Report Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDashboardStats()
    {
        try {
            // Get today's date
            $today = now()->format('Y-m-d');
            
            // Get daily active subscribers from subscription_base
            $dailyActive = DB::table('subscription_base')
                ->where('date', $today)
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            // Get monthly active subscribers (last 30 days)
            $monthlyActive = DB::table('subscription_base')
                ->where('date', '>=', now()->subDays(30)->format('Y-m-d'))
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            // Get yearly active subscribers (last 365 days)
            $yearlyActive = DB::table('subscription_base')
                ->where('date', '>=', now()->subDays(365)->format('Y-m-d'))
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            // Get total services count
            $totalServices = DB::table('subs_in_out_count')
                ->select('name')
                ->distinct()
                ->count();

            // Get total active subscribers by service
            $servicesStats = DB::table('subscription_base')
                ->select('name', DB::raw('SUM(base_count) as total_subscribers'))
                ->where('status', 'ACTIVE')
                ->where('date', $today)
                ->groupBy('name')
                ->orderBy('total_subscribers', 'desc')
                ->limit(5)
                ->get();

            // Get subscription trends for the last 7 days
            $trendData = DB::table('subscription_base')
                ->select('date', DB::raw('SUM(base_count) as total_subscribers'))
                ->where('status', 'ACTIVE')
                ->where('date', '>=', now()->subDays(7)->format('Y-m-d'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Get status distribution
            $statusDistribution = DB::table('subscription_base')
                ->select('status', DB::raw('SUM(base_count) as count'))
                ->where('date', $today)
                ->groupBy('status')
                ->get();

            // Get recent activity (last 5 days)
            $recentActivity = DB::table('subscription_base')
                ->select('date', 'name', 'status', DB::raw('SUM(base_count) as total_subscribers'))
                ->where('date', '>=', now()->subDays(5)->format('Y-m-d'))
                ->groupBy('date', 'name', 'status')
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();

            // Calculate percentage changes
            $yesterdayActive = DB::table('subscription_base')
                ->where('date', now()->subDay()->format('Y-m-d'))
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            $dailyChange = $yesterdayActive > 0 ? (($dailyActive - $yesterdayActive) / $yesterdayActive) * 100 : 0;

            $lastMonthActive = DB::table('subscription_base')
                ->where('date', '>=', now()->subDays(60)->format('Y-m-d'))
                ->where('date', '<', now()->subDays(30)->format('Y-m-d'))
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            $monthlyChange = $lastMonthActive > 0 ? (($monthlyActive - $lastMonthActive) / $lastMonthActive) * 100 : 0;

            $lastYearActive = DB::table('subscription_base')
                ->where('date', '>=', now()->subDays(730)->format('Y-m-d'))
                ->where('date', '<', now()->subDays(365)->format('Y-m-d'))
                ->where('status', 'ACTIVE')
                ->sum('base_count');

            $yearlyChange = $lastYearActive > 0 ? (($yearlyActive - $lastYearActive) / $lastYearActive) * 100 : 0;

            return response()->json([
                'daily_active' => $dailyActive,
                'monthly_active' => $monthlyActive,
                'yearly_active' => $yearlyActive,
                'daily_change' => round($dailyChange, 1),
                'monthly_change' => round($monthlyChange, 1),
                'yearly_change' => round($yearlyChange, 1),
                'total_services' => $totalServices,
                'services_stats' => $servicesStats,
                'trend_data' => $trendData,
                'status_distribution' => $statusDistribution,
                'recent_activity' => $recentActivity
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard Stats Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 