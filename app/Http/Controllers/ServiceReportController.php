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

            // Base query
            $query = DB::table('subs_in_out_count');

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

            // Get the data grouped by date and service
            $serviceData = $query->select(
                'date',
                'name',
                DB::raw('SUM(CASE WHEN status = "ACTIVE" THEN base_count ELSE 0 END) as active_count'),
                DB::raw('SUM(CASE WHEN status = "FAILED" THEN base_count ELSE 0 END) as failed_count'),
                DB::raw('SUM(CASE WHEN status = "NEW" THEN base_count ELSE 0 END) as new_count'),
                DB::raw('SUM(CASE WHEN status = "CANCELED" THEN base_count ELSE 0 END) as canceled_count'),
                DB::raw('SUM(base_count) as total_subs')
            )
            ->groupBy('date', 'name')
            ->orderBy('date', 'desc')
            ->get();

            // Calculate totals for the pie chart
            $totalActive = $serviceData->sum('active_count');
            $totalFailed = $serviceData->sum('failed_count');
            $totalNew = $serviceData->sum('new_count');
            $totalCanceled = $serviceData->sum('canceled_count');

            // Prepare table data
            $tableData = $serviceData->map(function ($item) {
                return [
                    'date' => $item->date,
                    'name' => $item->name,
                    'total_subs' => $item->total_subs,
                    'active' => $item->active_count,
                    'failed' => $item->failed_count,
                    'new' => $item->new_count,
                    'canceled' => $item->canceled_count
                ];
            });

            return response()->json([
                'status_totals' => [
                    'active' => $totalActive,
                    'failed' => $totalFailed,
                    'new' => $totalNew,
                    'canceled' => $totalCanceled
                ],
                'table_data' => $tableData,
                'dates' => $serviceData->pluck('date')->unique()->values(),
                'subscription_totals' => $serviceData->pluck('total_subs')->values()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch status-wise report',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 


