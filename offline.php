<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offline — Barangay San Pedro BMIS</title>
  <meta name="theme-color" content="#0f2d5a">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Roboto, sans-serif;
      background: #0f2d5a;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      text-align: center;
      padding: 2rem;
    }
    .container { max-width: 400px; }
    .icon { font-size: 4rem; margin-bottom: 1.5rem; }
    h1 { font-size: 1.6rem; font-weight: 700; margin-bottom: .75rem; }
    p  { font-size: .95rem; opacity: .8; line-height: 1.6; margin-bottom: 2rem; }
    img {
      width: 72px; height: 72px; border-radius: 16px;
      margin-bottom: 1.5rem;
    }
    .btn {
      display: inline-block;
      background: #c9943a;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: .75rem 2rem;
      font-size: .95rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
    }
    .btn:hover { background: #b8832f; }
  </style>
</head>
<body>
  <div class="container">
    <img src="/icons/pwa/icon-192x192.png"
         onerror="this.style.display='none'"
         alt="BMIS Logo">
    <div class="icon">📡</div>
    <h1>You're Offline</h1>
    <p>No internet connection detected. Please check your network and try again.</p>
    <button class="btn" onclick="window.location.reload()">Try Again</button>
  </div>
</body>
</html>