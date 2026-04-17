<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Login — SurveySays</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f4f4f6; font-family: 'Lato', sans-serif; }
    .login-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,.1); width: 100%; max-width: 400px; overflow: hidden; }
    .login-header { background: #550D0E; padding: 36px 32px; text-align: center; }
    .login-seal { width: 56px; height: 56px; background: #C9A84C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: #550D0E; margin: 0 auto 14px; }
    .login-brand { font-family: 'Playfair Display', serif; font-size: 22px; color: #fff; }
    .login-sub { font-size: 11px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .1em; margin-top: 4px; }
    .login-body { padding: 32px; }
    .form-group { margin-bottom: 18px; }
    label { display: block; font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; }
    input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; outline: none; transition: border-color .15s; }
    input:focus { border-color: #7B1213; }
    .btn-submit { width: 100%; padding: 12px; background: #7B1213; color: #fff; border: none; border-radius: 5px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'Lato', sans-serif; transition: background .15s; }
    .btn-submit:hover { background: #550D0E; }
    .error { color: #dc3545; font-size: 12px; margin-top: 5px; }
    .alert { background: #f8d7da; color: #721c24; padding: 10px 14px; border-radius: 5px; margin-bottom: 18px; font-size: 13px; }
    .alert-success { background: #d4edda; color: #155724; padding: 10px 14px; border-radius: 5px; margin-bottom: 18px; font-size: 13px; }
    .forgot-link { display: block; text-align: right; font-size: 12px; color: #7B1213; text-decoration: none; margin-top: -10px; margin-bottom: 18px; }
    .forgot-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="login-card">
  <div class="login-header">
    <div class="login-seal">S</div>
    <div class="login-brand">SurveySays</div>
    <div class="login-sub">Data Collection Platform</div>
  </div>
  <div class="login-body">
    @if(session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="alert">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
      <button type="submit" class="btn-submit">Sign In</button>
    </form>
  </div>
</div>
</body>
</html>
