<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline — Barangay San Pedro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0f2d5a 0%, #1a4480 60%, #2b5ea7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 40px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,0.25);
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 24px;
            display: block;
            border: 3px solid #e8eef7;
        }

        .icon-offline {
            font-size: 3.5rem;
            margin-bottom: 16px;
            display: block;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f2d5a;
            margin-bottom: 10px;
        }

        p {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .btn-retry {
            display: inline-block;
            background: linear-gradient(135deg, #0f2d5a, #2b5ea7);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 32px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.2s;
            margin-bottom: 16px;
            width: 100%;
        }

        .btn-retry:hover { opacity: 0.85; }

        .divider {
            border: none;
            height: 1px;
            background: #e8ecf0;
            margin: 24px 0;
        }

        .cached-hint {
            font-size: 0.8rem;
            color: #a0aec0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #dc2626;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .back-link {
            font-size: 0.82rem;
            color: #2b5ea7;
            text-decoration: none;
            margin-top: 12px;
            display: inline-block;
        }

        @media (max-width: 480px) {
            .card { padding: 36px 24px; }
            h1 { font-size: 1.35rem; }
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="/icons/pwa/icon-192x192.png" alt="Barangay San Pedro" class="logo" 
             onerror="this.style.display='none'">

        <span class="icon-offline">📡</span>

        <h1>You're Offline</h1>
        <p>
            It looks like you've lost your internet connection.<br>
            Please check your network and try again.
        </p>

        <button class="btn-retry" onclick="retryConnection()">
            ↻ &nbsp;Try Again
        </button>

        <hr class="divider">

        <div class="cached-hint">
            <span class="status-dot"></span>
            Some pages may still be available from cache
        </div>

        <a href="javascript:history.back()" class="back-link">← Go back</a>
    </div>

    <script>
        function retryConnection() {
            window.location.reload();
        }

        // Auto-retry when connection is restored
        window.addEventListener('online', function() {
            window.location.reload();
        });

        // Show connection status change
        window.addEventListener('offline', function() {
            document.querySelector('h1').textContent = "Still Offline";
        });
    </script>
</body>
</html>