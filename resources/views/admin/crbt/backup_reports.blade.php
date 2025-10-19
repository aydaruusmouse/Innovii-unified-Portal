@extends('layouts.layout_vertical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">CRBT Reports</a></li>
                        <li class="breadcrumb-item active">CRBT Core Backup</li>
                    </ol>
                </div>
                <h4 class="page-title">CRBT Core Backup</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">CRBT Core Backup Reports</h5>
                </div>
                <div class="card-body">
                    <p>CRBT core backup reports will be displayed here.</p>
                    <!-- Add your backup reports content here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('layouts.footer_js')
