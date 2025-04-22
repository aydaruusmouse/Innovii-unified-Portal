<?php

namespace App\Http\Controllers;

use App\Models\SDFReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['date', 'service', 'status']);
        $reports = SDFReport::query();

        if (!empty($filters['date'])) {
            $reports->where('date', $filters['date']);
        }

        if (!empty($filters['service'])) {
            $reports->where('name', $filters['service']);
        }

        if (!empty($filters['status'])) {
            $reports->where('status', $filters['status']);
        }

        return view('reports.index', [
            'reports' => $reports->paginate(20), // Use pagination to manage large datasets
        ]);
    }
}
