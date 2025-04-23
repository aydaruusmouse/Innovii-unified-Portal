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

    public function getServiceReport(Request $request)
    {
        try {
            $serviceName = $request->input('service_name');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $status = $request->input('status');
            
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

            // Get the data
            $serviceData = $query->orderBy('date', 'desc')->get();

            // Group data by status
            $groupedData = $serviceData->groupBy('status')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'date' => $item->date,
                            'count' => $item->base_count
                        ];
                    });
                });

            // Calculate totals for pie chart
            $activeCount = $serviceData->where('status', 'ACTIVE')->sum('base_count');
            $inactiveCount = $serviceData->where('status', 'INACTIVE')->sum('base_count');

            // Prepare table data
            $tableData = $serviceData->map(function ($item) {
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
                'dates' => $serviceData->pluck('date')->unique()->values(),
                'subscription_counts' => $serviceData->pluck('base_count')->values(),
                'table_data' => $tableData
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
                    DB::raw('DATE(created_at) as date'),
                    'name',
                    DB::raw('"FAILED" as status'),
                    DB::raw('COUNT(*) as total_subs')
                )
                ->where('status', 'FAILED')
                ->when($startDate, function ($query) use ($startDate) {
                    return $query->whereDate('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    return $query->whereDate('created_at', '<=', $endDate);
                })
                ->when($serviceName && $serviceName !== 'all', function ($query) use ($serviceName) {
                    return $query->where('name', $serviceName);
                })
                ->groupBy('date', 'name', 'status');

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
} 