<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Thank You — {{ $survey->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Lato',sans-serif;background:#f5f5f7;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px}
    .card{background:#fff;border-radius:10px;padding:48px 40px;text-align:center;max-width:460px;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .check{width:64px;height:64px;background:#d4edda;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px}
    .check svg{width:32px;height:32px;stroke:#155724}
    h1{font-family:'Playfair Display',serif;font-size:26px;color:#222;margin-bottom:10px}
    p{font-size:15px;color:#666;line-height:1.6}
  </style>
</head>
<body>
<div class="card">
  <div class="check">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  </div>
  <h1>Thank You!</h1>
  <p>Your response to <strong style="color:#7B1213">{{ $survey->title }}</strong> has been recorded successfully.</p>
  <p style="margin-top:12px;font-size:13px;color:#aaa">You may now close this window.</p>
</div>
</body>
</html>
