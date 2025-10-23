<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class CRBTController extends Controller
{
    public function dailyMIS()
    {
        return view('admin.crbt.daily_mis');
    }

    public function hourlyMIS()
    {
        return view('admin.crbt.hourly_mis');
    }

    public function interfaceSubUnsub()
    {
        return view('admin.crbt.interface_sub_unsub');
    }

    public function interfaceTone()
    {
        return view('admin.crbt.interface_tone');
    }

    public function statusCycle()
    {
        return view('admin.crbt.status_cycle');
    }

    public function hlrActivations()
    {
        return view('admin.crbt.hlr_activations');
    }

    public function userInfo()
    {
        return view('admin.crbt.user_info');
    }

    public function userToneInfo()
    {
        return view('admin.crbt.user_tone_info');
    }

    public function billingCharges()
    {
        return view('admin.crbt.billing_charges');
    }

    // Corporate CRBT Reports
    public function corporateInfo()
    {
        return view('admin.crbt.corporate_info');
    }

    public function corporateUsers()
    {
        return view('admin.crbt.corporate_users');
    }

    // Backup Reports
    public function backupReports()
    {
        return view('admin.crbt.backup_reports');
    }

    // API Methods for AJAX
    public function getDailyMISData()
    {

        log::info('getDailyMISData');
        log::info(request('start_date'));
        log::info(request('end_date'));
        log::info(request('page'));
        log::info(request('per_page'));

        log::info('getDailyMISData');
        log::info(request('start_date'));
        log::info(request('end_date'));
        log::info(request('page'));
        log::info(request('per_page'));
       
        try {
            $startDate = request('start_date');
            $endDate = request('end_date');
            $page = max(1, (int) request('page', 1));
            $perPage = min(100, max(5, (int) request('per_page', 10)));

            $query = DB::connection('crbt')
                ->table('DAILY_CRBT_MIS')
                ->select('*')
                ->orderBy('date', 'desc');

            if ($startDate) {
                $query->whereDate('date', '>=', Carbon::parse($startDate));
            }
            if ($endDate) {
                $query->whereDate('date', '<=', Carbon::parse($endDate));
            }

            $total = (clone $query)->count();
            $items = $query->forPage($page, $perPage)->get();

            return Response::json([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT daily MIS fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 0,
                ],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getHourlyMISData()
    {
        try {
            $date = request('date');
            $page = max(1, (int) request('page', 1));
            $perPage = min(100, max(5, (int) request('per_page', 24)));

            // Get aggregated data by date and hour
            $query = DB::connection('crbt')
                ->table('HR_WISE_CRBT_MIS')
                ->select(
                    DB::raw('DATE(date) as date'),
                    'currentHour as hour',
                    DB::raw('SUM(CASE WHEN action = "SubscriptionCount" THEN data ELSE 0 END) as activeNrml'),
                    DB::raw('SUM(CASE WHEN action = "UnSubscriptionCount" THEN data ELSE 0 END) as vchurnNrml'),
                    DB::raw('SUM(CASE WHEN action = "RenewalCount" THEN data ELSE 0 END) as VsmsSuccess')
                )
                ->groupBy(DB::raw('DATE(date)'), 'currentHour')
                ->orderBy(DB::raw('DATE(date)'), 'desc')
                ->orderBy('currentHour', 'desc');

            if ($date) {
                $query->whereDate('date', Carbon::parse($date));
            }

            $total = (clone $query)->count();
            $items = $query->forPage($page, $perPage)->get()->map(function ($item) {
                return [
                    'date' => $item->date,
                    'hour' => $item->hour,
                    'activeNrml' => (int) $item->activeNrml,
                    'vchurnNrml' => (int) $item->vchurnNrml,
                    'VsmsSuccess' => (int) $item->VsmsSuccess,
                    'SubsRev' => 0, // No revenue data in this table
                    'RenewRev' => 0, // No revenue data in this table
                ];
            })->toArray();

            return Response::json([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT hourly MIS fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 24,
                    'total' => 0,
                    'last_page' => 0,
                ],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getInterfaceData()
    {
        try {
            $startDate = request('start_date');
            $endDate = request('end_date');

            $query = DB::connection('crbt')
                ->table('INTERFACE_WISE_SUB_UNSUB_MIS')
                ->select(
                    'interface',
                    DB::raw('SUM(CASE WHEN action = "SUB" THEN data ELSE 0 END) as total_subscriptions'),
                    DB::raw('SUM(CASE WHEN action = "UNSUB" THEN data ELSE 0 END) as total_unsubscriptions'),
                    DB::raw('SUM(CASE WHEN action = "TONE" THEN data ELSE 0 END) as total_tone_usage')
                )
                ->groupBy('interface')
                ->orderByDesc('total_subscriptions');

            if ($startDate) {
                $query->whereDate('date', '>=', Carbon::parse($startDate));
            }
            if ($endDate) {
                $query->whereDate('date', '<=', Carbon::parse($endDate));
            }

            $rows = $query->get();

            return Response::json([
                'data' => $rows,
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT interface data fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getInterfaceToneData()
    {
        try {
            $startDate = request('start_date');
            $endDate = request('end_date');
            $page = max(1, (int) request('page', 1));
            $perPage = min(100, max(5, (int) request('per_page', 10)));

            $query = DB::connection('crbt')
                ->table('INTERFACE_WISE_TONE_MIS')
                ->select('interface',
                    DB::raw('SUM(data) as total_tone_usage'),
                    DB::raw('MIN(date) as start_date'),
                    DB::raw('MAX(date) as end_date'))
                ->groupBy('interface')
                ->orderByDesc(DB::raw('SUM(data)'));

            if ($startDate) {
                $query->whereDate('date', '>=', Carbon::parse($startDate));
            }
            if ($endDate) {
                $query->whereDate('date', '<=', Carbon::parse($endDate));
            }

            $total = (clone $query)->count();
            $items = $query->forPage($page, $perPage)->get();

            return Response::json([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT interface tone data fetch failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return Response::json([
                'data' => [],
                'message' => 'No data available or table missing',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getStatusCycleData()
    {
        try {
            $startDate = request('start_date');
            $endDate = request('end_date');
            $page = max(1, (int) request('page', 1));
            $perPage = min(100, max(5, (int) request('per_page', 10)));

            $query = DB::connection('crbt')
                ->table('DAILY_CRBT_MIS')
                ->select('date', 'activeBase', 'graceBase', 'suspendBase', 'vchurnBase', 'invchurnBase')
                ->orderByDesc('date');

            if ($startDate) {
                $query->whereDate('date', '>=', Carbon::parse($startDate));
            }
            if ($endDate) {
                $query->whereDate('date', '<=', Carbon::parse($endDate));
            }

            $total = (clone $query)->count();
            $items = $query->forPage($page, $perPage)->get();

            return Response::json([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT status cycle data fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getHLRActivationsData()
    {
        try {
            $startDate = request('start_date');
            $endDate = request('end_date');
            $page = max(1, (int) request('page', 1));
            $perPage = min(100, max(5, (int) request('per_page', 10)));

            $query = DB::connection('crbt')
                ->table('hlractivations')
                ->select('date', 'msisdn', 'status', 'activation_time', 'response_code', 'error_message')
                ->orderByDesc('date');

            if ($startDate) {
                $query->whereDate('date', '>=', Carbon::parse($startDate));
            }
            if ($endDate) {
                $query->whereDate('date', '<=', Carbon::parse($endDate));
            }

            $total = (clone $query)->count();
            $items = $query->forPage($page, $perPage)->get();

            return Response::json([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT HLR activations data fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getUserInfoData()
    {
        try {
            $msisdn = request('msisdn');
            $status = request('status');
            $page = request('page', 1);
            $perPage = request('per_page', 20);

            $query = DB::connection('crbt')
                ->table('userinfo')
                ->select('msisdn', 'status', 'registration_date', 'last_activity', 'service_type');

            if ($msisdn) {
                $query->where('msisdn', 'like', '%' . $msisdn . '%');
            }
            if ($status) {
                $query->where('status', $status);
            }

            $total = (clone $query)->count();
            $items = $query->forPage($page, $perPage)->get();

            return Response::json([
                'data' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $perPage),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT user info data fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                    'last_page' => 0,
                ],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getUserToneInfoData()
    {
        try {
            // For now, return sample data since the table structure is different
            $sampleData = [
                [
                    'msisdn' => '252612345678',
                    'tone_id' => 'TONE001',
                    'tone_name' => 'Sample Tone 1',
                    'status' => 'active',
                    'activation_date' => '2025-01-01',
                    'expiry_date' => '2025-12-31'
                ],
                [
                    'msisdn' => '252612345679',
                    'tone_id' => 'TONE002',
                    'tone_name' => 'Sample Tone 2',
                    'status' => 'expired',
                    'activation_date' => '2024-12-01',
                    'expiry_date' => '2024-12-31'
                ]
            ];

            return Response::json([
                'data' => $sampleData,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => count($sampleData),
                    'last_page' => 1,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT user tone info data fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                    'last_page' => 0,
                ],
                'message' => 'No data available or table missing',
            ]);
        }
    }

    public function getBillingChargesData()
    {
        try {
            // For now, return sample data since the table structure is different
            $sampleData = [
                [
                    'date' => '2025-01-15',
                    'msisdn' => '252612345678',
                    'charge_type' => 'subscription',
                    'amount' => 1000,
                    'status' => 'success',
                    'transaction_id' => 'TXN001'
                ],
                [
                    'date' => '2025-01-14',
                    'msisdn' => '252612345679',
                    'charge_type' => 'renewal',
                    'amount' => 500,
                    'status' => 'success',
                    'transaction_id' => 'TXN002'
                ]
            ];

            return Response::json([
                'data' => $sampleData,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => count($sampleData),
                    'last_page' => 1,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('CRBT billing charges data fetch failed', ['error' => $e->getMessage()]);
            return Response::json([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                    'last_page' => 0,
                ],
                'message' => 'No data available or table missing',
            ]);
        }
    }
}
