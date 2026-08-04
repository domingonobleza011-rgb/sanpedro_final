<?php
    
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors',0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
    require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_staff_or_admin();
    $bmis->delete_certofindigency();
    $bmis->create_certofindigency();
    $view = $bmis->view_certofindigency();
    $residents_list = $residentbmis->view_resident();
    $id_resident = $_GET['id_resident'];
    $residentbmis->get_single_certofindigency($id_resident);
   
?>

<?php 
    include('dashboard_sidebar_start.php');
?>
<style>
    .input-icons i {
        position: absolute;
    }
        
    .input-icons {
        width: 30%;
        margin-bottom: 10px;
        margin-left: 34%;
    }
        
    .icon {
        padding: 10px;
        min-width: 40px;
    }
    .form-control{
        text-align: center;
    }
    
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

/* ─── THEME TOKENS ──────────────────────────────────────────── */
:root {
    --navy:          #0f2d5a;
    --navy-mid:      #1a4480;
    --navy-light:    #2b5ea7;
    --navy-pale:     #e8eef7;
    --gold:          #c9943a;
    --gold-light:    #e8b86d;
    --gold-pale:     #fdf3e3;
    --teal:          #0d9488;
    --teal-pale:     #e0f2f0;
    --danger:        #dc2626;
    --danger-pale:   #fef2f2;
    --warning:       #d97706;
    --warning-pale:  #fffbeb;
    --success:       #059669;
    --success-pale:  #ecfdf5;
    --cream:         #f7f8fc;
    --white:         #ffffff;
    --text-dark:     #1a1a2e;
    --text-mid:      #4a5568;
    --text-light:    #718096;
    --border:        #e8ecf0;
    --shadow-sm:     0 2px 8px rgba(15,45,90,0.07);
    --shadow-md:     0 6px 24px rgba(15,45,90,0.11);
    --radius:        14px;
    --radius-sm:     10px;
    --transition:    0.22s cubic-bezier(0.4,0,0.2,1);
}

/* ─── GLOBAL ────────────────────────────────────────────────── */
body {
    font-family: 'DM Sans', -apple-system, sans-serif !important;
    background: var(--cream) !important;
    color: var(--text-dark) !important;
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'DM Sans', sans-serif !important;
}

/* Section headings */
h4 {
    font-weight: 700 !important;
    font-size: 1.05rem !important;
    color: var(--navy) !important;
    letter-spacing: 0.2px;
    display: flex;
    align-items: center;
    gap: 10px;
}

h4::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 20px;
    background: linear-gradient(to bottom, var(--gold), var(--gold-light));
    border-radius: 4px;
    flex-shrink: 0;
}

hr {
    border-color: var(--border) !important;
    opacity: 1 !important;
    margin: 0.5rem 0 !important;
}

/* ─── SIDEBAR ───────────────────────────────────────────────── */
.sidebar {
    background: linear-gradient(180deg, var(--navy) 0%, var(--navy-mid) 60%, #153560 100%) !important;
    border-right: none !important;
    box-shadow: 4px 0 24px rgba(15,45,90,0.18);
}

.sidebar-brand {
    padding: 1.6rem 1rem 1.4rem !important;
    background: rgba(0,0,0,0.12) !important;
    border-bottom: 1px solid rgba(255,255,255,0.08) !important;
    gap: 10px;
}

.sidebar-brand-text {
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
    color: rgba(255,255,255,0.95) !important;
    text-transform: none !important;
    line-height: 1.3;
}



.sidebar-divider {
    border-top-color: rgba(255,255,255,0.08) !important;
    margin: 0.6rem 1rem !important;
}

.sidebar-heading {
    font-size: 0.65rem !important;
    font-weight: 700 !important;
    letter-spacing: 1.8px !important;
    text-transform: uppercase !important;
    color: rgba(255,255,255,0.35) !important;
    padding: 0.8rem 1.2rem 0.4rem !important;
}

/* Sidebar nav links */
.sidebar .nav-item .nav-link {
    color: rgba(255,255,255,0.72) !important;
    font-size: 0.875rem !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    border-radius: 0 !important;
    transition: all var(--transition) !important;
    display: flex;
    align-items: center;
    gap: 10px;
    border-left: 3px solid transparent;
}

.sidebar .nav-item .nav-link i,
.sidebar .nav-item .nav-link .bi {
    font-size: 0.95rem;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    color: rgba(255,255,255,0.5);
    transition: color var(--transition);
}

.sidebar .nav-item .nav-link:hover {
    color: var(--white) !important;
    background: rgba(255,255,255,0.07) !important;
    border-left-color: rgba(201,148,58,0.5) !important;
}

.sidebar .nav-item .nav-link:hover i,
.sidebar .nav-item .nav-link:hover .bi {
    color: var(--gold-light);
}

.sidebar .nav-item.active .nav-link,
.sidebar .nav-item .nav-link.active {
    color: var(--white) !important;
    background: rgba(201,148,58,0.15) !important;
    border-left-color: var(--gold) !important;
    font-weight: 500 !important;
}

/* ─── TOPBAR ────────────────────────────────────────────────── */
.topbar {
    background: var(--white) !important;
    box-shadow: 0 2px 16px rgba(15,45,90,0.08) !important;
    border-bottom: 1px solid var(--border) !important;
    padding: 0 20px !important;
    height: 60px;
    align-items: center;
}

.topbar .nav-item .nav-link {
    color: var(--text-mid) !important;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 8px 14px !important;
    border-radius: 8px;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.topbar .nav-item .nav-link:hover {
    background: var(--cream);
    color: var(--navy) !important;
}

/* Username badge in topbar */
.topbar .text-gray-800 {
    color: var(--text-dark) !important;
    font-weight: 500;
}
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->

    <div class="row"> 
        <div class="col text-center"> 
            <h1> Certificate of Indigency Requests</h1>
        </div>
    </div>

    <hr>
    <br><br>

    <div class="row"> 
        <div class="col">
            <form method="POST">
                <div class="input-icons" >
                    <i class="fa fa-search icon"></i>
                    <input type="search" class="form-control" name="keyword" style="border-radius: 30px;" value="" required=""/>
                </div>
               <button class="btn btn-success" name="search_certofindigency" style="width: 100px; font-size: 17px; border-radius:30px; margin-left: 40%;">
                    Search
                </button>
                <a href="admn_certofindigency.php" class="btn btn-info" style="width: 100px; font-size: 17px; border-radius:30px;">Reload</a>
                <button type="button" class="btn" style="background:var(--primary); color:#fff; border-radius:30px; font-weight:600;" data-bs-toggle="modal" data-bs-target="#addCertofindigencyModal">
                     Add Certificate
                </button>
                </form>
            <br>
        </div>
    </div>

    <br>

    <div class="row"> 
        <div class="col"> 
            <?php 
                include('admn_table_certofindigency_search.php');
            ?>
        </div>
    </div>

    <!-- Add Certificate of Indigency Modal -->
    <div class="modal fade" id="addCertofindigencyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px; overflow:hidden;">
                <form method="POST" id="addCertofindigencyForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-file-earmark-plus"></i>&nbsp; Add Certificate of Indigency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Resident</label>
                            <select class="form-select" id="admCertofindigResident" name="id_resident" required>
                                <option value="" selected disabled>-- Choose a resident --</option>
                                <option value="0">🡒 Not in resident list (walk-in / manual entry)</option>
                                <?php foreach ($residents_list as $r): ?>
                                    <option
                                        value="<?= htmlspecialchars($r['id_resident']) ?>"
                                        data-lname="<?= htmlspecialchars($r['lname']) ?>"
                                        data-fname="<?= htmlspecialchars($r['fname']) ?>"
                                        data-mi="<?= htmlspecialchars($r['mi']) ?>"
                                        data-nationality="<?= htmlspecialchars($r['nationality']) ?>"
                                        data-houseno="<?= htmlspecialchars($r['houseno']) ?>"
                                        data-street="<?= htmlspecialchars($r['street']) ?>"
                                        data-brgy="<?= htmlspecialchars($r['brgy']) ?>"
                                        data-municipal="<?= htmlspecialchars($r['municipal']) ?>"
                                    >
                                        <?= htmlspecialchars($r['lname'] . ', ' . $r['fname'] . ' ' . $r['mi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text" id="admCertofindigWalkinNote" style="display:none; color:#b8860b;">
                                <i class="bi bi-info-circle"></i> Walk-in applicant — fill in the details below manually.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Last Name</label>
                                <input type="text" class="form-control" name="lname" id="admCertofindigLname" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">First Name</label>
                                <input type="text" class="form-control" name="fname" id="admCertofindigFname" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">M.I.</label>
                                <input type="text" class="form-control" name="mi" id="admCertofindigMi">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nationality</label>
                            <input type="text" class="form-control" name="nationality" id="admCertofindigNationality" required>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">House No.</label>
                                <input type="text" class="form-control" name="houseno" id="admCertofindigHouseno" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Street</label>
                                <input type="text" class="form-control" name="street" id="admCertofindigStreet" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Barangay</label>
                                <input type="text" class="form-control" name="brgy" id="admCertofindigBrgy" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Municipality</label>
                                <input type="text" class="form-control" name="municipal" id="admCertofindigMunicipal" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Purpose</label>
                                <select class="form-select" name="purpose" required>
                                    <option value="" selected disabled>Choose Purpose...</option>
                                    <option value="Job/Employment">Job/Employment</option>
                                    <option value="Business Establishment">Business Requirement</option>
                                    <option value="Financial Transaction">Financial Transaction</option>
                                    <option value="Scholarship">Scholarship</option>
                                    <option value="Other important transactions.">Other important transactions.</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_certofindigency" class="btn" style="background:var(--navy); color:#fff; font-weight:600;">Submit Certificate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('admCertofindigResident').addEventListener('change', function () {
            var opt = this.options[this.selectedIndex];
            var isWalkin = this.value === '0';
            document.getElementById('admCertofindigWalkinNote').style.display = isWalkin ? 'block' : 'none';
            document.getElementById('admCertofindigLname').value = isWalkin ? '' : (opt.getAttribute('data-lname') || '');
            document.getElementById('admCertofindigFname').value = isWalkin ? '' : (opt.getAttribute('data-fname') || '');
            document.getElementById('admCertofindigMi').value = isWalkin ? '' : (opt.getAttribute('data-mi') || '');
            document.getElementById('admCertofindigNationality').value = isWalkin ? '' : (opt.getAttribute('data-nationality') || '');
            document.getElementById('admCertofindigHouseno').value = isWalkin ? '' : (opt.getAttribute('data-houseno') || '');
            document.getElementById('admCertofindigStreet').value = isWalkin ? '' : (opt.getAttribute('data-street') || '');
            document.getElementById('admCertofindigBrgy').value = isWalkin ? '' : (opt.getAttribute('data-brgy') || '');
            document.getElementById('admCertofindigMunicipal').value = isWalkin ? '' : (opt.getAttribute('data-municipal') || '');
            if (isWalkin) { document.getElementById('admCertofindigLname').focus(); }
        });
    </script>

</div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<?php 
    include('dashboard_sidebar_end.php');
?>

