<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Forgot Password — SurveySays</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f4f4f6; font-family: 'Lato', sans-serif; }
    .card { background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,.1); width: 100%; max-width: 400px; overflow: hidden; }
    .card-header { background: #550D0E; padding: 36px 32px; text-align: center; }
    .seal { width: 56px; height: 56px; background: #C9A84C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: #550D0E; margin: 0 auto 14px; }
    .brand { font-family: 'Playfair Display', serif; font-size: 22px; color: #fff; }
    .sub { font-size: 11px; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .1em; margin-top: 4px; }
    .body { padding: 32px; }
    .desc { font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 20px; }
    .form-group { margin-bottom: 18px; }
    label { display: block; font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; }
    input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; outline: none; transition: border-color .15s; }
    input:focus { border-color: #7B1213; }
    .btn { width: 100%; padding: 12px; background: #7B1213; color: #fff; border: none; border-radius: 5px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'Lato', sans-serif; transition: background .15s; }
    .btn:hover { background: #550D0E; }
    .error { color: #dc3545; font-size: 12px; margin-top: 5px; }
    .alert-error { background: #f8d7da; color: #721c24; padding: 10px 14px; border-radius: 5px; margin-bottom: 18px; font-size: 13px; }
    .alert-success { background: #d4edda; color: #155724; padding: 10px 14px; border-radius: 5px; margin-bottom: 18px; font-size: 13px; }
    .back-link { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #7B1213; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <div class="seal">S</div>
    <div class="brand">SurveySays</div>
    <div class="sub">Reset Your Password</div>
  </div>
  <div class="body">
    @if(session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif
    <p class="desc">Enter your email address and we will send you a link to reset your password.</p>
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')<div class="error">{{ $message }}</div>@enderror
      </div>
      <button type="submit" class="btn">Send Reset Link</button>
    </form>
    <a href="{{ route('login') }}" class="back-link">← Back to Sign In</a>
  </div>
</div>
</body>
</html>
