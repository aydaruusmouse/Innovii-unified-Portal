@extends('layouts.layout_vertical')

@section('title', 'CRPT Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">CRPT Reports</a></li>
                        <li class="breadcrumb-item active">@yield('page_title')</li>
                    </ol>
                </div>
                <h4 class="page-title">@yield('report_title')</h4>
            </div>
        </div>
    </div>

    @yield('report_content')
</div>
@endsection

@push('styles')
<style>
    .table-responsive {
        margin-bottom: 1rem;
    }
    .card {
        margin-bottom: 1.5rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endpush 