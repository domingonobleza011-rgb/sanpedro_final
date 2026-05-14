<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once('classes/conn.php');
       require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();

    // ── Type labels & icons ──────────────────────────────────
    $type_meta = [
        'resident'        => ['label' => 'Resident',              'icon' => 'fa-users',          'color' => 'blue'],
        'certofres'       => ['label' => 'Cert. of Residency',    'icon' => 'fa-file-alt',        'color' => 'teal'],
        'certofindigency' => ['label' => 'Cert. of Indigency',    'icon' => 'fa-file-alt',        'color' => 'teal'],
        'clearance'       => ['label' => 'Brgy. Clearance',       'icon' => 'fa-file',            'color' => 'gold'],
        'bspermit'        => ['label' => 'Business Permit',       'icon' => 'fa-file-contract',   'color' => 'gold'],
        'blotter'         => ['label' => 'Peace & Order Report',  'icon' => 'fa-user-shield',     'color' => 'red'],
        'youth'           => ['label' => 'Youth Profile',         'icon' => 'fa-users',           'color' => 'teal'],
        'brgyid'          => ['label' => 'Barangay ID',           'icon' => 'fa-id-card',         'color' => 'blue'],
        'staff'           => ['label' => 'Staff',                 'icon' => 'fa-user-tie',        'color' => 'blue'],
    ];

    // ── Handle permanent delete ──────────────────────────────
    if (isset($_POST['permanent_delete']) && !empty($_POST['id_archive'])) {
        $id = (int)$_POST['id_archive'];
        $stmt = $conn->prepare("DELETE FROM tbl_archive WHERE id_archive = ?");
        $stmt->execute([$id]);
        echo "<script>alert('Record permanently deleted.'); window.location.href='admn_archive.php';</script>";
        exit;
    }

    // ── Handle restore (re-insert) ───────────────────────────
    if (isset($_POST['restore_record']) && !empty($_POST['id_archive'])) {
        $id   = (int)$_POST['id_archive'];
        $row  = $conn->prepare("SELECT * FROM tbl_archive WHERE id_archive = ?");
        $row->execute([$id]);
        $arc  = $row->fetch(PDO::FETCH_ASSOC);

        $restored = false;
        if ($arc && !$arc['is_restored']) {
            $data = json_decode($arc['record_data'], true);
            $type = $arc['record_type'];

            // Build restore INSERT per type
            try {
                switch ($type) {
                    case 'resident':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_resident
                            (id_resident,email,lname,fname,mi,age,sex,status,houseno,street,brgy,municipal,contact,bdate,bplace,nationality,voter,family_role,role,addedby)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_resident'],$data['email'],$data['lname'],$data['fname'],$data['mi'],$data['age'],$data['sex'],$data['status'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['contact'],$data['bdate'],$data['bplace'],$data['nationality'],$data['voter'],$data['family_role'],$data['role'],$data['addedby']]);
                        $restored = true; break;
                    case 'certofres':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_rescert
                            (id_rescert,id_resident,lname,fname,mi,age,nationality,houseno,street,brgy,municipal,date,purpose)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_rescert'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['age'],$data['nationality'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['date'],$data['purpose']]);
                        $restored = true; break;
                    case 'certofindigency':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_indigency
                            (id_indigency,id_resident,lname,fname,mi,nationality,houseno,street,brgy,municipal,purpose,date)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_indigency'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['nationality'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['purpose'],$data['date']]);
                        $restored = true; break;
                    case 'clearance':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_clearance
                            (id_clearance,id_resident,lname,fname,mi,purpose,houseno,street,brgy,municipal,status,age)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_clearance'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['purpose'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['status'],$data['age']]);
                        $restored = true; break;
                    case 'bspermit':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_bspermit
                            (id_bspermit,id_resident,lname,fname,mi,bsname,houseno,street,brgy,municipal,bsindustry,aoe)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_bspermit'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['bsname'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['bsindustry'],$data['aoe']]);
                        $restored = true; break;
                    case 'blotter':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_blotter
                            (id_blotter,id_resident,lname,fname,mi,houseno,street,brgy,municipal,contact,narrative)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_blotter'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['contact'],$data['narrative']]);
                        $restored = true; break;
                    case 'youth':
                        $s = $conn->prepare("INSERT IGNORE INTO tbl_youth
                            (id_youth,lname,fname,mi,age,sex,civil_status,contact_number,email_address,educ_attain,emp_status,skill_name)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                        $s->execute([$data['id_youth'],$data['lname'],$data['fname'],$data['mi'],$data['age'],$data['sex'],$data['civil_status'],$data['contact_number'],$data['email_address'],$data['educ_attain'],$data['emp_status'],$data['skill_name']]);
                        $restored = true; break;
                   case 'brgyid':
    $s = $conn->prepare("INSERT IGNORE INTO tbl_brgyid (id_brgyid, id_resident, lname, fname, mi, houseno, street, brgy, municipal, bplace, bdate, contact, relation, inc_lname, inc_fname, inc_contact) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    
    $s->execute([
        $data['id_brgyid'] ?? null,
        $data['id_resident'] ?? null,
        $data['lname'] ?? '',
        $data['fname'] ?? '',
        $data['mi'] ?? '',
        $data['houseno'] ?? '',
        $data['street'] ?? '',
        $data['brgy'] ?? '',
        $data['municipal'] ?? '',
        $data['bplace'] ?? '',
        $data['bdate'] ?? '',
        $data['contact'] ?? '',
        $data['relation'] ?? '',
        $data['inc_lname'] ?? '',   // If missing in JSON, inserts empty string
        $data['inc_fname'] ?? '',   // If missing in JSON, inserts empty string
        $data['inc_contact'] ?? ''  // If missing in JSON, inserts empty string
    ]);
    $restored = true; 
    break;
                }

                if ($restored) {
                    $upd = $conn->prepare("UPDATE tbl_archive SET is_restored=1, restored_at=NOW(), restored_by=? WHERE id_archive=?");
                    $upd->execute([$userdetails['surname'].', '.$userdetails['firstname'], $id]);
                    echo "<script>alert('Record restored successfully!'); window.location.href='admn_archive.php';</script>";
                    exit;
                }
            } catch (Exception $e) {
    // This will tell you EXACTLY which ID is the problem
    $msg = addslashes($e->getMessage());
    echo "<script>alert('SQL Error: $msg');</script>";
}
        }
    }

    // ── Filter parameters ────────────────────────────────────
    $filter_type    = isset($_GET['type'])    ? $_GET['type']    : '';
    $filter_keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $filter_status  = isset($_GET['status'])  ? $_GET['status']  : 'all'; // all | active | restored

    // ── Build query ──────────────────────────────────────────
    $where  = [];
    $params = [];

    if ($filter_type !== '') {
        $where[] = "record_type = ?";
        $params[] = $filter_type;
    }
    if ($filter_keyword !== '') {
        $where[] = "(full_name LIKE ? OR summary LIKE ?)";
        $params[] = "%$filter_keyword%";
        $params[] = "%$filter_keyword%";
    }
    if ($filter_status === 'active') {
        $where[] = "is_restored = 0";
    } elseif ($filter_status === 'restored') {
        $where[] = "is_restored = 1";
    }

    $sql   = "SELECT * FROM tbl_archive";
    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql  .= " ORDER BY deleted_at DESC";

    $stmt  = $conn->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ── Counts per type ──────────────────────────────────────
    $counts_stmt = $conn->query("SELECT record_type, COUNT(*) as cnt FROM tbl_archive WHERE is_restored=0 GROUP BY record_type");
    $counts = [];
    foreach ($counts_stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        $counts[$c['record_type']] = $c['cnt'];
    }
    $total_archived = array_sum($counts);
?>

<?php include('dashboard_sidebar_start.php'); ?>

<!-- Shared admin pages theme -->
<link href="css/admn_pages.css" rel="stylesheet">

<style>
/* ── Archive-specific styles ── */
.archive-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    padding-bottom: 1.4rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.6rem;
}

.archive-title-group { display: flex; align-items: center; gap: 14px; }

.archive-title-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fdf3e3, #fce8c5);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: var(--gold);
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(201,148,58,0.2);
}

.archive-title-group h1 {
    font-size: 1.35rem !important;
    font-weight: 700 !important;
    color: var(--navy) !important;
    margin: 0 !important;
}

.archive-title-group h1::before { display: none !important; }

.archive-title-group .subtitle {
    font-size: 0.8rem;
    color: var(--text-light);
    margin: 2px 0 0;
}

/* Stat chips */
.archive-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.stat-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none !important;
    color: var(--text-mid) !important;
}

.stat-chip:hover, .stat-chip.active {
    border-color: var(--navy-light);
    background: var(--navy-pale);
    color: var(--navy) !important;
}

.stat-chip .chip-count {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--navy);
}

.stat-chip .chip-label { font-size: 0.75rem; }

/* Filter bar */
.filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 12px 16px;
    margin-bottom: 1.4rem;
    box-shadow: 0 2px 8px rgba(15,45,90,0.05);
}

.filter-bar .filter-search {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.filter-bar .filter-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    font-size: 0.85rem;
    pointer-events: none;
}

.filter-bar .filter-search input {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 9px 12px 9px 36px;
    font-size: 0.875rem;
    background: var(--cream);
    font-family: 'DM Sans', sans-serif;
    transition: all 0.2s;
}

.filter-bar .filter-search input:focus {
    outline: none;
    border-color: var(--navy-light);
    box-shadow: 0 0 0 3px rgba(43,94,167,0.1);
    background: var(--white);
}

.filter-bar select {
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 0.875rem;
    background: var(--cream);
    font-family: 'DM Sans', sans-serif;
    color: var(--text-dark);
    cursor: pointer;
    transition: all 0.2s;
}

.filter-bar select:focus {
    outline: none;
    border-color: var(--navy-light);
    box-shadow: 0 0 0 3px rgba(43,94,167,0.1);
}

.btn-filter-apply {
    background: linear-gradient(135deg, var(--navy), var(--navy-light));
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 9px 20px;
    font-size: 0.845rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-filter-apply:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,45,90,0.25); }

.btn-clear-filter {
    background: var(--white);
    color: var(--text-mid);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 9px 16px;
    font-size: 0.845rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
}

.btn-clear-filter:hover { background: var(--cream); color: var(--navy); }

/* Results count */
.results-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.8rem;
    flex-wrap: wrap;
    gap: 8px;
}

.results-count {
    font-size: 0.82rem;
    color: var(--text-light);
    font-weight: 500;
}

.results-count strong { color: var(--navy); }

/* Table wrapper */
.archive-table-wrap {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(15,45,90,0.08);
    border: 1px solid var(--border);
    background: var(--white);
}

.archive-table-wrap .table-responsive { border-radius: 0; box-shadow: none; border: none; }

/* Type badge */
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

.type-badge.blue    { background: var(--navy-pale);   color: var(--navy-mid); }
.type-badge.teal    { background: var(--teal-pale);   color: var(--teal);     }
.type-badge.gold    { background: #fdf3e3;             color: var(--gold);     }
.type-badge.red     { background: var(--danger-pale); color: var(--danger);   }

/* Status badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
}

.status-badge.archived { background: #fff3cd; color: #92400e; }
.status-badge.restored { background: var(--success-pale); color: var(--success); }

/* Action buttons in archive */
.btn-restore {
    background: linear-gradient(135deg, var(--success), #34d399) !important;
    color: #fff !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 5px 14px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all 0.18s !important;
    display: inline-flex; align-items: center; gap: 4px;
}

.btn-restore:hover { transform: translateY(-1px) !important; box-shadow: 0 3px 10px rgba(5,150,105,0.3) !important; }

.btn-perma-delete {
    background: var(--white) !important;
    color: var(--danger) !important;
    border: 1.5px solid rgba(220,38,38,0.3) !important;
    border-radius: 8px !important;
    padding: 5px 12px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all 0.18s !important;
    display: inline-flex; align-items: center; gap: 4px;
}

.btn-perma-delete:hover {
    background: var(--danger-pale) !important;
    border-color: var(--danger) !important;
}

/* Summary text */
.summary-text {
    font-size: 0.78rem;
    color: var(--text-light);
    max-width: 260px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Date cell */
.date-cell { font-size: 0.78rem; color: var(--text-mid); white-space: nowrap; }

/* Empty state */
.archive-empty {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--text-light);
}

.archive-empty .empty-icon {
    width: 72px; height: 72px;
    background: var(--cream);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
    color: var(--text-light);
    margin: 0 auto 1.2rem;
}

.archive-empty h5 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-mid);
    margin-bottom: 0.4rem;
}

.archive-empty p { font-size: 0.85rem; margin: 0; }

/* Full JSON detail row */
.json-detail pre {
    background: var(--cream);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.78rem;
    max-height: 220px;
    overflow-y: auto;
    color: var(--text-dark);
    text-align: left;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .archive-header { flex-direction: column; }
    .filter-bar { flex-direction: column; align-items: stretch; }
    .filter-bar .filter-search { min-width: auto; }
    .summary-text { max-width: 140px; }
}
</style>

<!-- ─── BEGIN PAGE CONTENT ─────────────────────────────────── -->
<div class="container-fluid">

    <!-- Archive Header -->
    <div class="archive-header">
        <div class="archive-title-group">
            <div class="archive-title-icon">
                <i class="fas fa-archive"></i>
            </div>
            <div>
                <h1>Archive</h1>
                <p class="subtitle">Deleted records — restore or permanently remove</p>
            </div>
        </div>

        <!-- Stats chips -->
        <div class="archive-stats">
            <a href="admn_archive.php" class="stat-chip <?= $filter_type === '' && $filter_status !== 'restored' ? 'active' : '' ?>">
                <span class="chip-count"><?= $total_archived ?></span>
                <span class="chip-label">All Archived</span>
            </a>
            <?php foreach ($type_meta as $key => $meta): if (!isset($counts[$key])) continue; ?>
            <a href="admn_archive.php?type=<?= $key ?>" class="stat-chip <?= $filter_type === $key ? 'active' : '' ?>">
                <span class="chip-count"><?= $counts[$key] ?></span>
                <span class="chip-label"><?= $meta['label'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="admn_archive.php">
        <div class="filter-bar">
            <div class="filter-search">
                <i class="fas fa-search"></i>
                <input type="text" name="keyword" placeholder="Search by name or details…" value="<?= htmlspecialchars($filter_keyword) ?>">
            </div>

            <select name="type">
                <option value="">All Types</option>
                <?php foreach ($type_meta as $key => $meta): ?>
                <option value="<?= $key ?>" <?= $filter_type === $key ? 'selected' : '' ?>><?= $meta['label'] ?></option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="all"      <?= $filter_status === 'all'      ? 'selected' : '' ?>>All Statuses</option>
                <option value="active"   <?= $filter_status === 'active'   ? 'selected' : '' ?>>Archived Only</option>
                <option value="restored" <?= $filter_status === 'restored' ? 'selected' : '' ?>>Restored Only</option>
            </select>

            <button type="submit" class="btn-filter-apply"><i class="fas fa-filter"></i> Filter</button>
            <a href="admn_archive.php" class="btn-clear-filter"><i class="fas fa-times"></i> Clear</a>
        </div>
    </form>

    <!-- Results count -->
    <div class="results-bar">
        <span class="results-count">
            Showing <strong><?= count($records) ?></strong> record<?= count($records) !== 1 ? 's' : '' ?>
            <?php if ($filter_type): ?> — filtered by <strong><?= htmlspecialchars($type_meta[$filter_type]['label'] ?? $filter_type) ?></strong><?php endif; ?>
            <?php if ($filter_keyword): ?> matching <strong>"<?= htmlspecialchars($filter_keyword) ?>"</strong><?php endif; ?>
        </span>
    </div>

    <!-- Archive Table -->
    <div class="archive-table-wrap">
        <?php if (empty($records)): ?>
            <div class="archive-empty">
                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                <h5>No archived records found</h5>
                <p>Records appear here after they are deleted from any module.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover" id="archiveTable">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Deleted</th>
                        <th>Deleted By</th>
                        <th style="min-width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $i => $rec):
                        $meta = $type_meta[$rec['record_type']] ?? ['label' => $rec['record_type'], 'icon' => 'fa-file', 'color' => 'blue'];
                        $is_restored = (bool)$rec['is_restored'];
                    ?>
                    <tr>
                        <td class="date-cell text-muted"><?= $i + 1 ?></td>

                        <td>
                            <span class="type-badge <?= $meta['color'] ?>">
                                <i class="fas <?= $meta['icon'] ?>"></i>
                                <?= $meta['label'] ?>
                            </span>
                        </td>

                        <td>
                            <div style="font-weight:600; font-size:0.875rem; color:var(--text-dark);">
                                <?= htmlspecialchars($rec['full_name']) ?>
                            </div>
                            <div style="font-size:0.72rem; color:var(--text-light);">ID #<?= $rec['record_id'] ?></div>
                        </td>

                        <td>
                            <span class="summary-text" title="<?= htmlspecialchars($rec['summary']) ?>">
                                <?= htmlspecialchars($rec['summary'] ?? '—') ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($is_restored): ?>
                                <span class="status-badge restored"><i class="fas fa-check-circle"></i> Restored</span>
                                <div style="font-size:0.7rem; color:var(--text-light); margin-top:3px;">
                                    by <?= htmlspecialchars($rec['restored_by'] ?? '—') ?><br>
                                    <?= $rec['restored_at'] ? date('M d, Y', strtotime($rec['restored_at'])) : '' ?>
                                </div>
                            <?php else: ?>
                                <span class="status-badge archived"><i class="fas fa-archive"></i> Archived</span>
                            <?php endif; ?>
                        </td>

                        <td class="date-cell">
                            <?= date('M d, Y', strtotime($rec['deleted_at'])) ?><br>
                            <span style="color:var(--text-light); font-size:0.7rem;"><?= date('h:i A', strtotime($rec['deleted_at'])) ?></span>
                        </td>

                        <td class="date-cell"><?= htmlspecialchars($rec['deleted_by'] ?? '—') ?></td>

                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">

                                <!-- View Details toggle -->
                                <button type="button"
                                        class="btn-perma-delete"
                                        style="border-color:rgba(15,45,90,0.2) !important; color:var(--navy-mid) !important;"
                                        onclick="toggleDetail(<?= $rec['id_archive'] ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>

                                <!-- Restore (only if not already restored) -->
                                <?php if (!$is_restored): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Restore this record to the source table?');">
                                    <input type="hidden" name="id_archive"      value="<?= $rec['id_archive'] ?>">
                                    <button type="submit" name="restore_record" class="btn-restore">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                                <?php endif; ?>

                                <!-- Permanent delete -->
                                <form method="POST" style="display:inline;" onsubmit="return confirm('PERMANENTLY delete this record? This cannot be undone.');">
                                    <input type="hidden" name="id_archive"       value="<?= $rec['id_archive'] ?>">
                                    <button type="submit" name="permanent_delete" class="btn-perma-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Expandable JSON detail row -->
                    <tr id="detail-<?= $rec['id_archive'] ?>" style="display:none; background:var(--cream);">
                        <td colspan="8" class="json-detail">
                            <strong style="font-size:0.78rem; color:var(--navy); display:block; margin-bottom:6px;">
                                Full Record Data
                            </strong>
                            <pre><?php
                                $json_data = json_decode($rec['record_data'], true);
                                echo htmlspecialchars(json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            ?></pre>
                        </td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <br><br>
</div>
<!-- End of Main Content -->

<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (row) {
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    }
}
</script>

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

<?php include('dashboard_sidebar_end.php'); ?>
