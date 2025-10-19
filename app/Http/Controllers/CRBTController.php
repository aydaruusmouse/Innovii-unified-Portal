<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return response()->json(['message' => 'Daily MIS data endpoint']);
    }

    public function getHourlyMISData()
    {
        return response()->json(['message' => 'Hourly MIS data endpoint']);
    }

    public function getInterfaceData()
    {
        return response()->json(['message' => 'Interface data endpoint']);
    }
}
