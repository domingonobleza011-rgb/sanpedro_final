<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require('classes/main.class.php');
    $bmis->create_admin(); 
    $this->log_activity('CREATE_ADMIN', 'Admin', "Created admin account for $fname $lname ($role)");
    $userdetails = $bmis->get_userdata();

    include('dashboard_sidebar_start.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIS - Add Administrator</title>

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
        }

        .page-heading {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
        }
        .page-heading .head-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--blue-deep), var(--blue-mid));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
            box-shadow: 0 4px 14px rgba(46,95,163,.3);
        }
        .page-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--blue-deep);
            margin: 0;
            line-height: 1.2;
        }
        .page-heading p {
            font-size: 12px;
            color: #7a91b0;
            margin: 2px 0 0;
        }

        .cp-card {
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

        .cp-body { padding: 36px 36px 32px; }

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

        .field-group { margin-bottom: 20px; }
        .field-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: var(--blue-deep);
            margin-bottom: 7px;
        }
        .field-wrap { position: relative; }
        .field-wrap .icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0b4cc;
            font-size: 13px;
            pointer-events: none;
        }
        .field-wrap input,
        .field-wrap select {
            width: 100%;
            padding: 11px 38px 11px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--ink);
            background: var(--mist);
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
            box-sizing: border-box;
            appearance: none;
            -webkit-appearance: none;
        }
        .field-wrap input:focus,
        .field-wrap select:focus {
            border-color: var(--blue-bright);
            background: #fff;
            box-shadow: 0 0 0 3px var(--blue-glow);
        }
        .field-wrap input::placeholder { color: #b0bfd0; }
        .field-wrap.has-select::after {
            content: '\f078';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: #a0b4cc;
            pointer-events: none;
        }
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
        .field-wrap.no-icon input { padding-left: 14px; }
        .field-hint {
            font-size: 11.5px;
            color: #8fa5bb;
            margin-top: 5px;
        }

        .role-tag { display: none; margin-top: 6px; }
        .role-tag span {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: .3px;
        }
        .role-tag.admin span  { display: inline-block; background: #dbe9ff; color: var(--blue-mid); }
        .role-tag.staff span  { display: inline-block; background: #e8f5ec; color: #2e7d4f; }

        .cp-divider { height: 1px; background: var(--border); margin: 28px 0; }

        .action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-changepass {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--blue-mid);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: background .2s, border-color .2s, color .2s;
        }
        .btn-changepass:hover {
            background: var(--blue-glow);
            border-color: var(--blue-bright);
            color: var(--blue-deep);
            text-decoration: none;
        }
        .btn-group-right { display: flex; align-items: center; gap: 10px; }
        .btn-clear {
            padding: 10px 20px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: transparent;
            color: #7a91b0;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .btn-clear:hover { background: #f0f4fa; color: var(--ink); }
        .btn-create {
            padding: 11px 28px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--blue-mid) 0%, var(--blue-bright) 100%);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .3px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(46,95,163,.28);
            transition: transform .15s, filter .2s, box-shadow .2s;
        }
        .btn-create:hover {
            filter: brightness(1.07);
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(46,95,163,.35);
        }
        .btn-create:active { transform: translateY(0); filter: brightness(.96); }

        @media (max-width: 576px) {
            .cp-header, .cp-body { padding-left: 20px; padding-right: 20px; }
            .action-row { flex-direction: column; align-items: stretch; }
            .btn-group-right { justify-content: flex-end; }
        }
    </style>
</head>

<body id="page-top">
<div class="container-fluid mt-4">

    <div class="page-heading">
        <div class="head-icon"><i class="fas fa-users-cog"></i></div>
        <div>
            <h1>User Management</h1>
            <p>Manage administrator and staff accounts for this system</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="cp-card">

                <div class="cp-header">
                    <div class="badge-pill"><i class="fas fa-user-plus"></i> New Account</div>
                    <h2>Register Admin / Staff</h2>
                    <p>Fill in the details below to create a new system account.</p>
                </div>

                <div class="cp-body">
                    <form method="POST" autocomplete="off" id="addAdminForm">

                        <div class="section-label"><i class="fas fa-id-card"></i> Personal Information</div>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label for="fname">First Name</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" id="fname" name="fname" placeholder="Enter first name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="field-group">
                                    <label for="mi">M.I.</label>
                                    <div class="field-wrap no-icon">
                                        <input type="text" id="mi" name="mi" maxlength="1" placeholder="A" style="text-align:center; text-transform:uppercase;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="field-group">
                                    <label for="lname">Last Name</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" id="lname" name="lname" placeholder="Enter last name" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-label mt-2"><i class="fas fa-cog"></i> Account Details</div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label for="email">Email Address</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-envelope icon"></i>
                                        <input type="email" id="email" name="email" placeholder="example@email.com" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label for="role">Account Role</label>
                                    <div class="field-wrap has-select">
                                        <i class="fas fa-user-shield icon"></i>
                                        <select id="role" name="role" required onchange="showRoleTag(this.value)">
                                            <option value="" disabled selected>Select Role</option>
                                            <option value="administrator">Administrator</option>
                                            <option value="staff">Barangay Staff</option>
                                        </select>
                                    </div>
                                    <div class="role-tag" id="roleTag">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="password">Password</label>
                            <div class="field-wrap">
                                <i class="fas fa-lock icon"></i>
                                <input type="password" id="password" name="password"
                                       placeholder="Minimum 8 characters" required>
                                <button type="button" class="eye-btn" onclick="toggleVis('password',this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="field-hint"><i class="fas fa-info-circle mr-1"></i>Use a secure, unique password for this account.</div>
                        </div>

                        <div class="cp-divider"></div>

                        <div class="action-row">
                            <a href="admin_changepass.php" class="btn-changepass">
                                <i class="fas fa-key"></i> Change Existing Password
                            </a>
                            <div class="btn-group-right">
                                <button type="reset" class="btn-clear" onclick="resetExtras()">
                                    <i class="fas fa-undo mr-1"></i> Clear
                                </button>
                                <button type="button" class="btn-create" onclick="openConfirmModal()">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:420px; overflow:hidden; margin:1rem; box-shadow:0 24px 60px rgba(0,0,0,.2);">

        <div style="background:linear-gradient(135deg,#1a2e4d,#2e5fa3); padding:22px 24px 18px;">
            <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#c8dbf5;font-size:11px;padding:3px 10px;border-radius:50px;letter-spacing:.5px;margin-bottom:8px;">
                <i class="fas fa-user-plus"></i> Confirm Creation
            </div>
            <h5 style="color:#fff;font-family:'Playfair Display',serif;font-size:18px;margin:0 0 2px;">Create new account?</h5>
            <p style="color:rgba(255,255,255,.55);font-size:12px;margin:0;">Please review the details before confirming.</p>
        </div>

        <div style="padding:20px 24px;">
            <div style="background:#fdf6ec;border-radius:10px;padding:10px 14px;display:flex;gap:10px;align-items:flex-start;margin-bottom:16px;border:1px solid #fce8c0;">
                <i class="fas fa-exclamation-triangle" style="color:#c9a84c;margin-top:2px;"></i>
                <p style="font-size:12.5px;color:#7a6030;margin:0;line-height:1.5;">This will create a new system account. Make sure all information is correct before proceeding.</p>
            </div>
            <table style="width:100%;font-size:13px;border-collapse:collapse;">
                <tr style="border-bottom:1px solid #eef1f6;">
                    <td style="padding:9px 0;color:#7a91b0;width:90px;">Full name</td>
                    <td id="modal_name" style="padding:9px 0;font-weight:600;color:#0f1825;"></td>
                </tr>
                <tr style="border-bottom:1px solid #eef1f6;">
                    <td style="padding:9px 0;color:#7a91b0;">Email</td>
                    <td id="modal_email" style="padding:9px 0;font-weight:600;color:#0f1825;"></td>
                </tr>
                <tr>
                    <td style="padding:9px 0;color:#7a91b0;">Role</td>
                    <td style="padding:9px 0;"><span id="modal_role_badge" style="font-size:11px;padding:3px 10px;border-radius:50px;font-weight:600;"></span></td>
                </tr>
            </table>
        </div>

        <div style="padding:14px 24px;display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #eef1f6;">
            <button onclick="closeConfirmModal()" style="padding:9px 18px;border-radius:10px;border:1.5px solid #d0daea;background:transparent;color:#7a91b0;font-family:'DM Sans',sans-serif;font-size:13px;cursor:pointer;">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
            <button onclick="submitAdminForm()" style="padding:9px 22px;border-radius:10px;border:none;background:linear-gradient(135deg,#2e5fa3,#4a90d9);color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;box-shadow:0 4px 14px rgba(46,95,163,.28);">
                <i class="fas fa-check"></i> Yes, create account
            </button>
        </div>
    </div>
</div>

<script>
    function openConfirmModal() {
        const fname = document.getElementById('fname').value.trim();
        const mi    = document.getElementById('mi').value.trim();
        const lname = document.getElementById('lname').value.trim();
        const email = document.getElementById('email').value.trim();
        const role  = document.getElementById('role').value;

        if (!fname || !lname || !email || !role) {
            document.getElementById('addAdminForm').reportValidity();
            return;
        }

        const fullName = [fname, mi ? mi + '.' : '', lname].filter(Boolean).join(' ');
        document.getElementById('modal_name').textContent  = fullName;
        document.getElementById('modal_email').textContent = email;

        const badge = document.getElementById('modal_role_badge');
        if (role === 'administrator') {
            badge.textContent = '🛡 Administrator';
            badge.style.cssText = 'font-size:11px;padding:3px 10px;border-radius:50px;font-weight:600;background:#dbe9ff;color:#2e5fa3;';
        } else {
            badge.textContent = '👤 Barangay Staff';
            badge.style.cssText = 'font-size:11px;padding:3px 10px;border-radius:50px;font-weight:600;background:#e8f5ec;color:#2e7d4f;';
        }

        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function submitAdminForm() {
        // Add the hidden submit trigger so PHP sees 'add_admin' in $_POST
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'add_admin';
        hidden.value = '1';
        document.getElementById('addAdminForm').appendChild(hidden);
        document.getElementById('addAdminForm').submit();
    }

    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeConfirmModal();
    });

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

    function showRoleTag(val) {
        const tag  = document.getElementById('roleTag');
        const span = tag.querySelector('span');
        tag.className = 'role-tag';
        if (val === 'administrator') {
            tag.classList.add('admin');
            span.textContent = '🛡 Administrator — Full system access';
            tag.style.display = 'block';
        } else if (val === 'staff') {
            tag.classList.add('staff');
            span.textContent = '👤 Barangay Staff — Limited access';
            tag.style.display = 'block';
        } else {
            tag.style.display = 'none';
        }
    }

    function resetExtras() {
        document.getElementById('roleTag').style.display = 'none';
        const eye = document.querySelector('.eye-btn i');
        if (eye) eye.className = 'fas fa-eye';
        const pwd = document.getElementById('password');
        if (pwd) pwd.type = 'password';
    }
</script>
</body>
</html>