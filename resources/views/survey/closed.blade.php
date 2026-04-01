<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Survey Not Available</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:'Lato',sans-serif;background:#f5f5f7;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
    .card{background:#fff;border-radius:10px;padding:48px 40px;text-align:center;max-width:400px;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    h1{font-family:'Playfair Display',serif;font-size:22px;color:#550D0E;margin-bottom:12px}
    p{font-size:14px;color:#666;line-height:1.6}
  </style>
</head>
<body>
<div class="card">
  <h1>Survey Not Available</h1>
  <p><strong>{{ $survey->title }}</strong></p>
  <p style="margin-top:10px">This survey is not currently accepting responses. Please contact the researcher for more information.</p>
</div>
</body>
</html>
