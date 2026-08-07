<?php
/**
 * setup_security_question.php
 * Resident sets their security question after login.
 * Link to this from the resident dashboard / profile page.
 */
error_reporting(E_ALL ^ E_WARNING);
require_once __DIR__ . '/classes/security.php';
bmis_session_start();
$userdetails = bmis_require_resident();
date_default_timezone_set('Asia/Manila');
include('autoloader.php');
require('classes/conn.php');

$error   = '';
$success = '';

$questions = [
    "What is the name of your elementary school?",
    "What is your mother's maiden name?",
    "What is the name of your childhood best friend?",
    "What was the name of your first pet?",
    "What street did you grow up on?",
    "What is your oldest sibling's middle name?",
    "What city were you born in?",
    "What was your childhood nickname?",
];

// Fetch existing
$stmt = $conn->prepare("SELECT security_question, security_answer FROM tbl_resident WHERE id_resident = ?");
$stmt->execute([$userdetails['id_resident']]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_security_q'])) {
    bmis_verify_csrf();
    $question = $_POST['security_question'];
    $answer   = strtolower(trim($_POST['security_answer']));
    $confirm  = strtolower(trim($_POST['confirm_answer']));

    if (!in_array($question, $questions)) {
        $error = 'Invalid question selected.';
    } elseif (strlen($answer) < 2) {
        $error = 'Answer is too short.';
    } elseif ($answer !== $confirm) {
        $error = 'Answers do not match.';
    } else {
        $stmt = $conn->prepare("UPDATE tbl_resident SET security_question = ?, security_answer = ? WHERE id_resident = ?");
        $stmt->execute([$question, $answer, $userdetails['id_resident']]);
        $success = 'Security question saved successfully.';
        $current['security_question'] = $question;
        $current['security_answer']   = $answer;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Security Question | Barangay San Pedro</title>
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
        .security-hero {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            padding: 2rem 1.5rem;
            border-radius: 0 0 40px 40px;
            color: #fff;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .security-hero h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .security-hero p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        .security-hero i {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 576px) {
            .security-hero {
                padding: 1.5rem 1rem;
                border-radius: 0 0 24px 24px;
            }
            .security-hero h1 {
                font-size: 1.5rem;
            }
            .security-hero i {
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

        /* ----- FORM ELEMENTS ----- */
        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #dee2e6;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafbfc;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1b74e4;
            box-shadow: 0 0 0 3px rgba(27, 116, 228, 0.12);
            background: #fff;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23656b76' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
            padding-right: 36px;
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

        /* Current Question Alert */
        .current-question-box {
            background: #e7f3ff;
            border: 1px solid #b6d4fe;
            border-radius: 12px;
            padding: 0.9rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .current-question-box i {
            color: #1b74e4;
            font-size: 1.2rem;
            margin-top: 2px;
        }
        .current-question-box .q-text {
            font-weight: 600;
            color: #1c1e21;
        }
        .current-question-box .q-label {
            font-size: 0.78rem;
            color: #65676b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

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

        .btn-back {
            border-radius: 14px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            border: 1.5px solid #dee2e6;
            color: #65676b;
            background: transparent;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-back:hover {
            background: #f0f2f5;
            border-color: #bcc0c4;
            color: #1c1e21;
        }

        /* ----- ALERT MESSAGES ----- */
        .alert-custom {
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            border: none;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
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
        .alert-custom-info {
            background: #e7f3ff;
            color: #084298;
            border-left: 5px solid #1b74e4;
        }

        /* ----- DIVIDER ----- */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1rem 0;
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

        /* ----- RESPONSIVE ----- */
        @media (max-width: 576px) {
            .form-card {
                padding: 1rem 0.9rem;
            }
            .btn-submit {
                font-size: 0.95rem;
                padding: 0.75rem 1rem;
            }
            .btn-back {
                font-size: 0.9rem;
                padding: 0.65rem 1rem;
            }
            .form-control, .form-select {
                font-size: 0.9rem;
                padding: 0.6rem 0.9rem;
            }
            .current-question-box {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
            }
        }

        /* ----- MATCHING ANSWER INDICATOR ----- */
        .match-indicator {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 4px 0;
            min-height: 28px;
        }
        .match-indicator.success { color: #198754; }
        .match-indicator.error { color: #dc3545; }
    </style>
</head>
<body>


<!-- NAVBAR -->
<?php include __DIR__ . '/resident_navbar.php'; ?>

        <!-- HERO -->
    <div class="security-hero">
        <i class="bi bi-shield-check"></i>
        <h1>Security Question</h1>
        <p>Set up a security question to help recover your account if you forget your password</p>
    </div>
<div class="page-wrapper">


    <!-- FORM CARD -->
    <div class="form-card">

        <?php if ($error): ?>
            <div class="alert-custom alert-custom-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert-custom alert-custom-success">
                <i class="bi bi-check-circle-fill"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($current['security_question'])): ?>
            <div class="current-question-box">
                <i class="bi bi-info-circle-fill"></i>
                <div>
                    <div class="q-label">Current Security Question</div>
                    <div class="q-text"><?= htmlspecialchars($current['security_question']) ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-custom alert-custom-info">
                <i class="bi bi-info-circle-fill"></i>
                You haven't set a security question yet. Choose one below.
            </div>
        <?php endif; ?>

        <p class="text-muted small mb-3">
            <i class="bi bi-shield-exclamation me-1"></i>
            This question will be used to verify your identity if you forget your password.
            Please choose a question and answer that you will remember.
        </p>

        <form method="post" id="securityForm" onsubmit="return validateForm()">
            <?= bmis_csrf_field() ?>

            <!-- Select Question -->
            <div class="mb-3">
                <label class="form-label">Select a Security Question</label>
                <select name="security_question" class="form-select" id="securityQuestion" required>
                    <option value="">— Choose a question —</option>
                    <?php foreach ($questions as $q): ?>
                        <option value="<?= htmlspecialchars($q) ?>" 
                            <?= ($current['security_question'] ?? '') === $q ? 'selected' : '' ?>>
                            <?= htmlspecialchars($q) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Answer -->
            <div class="mb-3">
                <label class="form-label">Your Answer</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-pencil"></i></span>
                    <input type="text" name="security_answer" id="securityAnswer" 
                           class="input-field" required
                           placeholder="Type your answer (not case-sensitive)">
                </div>
            </div>

            <!-- Confirm Answer -->
            <div class="mb-3">
                <label class="form-label">Confirm Answer</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-check-circle"></i></span>
                    <input type="text" name="confirm_answer" id="confirmAnswer" 
                           class="input-field" required
                           placeholder="Type your answer again">
                </div>
                <div id="matchIndicator" class="match-indicator"></div>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="save_security_q" class="btn-submit">
                <i class="bi bi-save me-2"></i>Save Security Question
            </button>


        </form>
    </div>

</div><!-- end page-wrapper -->

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

    // ── Answer Match Validation ──────────────────────────────────────
    const answerInput = document.getElementById('securityAnswer');
    const confirmInput = document.getElementById('confirmAnswer');
    const matchIndicator = document.getElementById('matchIndicator');

    function checkAnswerMatch() {
        const answer = answerInput.value.trim();
        const confirm = confirmInput.value.trim();

        if (confirm.length === 0) {
            matchIndicator.textContent = '';
            matchIndicator.className = 'match-indicator';
            return;
        }

        if (answer.toLowerCase() === confirm.toLowerCase()) {
            matchIndicator.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Answers match';
            matchIndicator.className = 'match-indicator success';
        } else {
            matchIndicator.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Answers do not match';
            matchIndicator.className = 'match-indicator error';
        }
    }

    answerInput.addEventListener('keyup', checkAnswerMatch);
    confirmInput.addEventListener('keyup', checkAnswerMatch);

    // ── Form Validation ──────────────────────────────────────────────
    function validateForm() {
        const question = document.getElementById('securityQuestion');
        const answer = document.getElementById('securityAnswer');
        const confirm = document.getElementById('confirmAnswer');

        // Check if a question is selected
        if (question.value === '') {
            alert('Please select a security question.');
            question.focus();
            return false;
        }

        // Check if answer is long enough
        if (answer.value.trim().length < 2) {
            alert('Answer must be at least 2 characters long.');
            answer.focus();
            return false;
        }

        // Check if answers match
        if (answer.value.toLowerCase().trim() !== confirm.value.toLowerCase().trim()) {
            alert('Answers do not match. Please type the same answer in both fields.');
            confirm.focus();
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