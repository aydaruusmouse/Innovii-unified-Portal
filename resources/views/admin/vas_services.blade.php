<!-- resources/views/admin/vas_services.blade.php -->
@extends('layouts.admin')

@section('title', 'Vas Services')

@section('content')
    <h1>Vas Services</h1>
    <div class="table-responsive">
        <table class="table table-bordered" id="vasServicesTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through Vas Services -->
                @foreach($vasServices as $service)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->type }}</td>
                        <td>{{ $service->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
