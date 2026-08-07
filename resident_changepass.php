<?php 
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php'); 
error_reporting(E_ALL ^ E_WARNING); 
require('classes/resident.class.php');

$userdetails = $residentbmis->get_userdata();
$residentbmis->resident_changepass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Change Password | Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

    <style>
        /* ----- GLOBAL RESETS ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            padding-bottom: 85px; /* space for mobile nav */
        }
        @media (min-width: 768px) {
            body { padding-bottom: 0; }
        }

        /* ----- PAGE WRAPPER ----- */
        .page-wrapper {
            max-width: 780px;
            width: 100%;
            margin: 0 auto;
            padding: 12px 12px 0;
        }

        /* ----- HERO HEADER ----- */
        .password-hero {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            padding: 2rem 1.5rem;
            border-radius: 0 0 40px 40px;
            color: #fff;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .password-hero h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .password-hero p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        .password-hero i {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 576px) {
            .password-hero {
                padding: 1.5rem 1rem;
                border-radius: 0 0 24px 24px;
            }
            .password-hero h1 {
                font-size: 1.5rem;
            }
            .password-hero i {
                font-size: 2.2rem;
            }
        }

        /* ----- FORM CARD ----- */
        .form-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 2rem 2rem 1.75rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 576px) {
            .form-card {
                padding: 1.25rem 1rem;
            }
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #4b4f56;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.3rem;
        }

        /* ----- INPUT WITH ICON ----- */
        .input-group-custom {
            display: flex;
            width: 100%;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 1.5px solid #dee2e6;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafbfc;
        }
        .input-group-custom:focus-within {
            border-color: #1b74e4;
            box-shadow: 0 0 0 3px rgba(27, 116, 228, 0.12);
            background: #fff;
        }
        .input-group-custom .input-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 48px;
            background: #f0f2f5;
            color: #65676b;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .input-group-custom .input-field {
            flex: 1;
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            outline: none;
            background: transparent;
            font-family: inherit;
            min-width: 0;
        }
        .input-group-custom .input-field::placeholder {
            color: #a8afb8;
        }
        .input-group-custom .toggle-password {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            cursor: pointer;
            color: #8a8f9a;
            font-size: 1rem;
            transition: color 0.2s;
            background: transparent;
            border: none;
            padding: 0 12px;
        }
        .input-group-custom .toggle-password:hover {
            color: #1b74e4;
        }

        /* Password match message */
        .password-match-msg {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 4px 0;
        }
        .password-match-msg.success { color: #198754; }
        .password-match-msg.error { color: #dc3545; }

        /* ----- BUTTONS ----- */
        .btn-submit {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            color: #fff;
            padding: 0.9rem 1.5rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.05rem;
            width: 100%;
            border: none;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(27, 116, 228, 0.3);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(27, 116, 228, 0.35);
            color: #fff;
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-outline-custom {
            border-radius: 14px;
            padding: 0.9rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            border: 1.5px solid #1b74e4;
            color: #1b74e4;
            background: transparent;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-outline-custom:hover {
            background: #1b74e4;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(27, 116, 228, 0.25);
        }

        /* ----- ALERT MESSAGES ----- */
        .alert-custom {
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            border: none;
            margin-bottom: 1.25rem;
        }
        .alert-custom-success {
            background: #d1e7dd;
            color: #0f5132;
            border-left: 5px solid #198754;
        }
        .alert-custom-danger {
            background: #f8d7da;
            color: #842029;
            border-left: 5px solid #dc3545;
        }

        /* ----- BACK TO TOP ----- */
        .top-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #1b74e4;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: opacity 0.2s, transform 0.2s;
            z-index: 999;
        }
        .top-link.hide {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.8);
        }
        .top-link svg {
            fill: #fff;
            width: 20px;
            height: 12px;
        }
        .top-link:hover {
            background: #0a5ecf;
        }

        /* ----- DIVIDER ----- */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.25rem 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        .divider span {
            padding: 0 1rem;
            color: #8a8f9a;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* ----- RESPONSIVE ----- */
        @media (max-width: 576px) {
            .form-card {
                padding: 1rem 0.9rem;
            }
            .btn-submit {
                font-size: 0.95rem;
                padding: 0.75rem 1rem;
            }
            .btn-outline-custom {
                font-size: 0.9rem;
                padding: 0.75rem 1rem;
            }
            .input-group-custom .input-field {
                font-size: 0.9rem;
                padding: 0.65rem 0.8rem;
            }
            .input-group-custom .input-icon {
                min-width: 40px;
                font-size: 0.9rem;
            }
            .input-group-custom .toggle-password {
                min-width: 38px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<!-- BACK TO TOP BUTTON -->
<a class="top-link hide" id="js-top" href="#">
    <svg viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg>
    <span class="visually-hidden">Back to top</span>
</a>

<!-- NAVBAR -->
<?php include __DIR__ . '/resident_navbar.php'; ?>
    <!-- HERO -->
    <div class="password-hero">
        <i class="bi bi-shield-lock-fill"></i>
        <h1>Change Password</h1>
        <p>Keep your account secure by updating your password regularly</p>
    </div>
<div class="page-wrapper">



    <!-- FORM CARD -->
    <div class="form-card">

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert-custom alert-custom-success">
                <i class="bi bi-check-circle-fill me-2"></i> Password changed successfully!
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert-custom alert-custom-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Current password is incorrect or there was an error.
            </div>
        <?php endif; ?>

        <form method="post" id="passwordForm" onsubmit="return validateForm()">
            <!-- Current Password -->
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                    <input class="input-field" type="password" id="oldPassword" 
                           name="oldpassword" placeholder="Enter current password" required>
                    <button type="button" class="toggle-password" onclick="toggleVisibility('oldPassword', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <input type="hidden" name="oldpasswordverify" value="<?= $userdetails['password'] ?? '' ?>">
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-key-fill"></i></span>
                    <input class="input-field" id="newPassword" type="password" 
                           name="newpassword" placeholder="Enter new password" required>
                    <button type="button" class="toggle-password" onclick="toggleVisibility('newPassword', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-person-lock-fill"></i></span>
                    <input class="input-field" id="confirmPassword" type="password" 
                           name="checkpassword" placeholder="Confirm new password" required>
                    <button type="button" class="toggle-password" onclick="toggleVisibility('confirmPassword', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="passwordMatchMsg" class="password-match-msg"></div>
            </div>

            <!-- Password Strength Indicator -->
            <div class="mb-3" id="strengthContainer" style="display:none;">
                <label class="form-label">Password Strength</label>
                <div class="progress" style="height: 6px; border-radius: 10px;">
                    <div id="strengthBar" class="progress-bar" role="progressbar" 
                         style="width: 0%; border-radius: 10px; transition: width 0.3s;"></div>
                </div>
                <small id="strengthText" class="text-muted mt-1 d-block"></small>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 mt-4">
                <button class="btn-submit" type="submit" name="resident_changepass">
                    <i class="bi bi-check-circle-fill me-2"></i> Update Password
                </button>
            </div>

            <!-- Divider -->
            <div class="divider">
                <span>or</span>
            </div>

            <!-- Security Question Link -->
            <a href="setup_security_question.php" class="btn-outline-custom">
                <i class="bi bi-key-fill"></i> Set Up Security Question
            </a>
        </form>
    </div>

</div><!-- end page-wrapper -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── Back-to-top ──────────────────────────────────────────────────
    const topBtn = document.getElementById('js-top');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            topBtn.classList.remove('hide');
        } else {
            topBtn.classList.add('hide');
        }
    });
    topBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ── Toggle Password Visibility ──────────────────────────────────
    function toggleVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // ── Password Match Validation ──────────────────────────────────
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const matchMsg = document.getElementById('passwordMatchMsg');

    function checkMatch() {
        const newVal = newPassword.value;
        const confirmVal = confirmPassword.value;
        
        if (confirmVal.length === 0) {
            matchMsg.textContent = '';
            matchMsg.className = 'password-match-msg';
            return;
        }
        
        if (newVal === confirmVal) {
            matchMsg.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Passwords match';
            matchMsg.className = 'password-match-msg success';
        } else {
            matchMsg.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Passwords do not match';
            matchMsg.className = 'password-match-msg error';
        }
    }

    newPassword.addEventListener('keyup', checkMatch);
    confirmPassword.addEventListener('keyup', checkMatch);

    // ── Password Strength Indicator ──────────────────────────────────
    const strengthContainer = document.getElementById('strengthContainer');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = '';

        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;

        const percentages = [0, 16, 33, 50, 66, 83, 100];
        const percent = percentages[Math.min(score, 6)];

        const messages = [
            'Too weak',
            'Very weak',
            'Weak',
            'Fair',
            'Good',
            'Strong',
            'Very strong'
        ];
        const colors = [
            '#dc3545', '#dc3545', '#f59f00', '#f59f00', 
            '#fd7e14', '#198754', '#198754'
        ];

        return { score: Math.min(score, 6), percent, message: messages[Math.min(score, 6)], color: colors[Math.min(score, 6)] };
    }

    newPassword.addEventListener('keyup', function() {
        const pwd = this.value;
        if (pwd.length === 0) {
            strengthContainer.style.display = 'none';
            return;
        }

        strengthContainer.style.display = 'block';
        const result = checkPasswordStrength(pwd);
        strengthBar.style.width = result.percent + '%';
        strengthBar.style.background = result.color;
        strengthText.textContent = 'Strength: ' + result.message;
        strengthText.style.color = result.color;
    });

    // ── Form Validation ────────────────────────────────────────────
    function validateForm() {
        const oldPwd = document.getElementById('oldPassword');
        const newPwd = document.getElementById('newPassword');
        const confirmPwd = document.getElementById('confirmPassword');
        const matchMsg = document.getElementById('passwordMatchMsg');

        // Check if passwords match
        if (newPwd.value !== confirmPwd.value) {
            matchMsg.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Passwords do not match';
            matchMsg.className = 'password-match-msg error';
            confirmPwd.focus();
            return false;
        }

        // Check if new password is different from current
        if (newPwd.value === oldPwd.value) {
            alert('New password must be different from your current password.');
            newPwd.focus();
            return false;
        }

        // Check minimum length
        if (newPwd.value.length < 6) {
            alert('New password must be at least 6 characters long.');
            newPwd.focus();
            return false;
        }

        return true;
    }

    // ── Smooth Scroll for anchor links ─────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
</body>
</html>