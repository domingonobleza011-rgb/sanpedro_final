<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>404 - Page Not Found | Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ----- GLOBAL RESETS ----- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            min-height: 100vh;
        }
        body {
            background: #f8fafc;
            font-family: 'Inter', -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ----- ERROR CONTAINER ----- */
        .error-container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            text-align: center;
        }

        /* ----- 404 NUMBER ----- */
        .error-number {
            font-size: 8rem;
            font-weight: 900;
            color: #1b74e4;
            line-height: 1;
            letter-spacing: -8px;
            margin-bottom: 0.25rem;
            position: relative;
            display: inline-block;
        }
        .error-number::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1b74e4, #0a5ecf, #1b74e4);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
            border-radius: 4px;
        }
        @keyframes shimmer {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .error-number .zero {
            color: #0a5ecf;
        }

        /* ----- ERROR ILLUSTRATION (SVG) ----- */
        .error-illustration {
            margin: 0 auto 1.5rem;
            max-width: 280px;
            width: 100%;
        }
        .error-illustration svg {
            width: 100%;
            height: auto;
        }

        /* ----- ERROR TEXT ----- */
        .error-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1c1e21;
            margin-bottom: 0.75rem;
        }
        .error-message {
            font-size: 1rem;
            color: #65676b;
            line-height: 1.8;
            max-width: 460px;
            margin: 0 auto 2rem;
            font-weight: 400;
        }

        /* ----- ACTION BUTTONS ----- */
        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }
        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0.7rem 2rem;
            background: #1b74e4;
            color: #fff;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(27, 116, 228, 0.25);
        }
        .btn-primary-custom:hover {
            background: #0a5ecf;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(27, 116, 228, 0.35);
            color: #fff;
        }
        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0.7rem 2rem;
            background: #fff;
            color: #1c1e21;
            border: 1.5px solid #dee2e6;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .btn-secondary-custom:hover {
            background: #f0f2f5;
            border-color: #bcc0c4;
            transform: translateY(-2px);
        }

        /* ----- FOOTER LINKS ----- */
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            padding-top: 1.5rem;
            border-top: 1px solid #e4e6ea;
        }
        .footer-links a {
            color: #8a8f9a;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        .footer-links a:hover {
            color: #1b74e4;
        }
        .footer-links .divider {
            color: #dee2e6;
            font-weight: 300;
        }

        /* ----- RESPONSIVE ----- */
        @media (max-width: 768px) {
            .error-number {
                font-size: 6rem;
                letter-spacing: -6px;
            }
            .error-title {
                font-size: 1.4rem;
            }
            .error-message {
                font-size: 0.92rem;
                padding: 0 10px;
            }
            .error-illustration {
                max-width: 200px;
            }
        }
        @media (max-width: 576px) {
            body {
                padding: 16px;
            }
            .error-number {
                font-size: 4.5rem;
                letter-spacing: -4px;
            }
            .error-number::after {
                bottom: 4px;
                height: 3px;
            }
            .error-title {
                font-size: 1.2rem;
            }
            .error-message {
                font-size: 0.88rem;
                margin-bottom: 1.5rem;
                padding: 0;
            }
            .error-illustration {
                max-width: 160px;
                margin-bottom: 1rem;
            }
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .action-buttons .btn-primary-custom,
            .action-buttons .btn-secondary-custom {
                width: 100%;
                justify-content: center;
                padding: 0.65rem 1.5rem;
                font-size: 0.85rem;
            }
            .footer-links {
                gap: 0.8rem;
                flex-direction: column;
                align-items: center;
                padding-top: 1.2rem;
            }
            .footer-links .divider {
                display: none;
            }
            .footer-links a {
                font-size: 0.8rem;
            }
        }
        @media (max-width: 400px) {
            .error-number {
                font-size: 3.8rem;
            }
            .error-title {
                font-size: 1rem;
            }
            .error-illustration {
                max-width: 130px;
            }
        }
    </style>
</head>
<body>

<div class="error-container">

    <!-- 404 Number -->
    <div class="error-number">4<span class="zero">0</span>4</div>

    <!-- Illustration -->
    <div class="error-illustration">
        <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="0" y="0" width="200" height="120" fill="none"/>
            <!-- Compass / Location icon -->
            <circle cx="100" cy="55" r="32" stroke="#1b74e4" stroke-width="2.5" opacity="0.15"/>
            <circle cx="100" cy="55" r="24" stroke="#1b74e4" stroke-width="2" opacity="0.25"/>
            <circle cx="100" cy="55" r="16" stroke="#1b74e4" stroke-width="1.5" opacity="0.4"/>
            <path d="M100 35 L92 70 L100 62 L108 70 L100 35Z" fill="#1b74e4" opacity="0.6"/>
            <path d="M100 42 L96 65 L100 60 L104 65 L100 42Z" fill="#1b74e4"/>
            <!-- Small decorative dots -->
            <circle cx="40" cy="30" r="3" fill="#1b74e4" opacity="0.08"/>
            <circle cx="160" cy="25" r="4" fill="#0a5ecf" opacity="0.06"/>
            <circle cx="30" cy="80" r="2.5" fill="#1b74e4" opacity="0.07"/>
            <circle cx="170" cy="70" r="3.5" fill="#0a5ecf" opacity="0.05"/>
            <circle cx="50" cy="95" r="2" fill="#1b74e4" opacity="0.06"/>
            <circle cx="145" cy="95" r="2.5" fill="#0a5ecf" opacity="0.04"/>
            <!-- Arrow line -->
            <path d="M100 70 L100 100" stroke="#1b74e4" stroke-width="2" stroke-dasharray="4 4" opacity="0.3"/>
            <path d="M96 96 L100 102 L104 96" stroke="#1b74e4" stroke-width="2" fill="none" opacity="0.3"/>
        </svg>
    </div>

    <!-- Error Title -->
    <h1 class="error-title">Oops! Page not found</h1>

    <!-- Error Message -->
    <p class="error-message">
        The page you are looking for might have been removed,<br>
        had its name changed, or is temporarily unavailable.
    </p>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="/" class="btn-primary-custom">
            <i class="bi bi-house-door-fill"></i> Back to Home
        </a>
        <a href="javascript:history.back()" class="btn-secondary-custom">
            <i class="bi bi-arrow-left"></i> Go Back
        </a>
    </div>



</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>