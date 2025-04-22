@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reports</h1>
    <form method="GET" action="{{ route('reports.index') }}">
        <div>
            <label for="date">Date:</label>
            <input type="date" name="date" value="{{ request('date') }}">
        </div>
        <div>
            <label for="service">Service:</label>
            <input type="text" name="service" value="{{ request('service') }}">
        </div>
        <div>
            <label for="status">Status:</label>
            <select name="status">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Total Subs</th>
                <th>Active</th>
                <th>Failed</th>
                <th>New</th>
                <th>Canceled</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
            <tr>
                <td>{{ $report->date }}</td>
                <td>{{ $report->name }}</td>
                <td>{{ $report->total_subs }}</td>
                <td>{{ $report->active }}</td>
                <td>{{ $report->failed }}</td>
                <td>{{ $report->new }}</td>
                <td>{{ $report->canceled }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reports->links() }}
</div>
@endsection
