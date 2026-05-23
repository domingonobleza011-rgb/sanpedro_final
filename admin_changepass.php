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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIS - Change Password</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue-deep:    #1a2e4d;
            --blue-mid:     #2e5fa3;
            --blue-bright:  #4a90d9;
            --blue-glow:    rgba(74, 144, 217, 0.16);
            --gold:         #c9a84c;
            --ink:          #0f1825;
            --mist:         #f4f7fb;
            --border:       rgba(46, 95, 163, 0.16);
            --card-shadow:  0 20px 60px rgba(26,46,77,.12), 0 2px 8px rgba(26,46,77,.07);
        }

        body {
            background: var(--mist);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
        }

        .cp-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        /* ── Card ── */
        .cp-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        .cp-card::before {
            content: '';
            display: block;
            height: 5px;
            background: linear-gradient(90deg, var(--blue-deep), var(--blue-bright), var(--gold));
        }

        /* ── Header ── */
        .cp-header {
            background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-mid) 100%);
            padding: 28px 36px;
            position: relative;
            overflow: hidden;
        }
        .cp-header::after {
            content: '';
            position: absolute;
            right: -40px; top: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .cp-header .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            color: #c8dbf5;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 50px;
            margin-bottom: 10px;
        }
        .cp-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: #fff;
            margin: 0 0 4px;
        }
        .cp-header p {
            font-size: 13px;
            color: rgba(255,255,255,.6);
            margin: 0;
        }

        /* ── Body ── */
        .cp-body {
            padding: 36px 36px 32px;
        }

        /* ── Section label ── */
        .section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--blue-mid);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Field groups ── */
        .field-group {
            margin-bottom: 20px;
        }
        .field-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: var(--blue-deep);
            margin-bottom: 7px;
        }
        .field-wrap {
            position: relative;
        }
        .field-wrap .icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0b4cc;
            font-size: 13px;
            pointer-events: none;
        }
        .field-wrap input {
            width: 100%;
            padding: 11px 42px 11px 38px;
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
            border-color: var(--blue-bright);
            background: #fff;
            box-shadow: 0 0 0 3px var(--blue-glow);
        }
        .field-wrap input::placeholder { color: #b0bfd0; }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0b4cc;
            cursor: pointer;
            font-size: 13px;
            padding: 4px;
            line-height: 1;
            transition: color .2s;
        }
        .eye-btn:hover { color: var(--blue-mid); }

        /* ── Strength meter ── */
        .strength-wrap { margin-top: 8px; }
        .strength-bar {
            display: flex; gap: 4px; margin-bottom: 4px;
        }
        .strength-bar span {
            flex: 1; height: 3px; border-radius: 10px;
            background: #d8e4f0;
            transition: background .3s;
        }
        .strength-label {
            font-size: 11px;
            color: #a0b4cc;
            font-weight: 500;
            min-height: 16px;
        }
        .strength-1 span:nth-child(1)    { background: #e74c3c; }
        .strength-2 span:nth-child(-n+2) { background: #f39c12; }
        .strength-3 span:nth-child(-n+3) { background: var(--gold); }
        .strength-4 span                 { background: var(--blue-bright); }

        /* ── Divider ── */
        .cp-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0 24px;
        }

        /* ── Submit button ── */
        .btn-update {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--blue-mid) 0%, var(--blue-bright) 100%);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .3px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(46,95,163,.28);
            transition: transform .15s, filter .2s, box-shadow .2s;
        }
        .btn-update:hover {
            filter: brightness(1.07);
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(46,95,163,.35);
        }
        .btn-update:active { transform: translateY(0); filter: brightness(.96); }

        /* ── Footer link ── */
        .cp-footer {
            text-align: center;
            margin-top: 20px;
        }
        .cp-footer a {
            font-size: 13px;
            color: #7a91b0;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color .2s;
        }
        .cp-footer a:hover { color: var(--blue-mid); }

        @media (max-width: 520px) {
            .cp-header, .cp-body { padding-left: 20px; padding-right: 20px; }
        }
    </style>
</head>
<body id="page-top">

<div class="cp-wrapper">
    <div class="cp-card">

        <!-- Header -->
        <div class="cp-header">
            <div class="badge-pill"><i class="fas fa-lock"></i> Security Settings</div>
            <h2>Change Password</h2>
            <p>Use at least 8 characters with a mix of letters, numbers, and symbols.</p>
        </div>

        <!-- Form -->
        <div class="cp-body">
            <form method="POST" autocomplete="off" id="changePassForm">

                <!-- Current Password -->
                <div class="section-label"><i class="fas fa-key"></i> Current Credentials</div>

                <div class="field-group">
                    <label for="oldpassword">Current Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-key icon"></i>
                        <input type="password" id="oldpassword" name="oldpassword"
                               placeholder="Enter your current password" required>
                        <button type="button" class="eye-btn" onclick="toggleVis('oldpassword',this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="cp-divider"></div>

                <!-- New Password -->
                <div class="section-label"><i class="fas fa-lock"></i> New Credentials</div>

                <div class="field-group">
                    <label for="newpassword">New Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="newpassword" name="newpassword"
                               placeholder="Create a strong password" required
                               oninput="checkStrength(this.value)">
                        <button type="button" class="eye-btn" onclick="toggleVis('newpassword',this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="strength-wrap">
                        <div class="strength-bar" id="strengthBar">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        <div class="strength-label" id="strengthLabel"></div>
                    </div>
                </div>

                <div class="field-group" style="margin-bottom:28px;">
                    <label for="checkpassword">Confirm New Password</label>
                    <div class="field-wrap">
                        <i class="fas fa-check-circle icon"></i>
                        <input type="password" id="checkpassword" name="checkpassword"
                               placeholder="Re-type your new password" required>
                        <button type="button" class="eye-btn" onclick="toggleVis('checkpassword',this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="id_admin" value="<?php echo $userdetails['id_admin']; ?>">

                <button type="submit" name="admin_changepass" class="btn-update">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </form>

            <div class="cp-footer">
                <a href="admn_settings.php">
                    <i class="fas fa-arrow-left"></i> Cancel &amp; return to settings
                </a>
            </div>
        </div>

    </div>
</div>

<script>
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

    function checkStrength(val) {
        const bar   = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score   = 0;

        if (val.length >= 8)           score++;
        if (/[A-Z]/.test(val))         score++;
        if (/[0-9]/.test(val))         score++;
        if (/[^A-Za-z0-9]/.test(val))  score++;

        bar.className = 'strength-bar' + (val.length ? ' strength-' + score : '');

        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        const colors = ['', '#e74c3c', '#f39c12', '#c9a84c', '#4a90d9'];
        label.textContent = val.length ? labels[score] : '';
        label.style.color = val.length ? colors[score] : '';
    }
</script>
</body>
</html>