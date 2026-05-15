<?php
    require('classes/main.class.php');
    $bmis->admin_changepass();
    $userdetails = $bmis->get_userdata();
    include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>BMIS - Change Password</title>

    <!-- SB Admin 2 & Bootstrap Core -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../BarangaySystem/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --green-deep:    #1a4d2e;
            --green-mid:     #2e7d4f;
            --green-bright:  #3dba6f;
            --green-glow:    rgba(61, 186, 111, 0.18);
            --gold:          #c9a84c;
            --ink:           #0f1f17;
            --mist:          #f4f8f5;
            --border:        rgba(46, 125, 79, 0.18);
            --card-shadow:   0 20px 60px rgba(26,77,46,.13), 0 2px 8px rgba(26,77,46,.07);
        }

        body {
            background: var(--mist);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        /* ── Centered page wrapper ───────────────────── */
        .cp-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        /* ── Card ────────────────────────────────────── */
        .cp-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            position: relative;
        }

        /* decorative top bar */
        .cp-card::before {
            content: '';
            display: block;
            height: 5px;
            background: linear-gradient(90deg, var(--green-deep), var(--green-bright), var(--gold));
        }

        /* ── Header ──────────────────────────────────── */
        .cp-header {
            background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%);
            padding: 36px 40px 28px;
            position: relative;
            overflow: hidden;
        }
        .cp-header::after {
            content: '';
            position: absolute;
            right: -30px; top: -30px;
            width: 140px; height: 140px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
        }
        .cp-header .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            color: #d4f5e3;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 50px;
            margin-bottom: 14px;
        }
        .cp-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: #fff;
            margin: 0 0 6px;
            line-height: 1.2;
        }
        .cp-header p {
            font-size: 13px;
            color: rgba(255,255,255,.68);
            margin: 0;
            line-height: 1.5;
        }

        /* shield icon */
        .shield-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.22);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            color: #fff;
            margin-bottom: 18px;
            backdrop-filter: blur(4px);
        }

        /* ── Body ────────────────────────────────────── */
        .cp-body {
            padding: 36px 40px 32px;
        }

        /* ── Field groups ────────────────────────────── */
        .field-group {
            margin-bottom: 22px;
        }
        .field-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--green-deep);
            margin-bottom: 8px;
        }
        .field-wrap {
            position: relative;
        }
        .field-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9bb8a6;
            font-size: 14px;
            pointer-events: none;
        }
        .field-wrap input {
            width: 100%;
            padding: 12px 42px 12px 40px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--ink);
            background: var(--mist);
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
            box-sizing: border-box;
        }
        .field-wrap input:focus {
            border-color: var(--green-bright);
            background: #fff;
            box-shadow: 0 0 0 3px var(--green-glow);
        }
        .field-wrap input::placeholder {
            color: #b0c4b8;
        }
        /* eye toggle */
        .field-wrap .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9bb8a6;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            line-height: 1;
            transition: color .2s;
        }
        .field-wrap .eye-btn:hover { color: var(--green-mid); }

        /* ── Divider ─────────────────────────────────── */
        .cp-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 6px 0 22px;
            color: #b0c4b8;
            font-size: 11px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .cp-divider::before, .cp-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Strength meter ──────────────────────────── */
        .strength-wrap { margin-top: 8px; }
        .strength-bar {
            display: flex; gap: 4px; margin-bottom: 4px;
        }
        .strength-bar span {
            flex: 1; height: 3px; border-radius: 10px;
            background: #dde9e3;
            transition: background .3s;
        }
        .strength-label {
            font-size: 11px;
            color: #9bb8a6;
            font-weight: 500;
            min-height: 16px;
        }

        /* strength states */
        .strength-1 span:nth-child(1)                        { background: #e74c3c; }
        .strength-2 span:nth-child(-n+2)                     { background: #f39c12; }
        .strength-3 span:nth-child(-n+3)                     { background: var(--gold); }
        .strength-4 span                                     { background: var(--green-bright); }

        /* ── Submit button ───────────────────────────── */
        .btn-update {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--green-mid) 0%, var(--green-bright) 100%);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .3px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .15s, box-shadow .15s, filter .2s;
            box-shadow: 0 4px 16px rgba(46,125,79,.28);
        }
        .btn-update:hover {
            filter: brightness(1.06);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(46,125,79,.35);
        }
        .btn-update:active {
            transform: translateY(0);
            filter: brightness(.96);
        }

        /* ── Footer link ─────────────────────────────── */
        .cp-footer {
            text-align: center;
            margin-top: 20px;
        }
        .cp-footer a {
            font-size: 13px;
            color: #7a9e8a;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color .2s;
        }
        .cp-footer a:hover { color: var(--green-mid); }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 520px) {
            .cp-header, .cp-body { padding-left: 24px; padding-right: 24px; }
        }
    </style>
</head>
<body id="page-top">

<div class="cp-wrapper">
    <div class="cp-card">

        <!-- ── Header ── -->
        <div class="cp-header">
            <div class="shield-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="badge-pill"><i class="fas fa-lock"></i> Security Settings</div>
            <h2>Change Password</h2>
            <p>Keep your account safe — use at least 8 characters with a mix of letters, numbers, and symbols.</p>
        </div>

        <!-- ── Form ── -->
        <div class="cp-body">
            <form method="POST" autocomplete="off" id="changePassForm">

                <!-- Current Password -->
                <div class="field-group">
                    <label for="oldpassword">Current Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-key icon"></i>
                        <input type="password" id="oldpassword" name="oldpassword"
                               placeholder="Enter your current password" required>
                        <button type="button" class="eye-btn" onclick="toggleVis('oldpassword',this)">
                           
                        </button>
                    </div>
                </div>

                <div class="cp-divider">New credentials</div>

                <!-- New Password -->
                <div class="field-group">
                    <label for="newpassword">New Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="newpassword" name="newpassword"
                               placeholder="Create a strong password" required
                               oninput="checkStrength(this.value)">
                        <button type="button" class="eye-btn" onclick="toggleVis('newpassword',this)">
                            
                        </button>
                    </div>
                    <!-- Strength meter -->
                    <div class="strength-wrap">
                        <div class="strength-bar" id="strengthBar">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        <div class="strength-label" id="strengthLabel"></div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="field-group" style="margin-bottom:28px;">
                    <label for="checkpassword">Confirm New Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-check-circle icon"></i>
                        <input type="password" id="checkpassword" name="checkpassword"
                               placeholder="Re-type your new password" required>
                        <button type="button" class="eye-btn" onclick="toggleVis('checkpassword',this)">
                            
                        </button>
                    </div>
                </div>

                <!-- Hidden field -->
                <input type="hidden" name="id_admin" value="<?php echo $userdetails['id_admin']; ?>">

                <!-- Submit -->
                <button type="submit" name="admin_changepass" class="btn-update">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </form>

            <!-- Cancel link -->
            <div class="cp-footer">
                <a href="admn_settings.php">
                    <i class="fas fa-arrow-left"></i> Cancel &amp; return to settings
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"></script>

<script>
    /* ── Toggle password visibility ── */
    function toggleVis(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /* ── Password strength meter ── */
    function checkStrength(val) {
        const bar   = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score   = 0;

        if (val.length >= 8)              score++;
        if (/[A-Z]/.test(val))            score++;
        if (/[0-9]/.test(val))            score++;
        if (/[^A-Za-z0-9]/.test(val))     score++;

        bar.className = 'strength-bar' + (val.length ? ' strength-' + score : '');

        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['', '#e74c3c', '#f39c12', '#c9a84c', '#3dba6f'];
        label.textContent  = val.length ? labels[score] : '';
        label.style.color  = val.length ? colors[score] : '';
    }
</script>
</body>
</html>