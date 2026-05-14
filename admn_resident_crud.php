<?php
    
   error_reporting(E_ALL ^ E_WARNING);
   ini_set('display_errors',0);
define('BMIS_ROLE_REQUIRED', 'admin');
require_once('secure_header.php'); 
   require('classes/resident.class.php');
   $userdetails = $bmis->get_userdata();
   $bmis->validate_admin();
   $view = $residentbmis->view_resident();
   $residentbmis->create_resident();
   $residentbmis->update_resident();
   $residentbmis->delete_resident();
   

   $rescount = $residentbmis->count_resident();
   $rescountm = $residentbmis->count_male_resident();
   $rescountf = $residentbmis->count_female_resident();
   $rescountfh = $residentbmis->count_head_resident();
   $rescountfm = $residentbmis->count_member_resident();
   
?>

<?php 
    include('dashboard_sidebar_start.php');
?>
<style>
/* ============================================================
   BARANGAY SAN PEDRO — ADMIN DASHBOARD — IMPROVED CSS
   Extends sb-admin-2 with a refined navy + gold civic theme
   ============================================================ */

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

/* ─── CONTENT WRAPPER ───────────────────────────────────────── */
#content-wrapper {
    background: var(--cream) !important;
}

#content {
    padding-bottom: 2rem;
}

.container-fluid {
    padding: 1.5rem 2rem !important;
}

/* ─── STAT CARDS ────────────────────────────────────────────── */
/* Override sb-admin2 border-left cards */
.card {
    border: none !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow-sm) !important;
    transition: all var(--transition) !important;
    overflow: hidden;
    background: var(--white) !important;
}

.card:hover {
    box-shadow: var(--shadow-md) !important;
    transform: translateY(-3px);
}

.card-body {
    padding: 1.4rem 1.6rem !important;
}

/* Colored top accent instead of left border */
.card.border-left-primary {
    border-top: 3px solid var(--navy-light) !important;
    border-left: none !important;
}

.card.border-left-info {
    border-top: 3px solid var(--teal) !important;
    border-left: none !important;
}

.card.border-left-danger {
    border-top: 3px solid var(--danger) !important;
    border-left: none !important;
}

.card.border-left-warning {
    border-top: 3px solid var(--warning) !important;
    border-left: none !important;
}

.card.border-left-success {
    border-top: 3px solid var(--success) !important;
    border-left: none !important;
}

/* Tinted card backgrounds */
.card.border-left-primary .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--navy-pale)) !important; }
.card.border-left-info    .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--teal-pale))  !important; }
.card.border-left-danger  .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--danger-pale))!important; }
.card.border-left-warning .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--warning-pale))!important; }
.card.border-left-success .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--success-pale))!important; }

/* Card labels */
.text-xs.font-weight-bold.text-primary {
    color: var(--navy-mid) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-info {
    color: var(--teal) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-danger {
    color: var(--danger) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-warning {
    color: var(--warning) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-success {
    color: var(--success) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

/* Big number */
.h5.mb-0.font-weight-bold.text-dark {
    font-size: 1.8rem !important;
    font-weight: 800 !important;
    color: var(--text-dark) !important;
    line-height: 1.1;
    font-family: 'DM Sans', sans-serif !important;
}

/* View records link */
.card-body a {
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    color: var(--navy-mid) !important;
    text-decoration: none !important;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: gap var(--transition), opacity var(--transition);
}

.card.border-left-info .card-body a    { color: var(--teal)    !important; }
.card.border-left-danger .card-body a  { color: var(--danger)  !important; }
.card.border-left-warning .card-body a { color: var(--warning) !important; }
.card.border-left-success .card-body a { color: var(--success) !important; }

.card-body a:hover { opacity: 0.75; gap: 8px; }

.card-body a::after {
    content: '→';
    font-size: 0.85em;
}

/* Card icon */
.card-body .col-auto i,
.card-body .col-auto .bi {
    opacity: 0.18;
    font-size: 2.4rem !important;
    color: var(--text-dark) !important;
}

.card:hover .card-body .col-auto i,
.card:hover .card-body .col-auto .bi {
    opacity: 0.28;
}

/* ─── CARD SPACING ──────────────────────────────────────────── */
.card-upper-space {
    margin-top: 24px !important;
}

.card-row-gap {
    margin-top: 24px !important;
}

.row {
    row-gap: 0;
}

/* ─── SECTION SEPARATORS ────────────────────────────────────── */
.container-fluid > br + hr {
    border: none !important;
    height: 1px !important;
    background: linear-gradient(to right, transparent, var(--border), transparent) !important;
    margin: 1.5rem 0 !important;
}

/* ─── RESPONSIVE TABLES (other pages) ──────────────────────── */
.table {
    font-size: 0.875rem;
}

.table thead th {
    background: var(--navy);
    color: var(--white);
    font-weight: 600;
    letter-spacing: 0.5px;
    font-size: 0.78rem;
    text-transform: uppercase;
    border: none;
    padding: 12px 16px;
}

.table tbody tr:hover {
    background: var(--navy-pale);
}

.table td, .table th {
    border-color: var(--border);
    vertical-align: middle;
    padding: 10px 16px;
}

/* ─── BUTTONS ───────────────────────────────────────────────── */
.btn-primary {
    background: linear-gradient(135deg, var(--navy), var(--navy-light)) !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    letter-spacing: 0.3px;
    box-shadow: 0 3px 10px rgba(15,45,90,0.25) !important;
    transition: all var(--transition) !important;
}

.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(15,45,90,0.3) !important;
}

/* ─── PAGE HEADER (optional) ────────────────────────────────── */
.page-header {
    padding: 1.2rem 0 1.5rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.5rem;
}

.page-header h4 {
    font-size: 1.35rem !important;
}

/* ─── SECTION HEADER CHIPS ──────────────────────────────────── */
.section-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 20px;
    margin-bottom: 1rem;
}

.section-label.resident { background: var(--navy-pale); color: var(--navy-mid); }
.section-label.staff    { background: var(--teal-pale);  color: var(--teal);     }
.section-label.complaint{ background: var(--danger-pale);color: var(--danger);   }

/* ─── FOOTER ────────────────────────────────────────────────── */
.sticky-footer {
    background: var(--white) !important;
    border-top: 1px solid var(--border) !important;
    font-size: 0.8rem !important;
    color: var(--text-light) !important;
    padding: 16px 24px !important;
}

/* ─── SCROLLBAR (Webkit) ────────────────────────────────────── */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(15,45,90,0.15); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: rgba(15,45,90,0.28); }

/* ─── ALERTS & BADGES ───────────────────────────────────────── */
.badge-primary { background-color: var(--navy-light) !important; }
.badge-info    { background-color: var(--teal)       !important; }
.badge-danger  { background-color: var(--danger)     !important; }
.badge-warning { background-color: var(--warning)    !important; color: var(--white) !important; }
.badge-success { background-color: var(--success)    !important; }

/* ─── RESPONSIVE ────────────────────────────────────────────── */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem 1.2rem !important;
    }

    .h5.mb-0.font-weight-bold.text-dark {
        font-size: 1.5rem !important;
    }

    .card:hover {
        transform: none;
    }
}
</style>

<div class="container-fluid">
    <h1 class="mb-4 text-center text-secondary" style="font-weight: 300;">Barangay Residents Data</h1>

    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="mb-3 text-left">
                <button type="button" class="btn btn-primary btn-pill shadow-sm" data-toggle="modal" data-target="#addResidentModal">
                    <i class="fas fa-plus"></i> Add New Barangay Resident
                </button>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-blue shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.7rem;">Number of Residents</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescount ?></div>
                            <a href="admn_table_totalres.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-green shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.7rem;">Total Household Head</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescountfh ?></div>
                            <a href="admn_table_totalhouse.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-tie fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-cyan shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 0.7rem;">Total Male Residents</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescountm ?></div>
                            <a href="admn_table_maleres.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-male fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-orange shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.7rem;">Total Female Residents</div>
                            <div class="h4 mb-0 font-weight-bold text-dark"><?= $rescountf ?></div>
                            <a href="admn_table_femaleres.php"> View Records </a>
                        </div>
                        <div class="col-auto"><i class="fas fa-female fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="search-container mb-4">
                <form method="POST" action="">
                    <div class="position-relative mb-3">
                        
                        <input type="search" class="form-control pill-search form-control-lg" name="keyword" placeholder="Search resident..." required>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-success btn-pill mr-2" name="search_resident" style="width: 120px;">Search</button>
                        <a href="admn_resident_crud.php" class="btn btn-info btn-pill text-white" style="width: 120px;">Reload</a>
                    </div>
                </form>
            </div>
    <div class="row">
        <div class="col-12">
            <div class="bg-white p-4 shadow-sm" style="border-radius: 15px;">
                <?php include('search_resident.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addResidentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl shadow-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel text-center">Add New Barangay Resident</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" class="was-validated">
                <div class="modal-body bg-light">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Last Name:</label>
                            <input type="text" class="form-control" name="lname" placeholder="Enter Last Name" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>First Name:</label>
                            <input type="text" class="form-control" name="fname" placeholder="Enter First Name" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Middle Initial:</label>
                            <input type="text" class="form-control" name="mi" placeholder="Enter Middle Initial" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Contact Number:</label>
                            <input type="tel" class="form-control" name="contact" maxlength="11" pattern="[0-9]{11}" placeholder="09xxxxxxxxx" required>
                        </div>
                       <div class="col-md-4 form-group">
                            <label>Email or Phone Number:</label>
                            <input type="text" class="form-control" name="login_identity" placeholder="Enter Email or Phone Number" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Password:</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group"><label>House No:</label><input type="text" class="form-control" name="houseno" required></div>
                        <div class="col-md-3 form-group"><label>Street:</label><input type="text" class="form-control" name="street" required></div>
                        <div class="col-md-3 form-group"><label>Barangay:</label><input type="text" class="form-control" name="brgy" required></div>
                        <div class="col-md-3 form-group"><label>Municipality:</label><input type="text" class="form-control" name="municipal" required></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group"><label>Birth Date:</label><input type="date" class="form-control" name="bdate" required></div>
                        <div class="col-md-4 form-group"><label>Birth Place:</label><input type="text" class="form-control" name="bplace" required></div>
                        <div class="col-md-4 form-group"><label>Nationality:</label><input type="text" class="form-control" name="nationality" required></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Status:</label>
                            <select class="form-control" name="status" required>
                                <option value="">Choose Status...</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Age:</label>
                            <input type="number" class="form-control" name="age" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Sex:</label>
                            <select class="form-control" name="sex" required>
                                <option value="">Choose Sex...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Registered Voter?</label>
                            <select class="form-control" name="voter" required>
                                <option value="">...</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Head of Family?</label>
                            <select class="form-control" name="family_role" required>
                                <option value="">...</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="role" value="resident">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:30px;">Close</button>
                    <button type="submit" name="add_resident" class="btn btn-primary" style="width: 150px; border-radius:30px;">Submit Data</button>
                </div>
            </form>
        </div>
    </div>
</div>


    
    <!-- /.container-fluid -->
    
</div>
<!-- End of Main Content -->

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

<?php 
    include('dashboard_sidebar_end.php');
?>
