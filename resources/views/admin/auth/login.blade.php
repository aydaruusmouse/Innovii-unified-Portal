<!DOCTYPE html>
<html lang="en">

<head>
  <title>Datta Able Free Bootstrap 4 Admin Template</title>
  <!-- Meta -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="Datta Able Bootstrap admin template" />
  <meta name="author" content="CodedThemes"/>

  <!-- Favicon icon -->
  <link rel="icon" href="/admin/assets/images/favicon.ico" type="image/x-icon">
  <!-- fontawesome icon -->
  <link rel="stylesheet" href="/admin/assets/fonts/fontawesome/css/fontawesome-all.min.css">
  <!-- animation css -->
  <link rel="stylesheet" href="/admin/assets/plugins/animation/css/animate.min.css">
  <!-- vendor css -->
  <link rel="stylesheet" href="/admin/assets/css/style.css">

  <style>
    body {
      min-height: 100vh;
      background: radial-gradient(1200px 600px at 10% 10%, #eef3ff 0%, #f7f9ff 30%, #f4f6fb 60%, #eef1f7 100%);
    }
    .auth-wrapper {
      display: grid;
      place-items: center;
      min-height: 100vh;
      padding: 32px 16px;
    }
    .auth-content {
      width: 100%;
      max-width: 420px; /* narrower width */
      margin: 0 auto;
    }
    .card {
      border: 0;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(20, 27, 45, 0.08), 0 2px 10px rgba(20, 27, 45, 0.06);
      overflow: hidden;
    }
    .card-body {
      padding: 28px 26px 24px;
      text-align: center; /* ensure all headings/text align consistently */
    }
    .auth-icon {
      color: #3f80ff;
      background: rgba(63,128,255,0.1);
      border-radius: 14px;
      padding: 14px;
      font-size: 28px;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 6px;
    }
    .brand img {
      height: 34px;
      width: auto;
    }
    .brand span {
      font-weight: 700;
      font-size: 18px;
      letter-spacing: 0.2px;
      color: #2a2f45;
    }
    .form-control {
      height: 44px;
      border-radius: 12px;
      border-color: #e7e9f3;
    }
    .form-control:focus {
      border-color: #3f80ff;
      box-shadow: 0 0 0 0.15rem rgba(63,128,255,.15);
    }
    .btn-primary {
      height: 44px;
      border-radius: 12px;
      background: linear-gradient(135deg, #4b7bff 0%, #6a9dff 100%);
      border: none;
      transition: transform .05s ease, box-shadow .2s ease;
    }
    .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(75, 123, 255, .18);
    }
    .hint {
      font-size: 12px;
      color: #6b7391;
      text-align: center;
      display: block;
    }
    /* Align checkbox text nicely with the box */
    .form-group .checkbox { display: inline-flex; align-items: center; gap: 8px; }
    .form-group .checkbox input[type="checkbox"] { margin: 0; width: 16px; height: 16px; }
    .form-group .checkbox label { margin: 0; line-height: 1; cursor: pointer; }
    /* Ensure inputs and button are the same width and centered */
    form .input-group, form .btn { width: 100%; }
  </style>
</head>

<body>
  <div class="auth-wrapper">
    <div class="auth-content">
      <div class="auth-bg">
        <span class="r"></span>
        <span class="r s"></span>
        <span class="r s"></span>
        <span class="r"></span>
      </div>
      <div class="card">
        <div class="card-body text-center">
          <h5 class="mb-2" style="font-weight:700;color:#2a2f45;">Unified Reports Portal</h5>
          <h3 class="mb-2">Sign in</h3>
          <div class="hint mb-3">Use your admin username and password to continue</div>

          @if ($errors->any())
            <div class="alert alert-danger text-left">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-3 text-center">
            @csrf
            <div class="input-group mb-3">
              <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="input-group mb-4">
              <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary shadow-2 mb-4 w-100">Login</button>
          </form>

          <p class="mb-2 text-muted">Forgot password?</p>
          <p class="mb-0 text-muted">Don’t have an account?</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Required Js -->
  <script src="/admin/assets/js/vendor-all.min.js"></script>
  <script src="/admin/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>


