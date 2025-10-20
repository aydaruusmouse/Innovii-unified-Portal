<!doctype html>
<html lang="en">
  <head>
    @include('layouts.heads_page')
    @include('layouts.heads_css')
  </head>
  <body @@bodySetup>
    @include('layouts.layout_vertical')

    <div class="pc-container">
      <div class="pc-content">
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">@yield('page_title', 'CRBT Report')</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.simple') }}">Home</a></li>
                  <li class="breadcrumb-item">CRBT Reports</li>
                  <li class="breadcrumb-item" aria-current="page">@yield('page_title', 'CRBT Report')</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>@yield('report_title', 'Report Details')</h5>
              </div>
              <div class="card-body">
                @yield('report_content')
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    @include('layouts.footer')
    @include('layouts.footer_js')
  </body>
</html>