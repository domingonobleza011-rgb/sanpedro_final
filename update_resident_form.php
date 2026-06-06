<?php
   error_reporting(E_ALL ^ E_WARNING);
   require('classes/resident.class.php');
   $userdetails = $bmis->get_userdata();
   $id_resident = $_GET['id_resident'];
   $view = $residentbmis->get_single_resident($id_resident);
   $residentbmis->update_resident();
   include('dashboard_sidebar_start_staff.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIS – Update Resident</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

    <style>
        :root {
            --blue-deep:   #1a2e4d;
            --blue-mid:    #2e5fa3;
            --blue-bright: #4a90d9;
            --blue-glow:   rgba(74,144,217,.15);
            --gold:        #c9a84c;
            --ink:         #0f1825;
            --mist:        #f4f7fb;
            --border:      rgba(46,95,163,.16);
            --card-shadow: 0 20px 60px rgba(26,46,77,.11), 0 2px 8px rgba(26,46,77,.07);
            --green:       #2e7d4f;
            --green-light: #e8f5ec;
            --red:         #c0392b;
            --red-light:   #fdecea;
        }

        body {
            background: var(--mist);
            font-family: 'DM Sans', sans-serif;
        }

        /* ── page heading ── */
        .page-heading {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 28px;
        }
        .page-heading .head-icon {
            width: 46px; height: 46px; border-radius: 12px;
            background: linear-gradient(135deg, var(--blue-deep), var(--blue-mid));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px;
            box-shadow: 0 4px 14px rgba(46,95,163,.3);
        }
        .page-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px; color: var(--blue-deep);
            margin: 0; line-height: 1.2;
        }
        .page-heading p { font-size: 12px; color: #7a91b0; margin: 2px 0 0; }

        /* ── card ── */
        .up-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 32px;
        }
        .up-card::before {
            content: '';
            display: block; height: 5px;
            background: linear-gradient(90deg, var(--blue-deep), var(--blue-bright), var(--gold));
        }

        /* ── card header ── */
        .up-header {
            background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-mid) 100%);
            padding: 26px 36px;
            position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 14px;
        }
        .up-header::after {
            content: '';
            position: absolute; right: -40px; top: -40px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,.04); pointer-events: none;
        }
        .up-header-left .badge-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            color: #c8dbf5; font-size: 11px; font-weight: 500;
            letter-spacing: .6px; text-transform: uppercase;
            padding: 4px 12px; border-radius: 50px; margin-bottom: 8px;
        }
        .up-header-left h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px; color: #fff; margin: 0 0 3px;
        }
        .up-header-left p { font-size: 12.5px; color: rgba(255,255,255,.6); margin: 0; }

        /* resident name chip in header */
        .resident-chip {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 12px; padding: 10px 16px;
        }
        .resident-chip .avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,255,255,.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 700; color: #fff;
            letter-spacing: .5px;
        }
        .resident-chip .rname { font-size: 14px; font-weight: 600; color: #fff; line-height: 1.2; }
        .resident-chip .rid   { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 1px; }

        /* ── body ── */
        .up-body { padding: 32px 36px 28px; }

        /* ── section label ── */
        .section-label {
            font-size: 11px; font-weight: 600; letter-spacing: .7px;
            text-transform: uppercase; color: var(--blue-mid);
            margin-bottom: 18px; margin-top: 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-label::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* ── field group ── */
        .field-group { margin-bottom: 18px; }
        .field-group label {
            display: block; font-size: 11.5px; font-weight: 600;
            letter-spacing: .4px; text-transform: uppercase;
            color: var(--blue-deep); margin-bottom: 6px;
        }
        .field-wrap { position: relative; }
        .field-wrap .icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #a0b4cc; font-size: 13px; pointer-events: none;
        }
        .field-wrap input,
        .field-wrap select,
        .field-wrap textarea {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px; color: var(--ink);
            background: var(--mist);
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none; box-sizing: border-box;
            appearance: none; -webkit-appearance: none;
        }
        .field-wrap input:focus,
        .field-wrap select:focus {
            border-color: var(--blue-bright);
            background: #fff;
            box-shadow: 0 0 0 3px var(--blue-glow);
        }
        .field-wrap input::placeholder { color: #b0bfd0; }
        /* select arrow */
        .field-wrap.has-select::after {
            content: '\f078';
            font-family: 'Font Awesome 5 Free'; font-weight: 900;
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            font-size: 11px; color: #a0b4cc; pointer-events: none;
        }
        /* no-icon variant */
        .field-wrap.no-icon input,
        .field-wrap.no-icon select { padding-left: 14px; }

        /* ── divider ── */
        .up-divider { height: 1px; background: var(--border); margin: 26px 0; }

        /* ── action row ── */
        .action-row {
            display: flex; align-items: center;
            justify-content: flex-end; gap: 10px; flex-wrap: wrap;
        }
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 10px;
            border: 1.5px solid var(--border);
            background: transparent; color: var(--red);
            font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500;
            text-decoration: none;
            transition: background .2s, border-color .2s;
        }
        .btn-back:hover {
            background: var(--red-light); border-color: #e08070;
            color: var(--red); text-decoration: none;
        }
        .btn-update {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 28px; border-radius: 10px; border: none;
            background: linear-gradient(135deg, var(--blue-mid), var(--blue-bright));
            color: #fff; font-family: 'DM Sans', sans-serif;
            font-size: 13px; font-weight: 600; letter-spacing: .3px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(46,95,163,.28);
            transition: filter .2s, transform .15s, box-shadow .2s;
        }
        .btn-update:hover {
            filter: brightness(1.07); transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(46,95,163,.36);
        }
        .btn-update:active { transform: translateY(0); filter: brightness(.96); }

        @media (max-width: 576px) {
            .up-header, .up-body { padding-left: 18px; padding-right: 18px; }
            .action-row { justify-content: stretch; flex-direction: column; }
            .btn-back, .btn-update { width: 100%; justify-content: center; }
        }
    </style>
</head>

<body id="page-top">
<div class="container-fluid mt-4">

    <!-- Page Heading -->
    <div class="page-heading">
        <div class="head-icon"><i class="fas fa-users"></i></div>
        <div>
            <h1>Resident Management</h1>
            <p>Update an existing barangay resident record</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="up-card">

                <!-- Card Header -->
                <div class="up-header">
                    <div class="up-header-left">
                        <div class="badge-pill"><i class="fas fa-user-edit"></i> Edit Record</div>
                        <h2>Update Resident Data</h2>
                        <p>Modify the details below and click Update to save changes.</p>
                    </div>
                    <div class="resident-chip">
                        <div class="avatar">
                            <?php
                                echo strtoupper(
                                    substr($view['fname'] ?? 'R', 0, 1) .
                                    substr($view['lname'] ?? 'S', 0, 1)
                                );
                            ?>
                        </div>
                        <div>
                            <div class="rname">
                                <?php echo htmlspecialchars(($view['fname'] ?? '') . ' ' . ($view['lname'] ?? '')); ?>
                            </div>
                            <div class="rid">Resident ID #<?php echo htmlspecialchars($id_resident); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="up-body">
                    <form method="post">

                        <!-- ── Personal Information ── -->
                        <div class="section-label"><i class="fas fa-id-card"></i> Personal Information</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Last Name</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" name="lname" value="<?= htmlspecialchars($view['lname'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>First Name</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" name="fname" value="<?= htmlspecialchars($view['fname'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Middle Name</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-user icon"></i>
                                        <input type="text" name="mi" value="<?= htmlspecialchars($view['mi'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Birth Date</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-calendar icon"></i>
                                        <input type="date" name="bdate" value="<?= htmlspecialchars($view['bdate'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Birth Place</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-map-marker-alt icon"></i>
                                        <input type="text" name="bplace" value="<?= htmlspecialchars($view['bplace'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Nationality</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-flag icon"></i>
                                        <input type="text" name="nationality" value="<?= htmlspecialchars($view['nationality'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Civil Status</label>
                                    <div class="field-wrap no-icon has-select">
                                        <select name="status" required>
                                            <option value="">Choose...</option>
                                            <?php
                                                $statuses = ['Single','Married','Widowed','Separated','Divorced'];
                                                foreach ($statuses as $s) {
                                                    $sel = (($view['status'] ?? '') == $s) ? 'selected' : '';
                                                    echo "<option value=\"$s\" $sel>$s</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>Sex</label>
                                    <div class="field-wrap no-icon has-select">
                                        <select name="sex" required>
                                            <option value="">Choose...</option>
                                            <option value="Male"   <?= ($view['sex'] ?? '') == 'Male'   ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= ($view['sex'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-group">
                                    <label>PWD?</label>
                                    <div class="field-wrap no-icon has-select">
                                        <select name="pwd" required>
                                            <option value="">Choose...</option>
                                            <option value="Yes" <?= ($view['pwd'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                            <option value="No"  <?= ($view['pwd'] ?? '') == 'No'  ? 'selected' : '' ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Contact Information ── -->
                        <div class="section-label mt-2"><i class="fas fa-address-book"></i> Contact Information</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label>Contact Number</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-phone icon"></i>
                                        <input type="tel" name="contact" maxlength="11" pattern="[0-9]{11}"
                                               value="<?= htmlspecialchars($view['contact'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label>Email Address</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-envelope icon"></i>
                                        <input type="email" name="email" value="<?= htmlspecialchars($view['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Address ── -->
                        <div class="section-label mt-2"><i class="fas fa-home"></i> Address</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>House No.</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-door-open icon"></i>
                                        <input type="text" name="houseno" value="<?= htmlspecialchars($view['houseno'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>Street</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-road icon"></i>
                                        <input type="text" name="street" value="<?= htmlspecialchars($view['street'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>Barangay</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-map-pin icon"></i>
                                        <input type="text" name="brgy" value="<?= htmlspecialchars($view['brgy'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-group">
                                    <label>Municipality</label>
                                    <div class="field-wrap">
                                        <i class="fas fa-city icon"></i>
                                        <input type="text" name="municipal" value="<?= htmlspecialchars($view['municipal'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Civic Info ── -->
                        <div class="section-label mt-2"><i class="fas fa-vote-yea"></i> Civic Information</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label>Registered Voter?</label>
                                    <div class="field-wrap no-icon has-select">
                                        <select name="voter" required>
                                            <option value="">Choose...</option>
                                            <option value="Yes" <?= ($view['voter'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                            <option value="No"  <?= ($view['voter'] ?? '') == 'No'  ? 'selected' : '' ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label>Head of Family?</label>
                                    <div class="field-wrap no-icon has-select">
                                        <select name="family_role" required>
                                            <option value="">Choose...</option>
                                            <option value="Yes" <?= ($view['family_role'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                            <option value="No"  <?= ($view['family_role'] ?? '') == 'No'  ? 'selected' : '' ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="up-divider"></div>

                        <input type="hidden" name="role" value="resident">
                        <div class="action-row">
                            <a href="admn_resident_crud.php" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" name="update_resident" class="btn-update">
                                <i class="fas fa-save"></i> Update Resident
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('dashboard_sidebar_end.php'); ?>
