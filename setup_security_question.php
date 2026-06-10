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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Security Question — Barangay San Pedro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
</head>
<!-- DESKTOP NAVBAR (Hidden on Mobile) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-md-block shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="resident_homepage.php">
            <i class="bi bi-building-fill me-2"></i> Barangay San Pedro
        </a>
        <div class="d-flex ms-auto">
            <a href="resident_homepage.php" class="btn btn-primary me-1"><i class="bi bi-house-door-fill me-1"></i> Home</a>
            <a href="resident_announcement.php" class="btn btn-primary me-1"><i class="bi bi-megaphone-fill me-1"></i> Announcements</a>
            <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-person-badge me-1"></i> Profile</a>
            <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-shield-lock me-1"></i> Password</a>
            <a href="logout.php" class="btn btn-danger ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- MOBILE BOTTOM NAV (Hidden on Desktop) -->
<div class="mobile-bottom-nav d-md-none">
    <a href="resident_homepage.php" class="nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>
    <a href="resident_announcement.php" class="nav-item">
        <i class="bi bi-megaphone-fill"></i>
        <span>News</span>
    </a>
    <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-person-badge"></i>
        <span>Profile</span>
    </a>
    <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-shield-lock"></i>
        <span>Pass</span>
    </a>
    <a href="logout.php" class="nav-item text-danger">
        <i class="bi bi-box-arrow-right"></i>
        <span>Exit</span>
    </a>
</div>
<body class="bg-light">
<div class="content-wrapper p-4">
    <div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Security Question</h5>
            </div>
            <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($current['security_question'])): ?>
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Current question: <strong><?= htmlspecialchars($current['security_question']) ?></strong>
                </div>
            <?php endif; ?>

            <p class="text-muted small">This question will be used to verify your identity if you forget your password.</p>

            <form method="post">
                <?= bmis_csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select a Security Question</label>
                    <select name="security_question" class="form-select" required>
                        <option value="">— Choose a question —</option>
                        <?php foreach ($questions as $q): ?>
                            <option value="<?= htmlspecialchars($q) ?>"
                                <?= ($current['security_question'] ?? '') === $q ? 'selected' : '' ?>>
                                <?= htmlspecialchars($q) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Your Answer</label>
                    <input type="text" name="security_answer" class="form-control" required
                           placeholder="Type your answer (not case-sensitive)">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirm Answer</label>
                    <input type="text" name="confirm_answer" class="form-control" required
                           placeholder="Type your answer again">
                </div>
                <button type="submit" name="save_security_q" class="btn btn-primary w-100">
                    <i class="fas fa-save me-2"></i>Save Security Question
                </button>
            </form>
            </div>
        </div>
    </div>
    </div>
</div>
<?php include('dashboard_sidebar_end.php'); ?>
</body>
</html>