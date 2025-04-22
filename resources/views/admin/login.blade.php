<!doctype html>
<html lang="en">
  <!-- [Head] start -->

  <head>
    @include('layouts.heads_page')
    @include('layouts.heads_css')
   
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    @include('layouts.loader')
 
    <div class="auth-main">
      <div class="auth-wrapper v1">
        <div class="auth-form">
          <div class="position-relative my-5">
            <div class="auth-bg">
              <span class="r"></span>
              <span class="r s"></span>
              <span class="r s"></span>
              <span class="r"></span>
            </div>
            <div class="card mb-0">
              <div class="card-body">
                <div class="text-center">
                    <a href="#"><img src="{{ asset('admin/assets/images/logo-dark.svg') }}" alt="img" /></a>

                </div>
                <h4 class="text-center f-w-500 mt-4 mb-3">Login</h4>
                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <!-- Display Validation Errors -->
@if ($errors->any())
<div class="alert alert-danger">
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif
                <div class="form-group mb-3">
                  <input type="email" name="email" class="form-control" id="floatingInput" placeholder="Email Address" required />
                </div>
                <div class="form-group mb-3">
                  <input type="password" name="password" class="form-control" id="floatingInput1" placeholder="Password" required />
                </div>

            
          
                <div class="d-flex mt-1 justify-content-between align-items-center">
                  <div class="form-check">
                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="" />
                    <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                  </div>
                  <h6 class="text-secondary f-w-400 mb-0">Forgot Password?</h6>
                </div>
                <div class="text-center mt-4">
                  <button type="type="submit"" class="btn btn-primary shadow px-sm-4">Login</button>
                </div>
            </form>
                <div class="d-flex justify-content-between align-items-end mt-4">
                  <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
                  <a href="#" class="link-primary">Create Account</a>
                </div>
              </div>
          
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('layouts.footer_js')
  
  </body>
  <!-- [Body] end -->
</html>
