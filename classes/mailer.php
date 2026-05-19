<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// ── Gmail config ──────────────────────────────────────
define('MAIL_FROM',     'domingonobleza011@gmail.com');   // ← your Gmail
define('MAIL_PASSWORD', 'iqxq cgac fpjh yjzk');       // ← App Password (no spaces)
define('MAIL_NAME',     'Barangay San Pedro BMIS');
// ─────────────────────────────────────────────────────

function sendGmail($to_email, $to_name, $subject, $body) {
    if (empty($to_email)) return false;  // skip if no email

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_FROM;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(MAIL_FROM, MAIL_NAME);
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = emailTemplate($subject, $body);
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// ── Reusable HTML email template ─────────────────────
function emailTemplate($title, $content) {
    return "
    <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;
                border:1px solid #dee2e6;border-radius:10px;overflow:hidden;'>
        <div style='background:#1a472a;padding:24px;text-align:center;'>
            <h2 style='color:#fff;margin:0;'>Barangay San Pedro BMIS</h2>
        </div>
        <div style='padding:30px;background:#fff;'>
            <h3 style='color:#1a472a;'>{$title}</h3>
            {$content}
            <hr style='margin:24px 0;border:none;border-top:1px solid #dee2e6;'>
            <p style='color:#6c757d;font-size:12px;'>
                This is an automated message from Barangay San Pedro BMIS.<br>
                Please do not reply to this email.
            </p>
        </div>
        <div style='background:#f8f9fa;padding:14px;text-align:center;'>
            <small style='color:#6c757d;'>© " . date('Y') . " Barangay San Pedro. All rights reserved.</small>
        </div>
    </div>";
}