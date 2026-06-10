<?php
error_reporting(E_ALL ^ E_WARNING);
require_once __DIR__ . '/classes/security.php';
bmis_session_start();
date_default_timezone_set('Asia/Manila');
include('autoloader.php');
require('classes/conn.php');

$step    = $_GET['step'] ?? 'identify';
$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['find_account'])) {
    bmis_verify_csrf();
    $identity = trim($_POST['identity']);
    $stmt = $conn->prepare(
        "SELECT id_resident, fname, lname, security_question, security_answer
         FROM tbl_resident
         WHERE (email = ? OR phone_number = ?) AND is_archived = 0 LIMIT 1"
    );
    $stmt->execute([$identity, $identity]);
    $resident = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resident) {
        $error = 'No account found with that email or phone number.';
    } elseif (empty($resident['security_question'])) {
        $_SESSION['reset_id']   = $resident['id_resident'];
        $_SESSION['reset_name'] = $resident['fname'] . ' ' . $resident['lname'];
        header('Location: forgot_password.php?step=admin_request');
        exit;
    } else {
        $_SESSION['reset_id']       = $resident['id_resident'];
        $_SESSION['reset_name']     = $resident['fname'] . ' ' . $resident['lname'];
        $_SESSION['reset_question'] = $resident['security_question'];
        $_SESSION['reset_answer']   = $resident['security_answer'];
        header('Location: forgot_password.php?step=security_question');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_answer'])) {
    bmis_verify_csrf();
    if (empty($_SESSION['reset_id'])) { header('Location: forgot_password.php'); exit; }
    $answer = strtolower(trim($_POST['security_answer']));
    $stored = strtolower(trim($_SESSION['reset_answer'] ?? ''));
    if ($answer !== $stored) {
        $error = 'Incorrect answer. Please try again.';
        $step  = 'security_question';
    } else {
        $_SESSION['reset_verified'] = true;
        header('Location: forgot_password.php?step=new_password');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_password'])) {
    bmis_verify_csrf();
    if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_id'])) {
        header('Location: forgot_password.php'); exit;
    }
    $new  = $_POST['new_password'];
    $conf = $_POST['confirm_password'];
    if (strlen($new) < 8) {
        $error = 'Password must be at least 8 characters.'; $step = 'new_password';
    } elseif ($new !== $conf) {
        $error = 'Passwords do not match.'; $step = 'new_password';
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $conn->prepare("UPDATE tbl_resident SET password = ? WHERE id_resident = ?")
             ->execute([$hashed, $_SESSION['reset_id']]);
        unset($_SESSION['reset_id'], $_SESSION['reset_name'], $_SESSION['reset_question'],
              $_SESSION['reset_answer'], $_SESSION['reset_verified']);
        $success = 'Password changed successfully! You may now log in.';
        $step    = 'done';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_admin_reset'])) {
    bmis_verify_csrf();
    if (empty($_SESSION['reset_id'])) { header('Location: forgot_password.php'); exit; }
    $id = $_SESSION['reset_id'];
    $check = $conn->prepare("SELECT id FROM tbl_password_reset_requests WHERE id_resident = ? AND status = 'pending' LIMIT 1");
    $check->execute([$id]);
    if ($check->fetch()) {
        $success = 'You already have a pending reset request. Please wait for admin approval.';
    } else {
        $conn->prepare(
            "INSERT INTO tbl_password_reset_requests (id_resident, full_name, phone_number)
             SELECT ?, CONCAT(fname,' ',lname), phone_number FROM tbl_resident WHERE id_resident = ?"
        )->execute([$id, $id]);
        unset($_SESSION['reset_id'], $_SESSION['reset_name']);
        $success = 'Reset request submitted! Please visit the barangay hall or wait for admin to contact you.';
    }
    $step = 'done';
}

if (empty($step) || $step === 'identify') {
    $step = $_GET['step'] ?? 'identify';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password — Barangay San Pedro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            max-width: 440px;
            width: 100%;
        }

        /* ── White header so logo colours show properly ── */
        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            border-radius: 16px 16px 0 0 !important;
            text-align: center;
            padding: 1.8rem 1.5rem 1.4rem;
        }

        .card-header img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0,0,0,.12);
            margin-bottom: .75rem;
        }

        .card-header h5 {
            color: #0f2d5a;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: .4rem;
        }

        .step-badge {
            display: inline-block;
            background: #0f2d5a;
            color: #fff;
            border-radius: 20px;
            padding: 3px 14px;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .4px;
        }

        /* ── Form elements ── */
        .form-label { font-weight: 600; font-size: .9rem; }
        .input-group-text {
            background: transparent;
            color: #6c757d;
            border-right: none;
        }
        .form-control {
            border-left: none;
            padding: .75rem;
        }
        .form-control:focus { box-shadow: none; border-color: #ced4da; }

        .btn-primary {
            background: #0f2d5a;
            border-color: #0f2d5a;
            font-weight: 600;
            padding: .75rem;
        }
        .btn-primary:hover { background: #1a4480; border-color: #1a4480; }

        .btn-outline-secondary { font-weight: 500; }

        /* Question highlight box */
        .question-box {
            background: #f0f4ff;
            border: 1px solid #c2d4f8;
            border-left: 4px solid #0f2d5a;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: .92rem;
            color: #0f2d5a;
            font-weight: 500;
            margin-bottom: 1.2rem;
        }

        /* Success state */
        .success-circle {
            width: 72px; height: 72px;
            background: #d1fae5;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }

        /* Error alert */
        .error-alert {
            display: flex; align-items: flex-start; gap: 10px;
            background: #fff0f0;
            border: 1px solid #fca5a5;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 1rem;
            animation: shake .4s ease;
        }
        @keyframes shake {
            0%,100%{transform:translateX(0)} 20%{transform:translateX(-5px)}
            40%{transform:translateX(5px)}   60%{transform:translateX(-3px)}
            80%{transform:translateX(3px)}
        }
    </style>
</head>
<body>
<div class="container px-3">
<div class="card mx-auto">

    <!-- White header -->
    <div class="card-header">
        <img src="icons/logo.png" alt="Barangay San Pedro">
        <h5>Forgot Password</h5>
        <span class="step-badge">
            <?php
            $labels = [
                'identify'          => 'Step 1 of 3',
                'security_question' => 'Step 2 of 3',
                'new_password'      => 'Step 3 of 3',
                'admin_request'     => 'Admin Reset',
                'done'              => 'Done',
            ];
            echo $labels[$step] ?? 'Step 1 of 3';
            ?>
        </span>
    </div>

    <div class="card-body p-4">

        <?php if ($error): ?>
        <div class="error-alert">
            <i class="fas fa-exclamation-circle text-danger mt-1 flex-shrink-0"></i>
            <div>
                <strong style="font-size:.88rem;color:#b91c1c;">Error</strong><br>
                <span style="font-size:.84rem;color:#7f1d1d;"><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($success && $step !== 'done'): ?>
        <div class="alert alert-success" style="border-radius:8px;font-size:.9rem">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- STEP 1 -->
        <?php if ($step === 'identify'): ?>
            <p class="text-muted small mb-3">Enter the email address or phone number linked to your account.</p>
            <form method="post">
                <?= bmis_csrf_field() ?>
                <label class="form-label">Email or Phone Number</label>
                <div class="input-group mb-4">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="identity" class="form-control"
                           placeholder="e.g. 09xxxxxxxxx" required autofocus>
                </div>
                <button type="submit" name="find_account" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Find My Account
                </button>
            </form>

        <!-- STEP 2 -->
        <?php elseif ($step === 'security_question'): ?>
            <p class="text-muted small mb-3">Answer your security question to verify your identity.</p>
            <div class="question-box">
                <i class="fas fa-question-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['reset_question'] ?? '') ?>
            </div>
            <form method="post">
                <?= bmis_csrf_field() ?>
                <label class="form-label">Your Answer</label>
                <div class="input-group mb-4">
                    <span class="input-group-text"><i class="fas fa-comment-dots"></i></span>
                    <input type="text" name="security_answer" class="form-control"
                           placeholder="Type your answer" required autofocus>
                </div>
                <button type="submit" name="verify_answer" class="btn btn-primary w-100">
                    <i class="fas fa-shield-alt me-2"></i>Verify Answer
                </button>
            </form>
            <hr>
            <p class="text-center small text-muted mb-1">Don't remember your answer?</p>
            <a href="forgot_password.php?step=admin_request"
               class="btn btn-outline-secondary w-100">Request Admin Reset Instead</a>

        <!-- STEP 3 -->
        <?php elseif ($step === 'new_password'): ?>
            <p class="text-muted small mb-3">Set your new password. Minimum 8 characters.</p>
            <form method="post">
                <?= bmis_csrf_field() ?>
                <label class="form-label">New Password</label>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="new_password" id="newpw" class="form-control"
                           minlength="8" placeholder="Minimum 8 characters" required autofocus>
                </div>
                <label class="form-label">Confirm New Password</label>
                <div class="input-group mb-1">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="confirm_password" id="confpw" class="form-control"
                           minlength="8" placeholder="Re-enter password" required>
                </div>
                <div id="pw-match" class="form-text mb-3"></div>
                <button type="submit" name="set_password" class="btn btn-primary w-100">
                    <i class="fas fa-save me-2"></i>Save New Password
                </button>
            </form>
            <script>
            document.getElementById('confpw').addEventListener('input', function(){
                var m = document.getElementById('pw-match');
                if (this.value === document.getElementById('newpw').value) {
                    m.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i><span class="text-success">Passwords match</span>';
                } else {
                    m.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i><span class="text-danger">Passwords do not match</span>';
                }
            });
            </script>

        <!-- Admin Request -->
        <?php elseif ($step === 'admin_request'): ?>
            <p class="text-muted small mb-3">Submit a reset request to the barangay admin. You can visit the barangay hall to claim your temporary password.</p>
            <div class="alert alert-warning" style="border-radius:8px;border-left:4px solid #f59e0b;font-size:.9rem">
                <i class="fas fa-info-circle me-2"></i>
                Admin will set a <strong>temporary password</strong> for your account.
                You must change it upon next login.
            </div>
            <form method="post">
                <?= bmis_csrf_field() ?>
                <button type="submit" name="request_admin_reset" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i>Submit Reset Request
                </button>
            </form>

        <!-- Done -->
        <?php elseif ($step === 'done'): ?>
            <div class="text-center py-2">
                <div class="success-circle">
                    <i class="fas fa-check text-success" style="font-size:1.8rem"></i>
                </div>
                <h6 class="fw-bold text-success mb-2">Success!</h6>
                <p class="text-muted small"><?= htmlspecialchars($success ?: 'Your request has been submitted.') ?></p>
                <a href="index.php" class="btn btn-primary w-100 mt-1">
                    <i class="fas fa-sign-in-alt me-2"></i>Back to Login
                </a>
            </div>
        <?php endif; ?>

        <?php if ($step !== 'done'): ?>
        <div class="text-center mt-3">
            <a href="index.php" class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i>Back to Login
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>