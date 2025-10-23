@extends('admin.crbt.base')

@section('page_title', 'Interface-wise Sub/Unsub')
@section('report_title', 'Interface-wise Subscription/Unsubscription')

@section('report_content')
<div class="row g-3 mb-3">
    <div class="col-md-4"><input type="date" id="startDate" class="form-control" placeholder="Start date"></div>
    <div class="col-md-4"><input type="date" id="endDate" class="form-control" placeholder="End date"></div>
    <div class="col-md-4 d-grid"><button id="applyFilter" class="btn btn-primary">Apply Filter</button></div>
    </div>

<!-- Charts Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Top Interfaces by Subscriptions</h5></div>
            <div class="card-body"><canvas id="subsChart"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Tone Usage by Interface</h5></div>
            <div class="card-body"><canvas id="tonesChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="table-responsive mt-4">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Interface</th>
                <th>Total Subscriptions</th>
                <th>Total Unsubscriptions</th>
                <th>Total Tone Usage</th>
            </tr>
        </thead>
        <tbody id="interfaceBody">
            <tr><td colspan="4" class="text-center">Loading...</td></tr>
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let subsChart, tonesChart;

    function initCharts(labels=[], subs=[], tones=[]) {
        const sctx = document.getElementById('subsChart').getContext('2d');
        const tctx = document.getElementById('tonesChart').getContext('2d');
        if (subsChart) subsChart.destroy();
        if (tonesChart) tonesChart.destroy();
        subsChart = new Chart(sctx, { type:'bar', data:{ labels, datasets:[{ label:'Subscriptions', data:subs, backgroundColor:'rgba(13,110,253,.6)' }] }, options:{ responsive:true, maintainAspectRatio:false } });
        tonesChart = new Chart(tctx, { type:'bar', data:{ labels, datasets:[{ label:'Tone Usage', data:tones, backgroundColor:'rgba(25,135,84,.6)' }] }, options:{ responsive:true, maintainAspectRatio:false } });
    }

    async function fetchInterface() {
        const params = new URLSearchParams();
        const s = document.getElementById('startDate').value;
        const e = document.getElementById('endDate').value;
        if (s) params.append('start_date', s);
        if (e) params.append('end_date', e);
        const url = `/api/crbt/interface-data?${params.toString()}`;
        const tbody = document.getElementById('interfaceBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
        try {
            const res = await fetch(url);
            const json = await res.json();
            const rows = Array.isArray(json.data) ? json.data : [];
            
            // Process the data
            
            if (rows.length===0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No data</td></tr>';
                initCharts();
                return;
            }
            tbody.innerHTML = '';
            const labels=[], subs=[], tones=[];
            rows.forEach((r) => {
                // Extract data from the row - ensure we have the correct field names
                const interfaceName = r.interface || r.interface_name || 'Unknown';
                const subscriptions = r.total_subscriptions || 0;
                const unsubscriptions = r.total_unsubscriptions || 0;
                const toneUsage = r.total_tone_usage || 0;
                
                // Add to chart data
                labels.push(interfaceName);
                subs.push(Number(subscriptions));
                tones.push(Number(toneUsage));
                
                // Add to table
                tbody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${interfaceName}</td>
                        <td>${subscriptions}</td>
                        <td>${unsubscriptions}</td>
                        <td>${toneUsage}</td>
                    </tr>
                `);
            });
            initCharts(labels, subs, tones);
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-danger text-center">Error loading data</td></tr>';
            initCharts();
        }
    }

    document.getElementById('applyFilter').addEventListener('click', fetchInterface);
    fetchInterface();
});
</script>
@endpush
@endsection
