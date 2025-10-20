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

            $query = DB::connection('crbt')
                ->table('HOURLY_CRBT_MIS')
                ->select('*')
                ->orderBy('date', 'desc')
                ->orderBy('hour');

            if ($date) {
                $query->whereDate('date', Carbon::parse($date));
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
                ->select('interface_name',
                    DB::raw('SUM(subscriptions) as total_subscriptions'),
                    DB::raw('SUM(unsubscriptions) as total_unsubscriptions'),
                    DB::raw('SUM(tone_usage) as total_tone_usage'))
                ->groupBy('interface_name')
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
}
