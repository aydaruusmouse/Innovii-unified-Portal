<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function corporateInfo()
    {
        return view('admin.crbt.corporate_info');
    }

    public function corporateUsers()
    {
        return view('admin.crbt.corporate_users');
    }

    public function backupReports()
    {
        return view('admin.crbt.backup_reports');
    }
} 