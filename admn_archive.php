<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once('classes/conn.php');
    require('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();

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

    // ── Handle permanent delete (single) ────────────────────
    if (isset($_POST['permanent_delete']) && !empty($_POST['id_archive'])) {
        $id = (int)$_POST['id_archive'];
        $stmt = $conn->prepare("DELETE FROM tbl_archive WHERE id_archive = ?");
        $stmt->execute([$id]);
        echo "<script>sessionStorage.setItem('archiveToast', JSON.stringify({type:'delete',msg:'Record permanently deleted.'})); window.location.href='admn_archive.php';</script>";
        exit;
    }

    // ── Handle bulk permanent delete ─────────────────────────
    if (isset($_POST['bulk_permanent_delete']) && !empty($_POST['selected_archives'])) {
        $ids = array_filter($_POST['selected_archives'], 'is_numeric');
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $conn->prepare("DELETE FROM tbl_archive WHERE id_archive IN ($placeholders)");
            $stmt->execute(array_values($ids));
            $count = $stmt->rowCount();
            $msg = "$count record(s) permanently deleted.";
            echo "<script>sessionStorage.setItem('archiveToast', JSON.stringify({type:'delete',msg:" . json_encode($msg) . "})); window.location.href='admn_archive.php';</script>";
            exit;
        }
    }

    // ── Helper: restore one archive row by its data ─────────
    function restore_single_row($conn, $arc) {
        $data = json_decode($arc['record_data'], true);
        $type = $arc['record_type'];
        switch ($type) {

            case 'staff':
                $s = $conn->prepare("
                    INSERT INTO tbl_user
                        (id_user, login_identity, email, phone_number, password,
                         lname, fname, mi, age, sex, address, contact,
                         position, role, addedby, photo)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE
                        login_identity = VALUES(login_identity),
                        email          = VALUES(email),
                        phone_number   = VALUES(phone_number),
                        password       = VALUES(password),
                        lname          = VALUES(lname),
                        fname          = VALUES(fname),
                        mi             = VALUES(mi),
                        age            = VALUES(age),
                        sex            = VALUES(sex),
                        address        = VALUES(address),
                        contact        = VALUES(contact),
                        position       = VALUES(position),
                        role           = VALUES(role),
                        addedby        = VALUES(addedby),
                        photo          = VALUES(photo)
                ");
                $s->execute([
                    $data['id_user']        ?? null,
                    $data['login_identity'] ?? '',
                    $data['email']          ?? '',
                    $data['phone_number']   ?? '',
                    $data['password']       ?? '',
                    $data['lname']          ?? '',
                    $data['fname']          ?? '',
                    $data['mi']             ?? '',
                    $data['age']            ?? 0,
                    $data['sex']            ?? '',
                    $data['address']        ?? '',
                    $data['contact']        ?? '',
                    $data['position']       ?? '',
                    $data['role']           ?? '',
                    $data['addedby']        ?? '',
                    $data['photo']          ?? '',
                ]);
                return true;

            case 'resident':
                $s = $conn->prepare("
                    INSERT INTO tbl_resident
                        (id_resident, email, phone_number, password,
                         lname, fname, mi, age, sex, status,
                         houseno, street, brgy, municipal, address, contact,
                         bdate, bplace, nationality, voter, family_role, role,
                         is_verified, verified_at, verified_by, addedby)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE
                        email        = VALUES(email),
                        phone_number = VALUES(phone_number),
                        password     = VALUES(password),
                        lname        = VALUES(lname),
                        fname        = VALUES(fname),
                        mi           = VALUES(mi),
                        age          = VALUES(age),
                        sex          = VALUES(sex),
                        status       = VALUES(status),
                        houseno      = VALUES(houseno),
                        street       = VALUES(street),
                        brgy         = VALUES(brgy),
                        municipal    = VALUES(municipal),
                        address      = VALUES(address),
                        contact      = VALUES(contact),
                        bdate        = VALUES(bdate),
                        bplace       = VALUES(bplace),
                        nationality  = VALUES(nationality),
                        voter        = VALUES(voter),
                        family_role  = VALUES(family_role),
                        role         = VALUES(role),
                        is_verified  = VALUES(is_verified),
                        verified_at  = VALUES(verified_at),
                        verified_by  = VALUES(verified_by),
                        addedby      = VALUES(addedby)
                ");
                $s->execute([
                    $data['id_resident']    ?? null,
                    $data['email']          ?? null,
                    $data['phone_number']   ?? null,
                    $data['password']       ?? '',
                    $data['lname']          ?? '',
                    $data['fname']          ?? '',
                    $data['mi']             ?? '',
                    $data['age']            ?? 0,
                    $data['sex']            ?? '',
                    $data['status']         ?? '',
                    $data['houseno']        ?? null,
                    $data['street']         ?? null,
                    $data['brgy']           ?? null,
                    $data['municipal']      ?? null,
                    $data['address']        ?? null,
                    $data['contact']        ?? null,
                    $data['bdate']          ?? null,
                    $data['bplace']         ?? '',
                    $data['nationality']    ?? '',
                    $data['voter']          ?? '',
                    $data['family_role']    ?? '',
                    $data['role']           ?? 'resident',
                    $data['is_verified']    ?? 0,
                    $data['verified_at']    ?? null,
                    $data['verified_by']    ?? null,
                    $data['addedby']        ?? '',
                ]);
                return true;

            case 'certofres':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_rescert
                    (id_rescert,id_resident,lname,fname,mi,age,nationality,houseno,street,brgy,municipal,date,purpose)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$data['id_rescert'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['age'],$data['nationality'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['date'],$data['purpose']]);
                return true;

            case 'certofindigency':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_indigency
                    (id_indigency,id_resident,lname,fname,mi,nationality,houseno,street,brgy,municipal,purpose,date)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$data['id_indigency'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['nationality'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['purpose'],$data['date']]);
                return true;

            case 'clearance':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_clearance
                    (id_clearance,id_resident,lname,fname,mi,purpose,houseno,street,brgy,municipal,status,age)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$data['id_clearance'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['purpose'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['status'],$data['age']]);
                return true;

            case 'bspermit':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_bspermit
                    (id_bspermit,id_resident,lname,fname,mi,bsname,houseno,street,brgy,municipal,bsindustry,aoe)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$data['id_bspermit'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['bsname'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['bsindustry'],$data['aoe']]);
                return true;

            case 'blotter':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_blotter
                    (id_blotter,id_resident,lname,fname,mi,houseno,street,brgy,municipal,contact,narrative)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$data['id_blotter'],$data['id_resident'],$data['lname'],$data['fname'],$data['mi'],$data['houseno'],$data['street'],$data['brgy'],$data['municipal'],$data['contact'],$data['narrative']]);
                return true;

            case 'youth':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_youth
                    (id_youth,lname,fname,mi,age,sex,civil_status,contact_number,email_address,educ_attain,emp_status,skill_name)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([$data['id_youth'],$data['lname'],$data['fname'],$data['mi'],$data['age'],$data['sex'],$data['civil_status'],$data['contact_number'],$data['email_address'],$data['educ_attain'],$data['emp_status'],$data['skill_name']]);
                return true;

            case 'brgyid':
                $s = $conn->prepare("INSERT IGNORE INTO tbl_brgyid
                    (id_brgyid,id_resident,lname,fname,mi,houseno,street,brgy,municipal,bplace,bdate,contact,relation,inc_lname,inc_fname,inc_contact)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $s->execute([
                    $data['id_brgyid']    ?? null, $data['id_resident']  ?? null,
                    $data['lname']        ?? '',   $data['fname']         ?? '',
                    $data['mi']           ?? '',   $data['houseno']       ?? '',
                    $data['street']       ?? '',   $data['brgy']          ?? '',
                    $data['municipal']    ?? '',   $data['bplace']        ?? '',
                    $data['bdate']        ?? '',   $data['contact']       ?? '',
                    $data['relation']     ?? '',   $data['inc_lname']     ?? '',
                    $data['inc_fname']    ?? '',   $data['inc_contact']   ?? '',
                ]);
                return true;
        }
        return false;
    }

    // ── Handle bulk restore ──────────────────────────────────
    if (isset($_POST['bulk_restore']) && !empty($_POST['selected_archives'])) {
        $ids = array_filter($_POST['selected_archives'], 'is_numeric');
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $rows_stmt = $conn->prepare("SELECT * FROM tbl_archive WHERE id_archive IN ($placeholders)");
            $rows_stmt->execute(array_values($ids));
            $rows = $rows_stmt->fetchAll(PDO::FETCH_ASSOC);

            $success = 0;
            $failed  = 0;
            $restored_ids = [];

            foreach ($rows as $arc) {
                try {
                    if (restore_single_row($conn, $arc)) {
                        $restored_ids[] = $arc['id_archive'];
                        $success++;
                    }
                } catch (Exception $e) {
                    $failed++;
                }
            }

            if (!empty($restored_ids)) {
                $del_placeholders = implode(',', array_fill(0, count($restored_ids), '?'));
                $conn->prepare("DELETE FROM tbl_archive WHERE id_archive IN ($del_placeholders)")
                     ->execute($restored_ids);
            }

            $msg = "$success record(s) restored successfully.";
            if ($failed > 0) $msg .= " $failed failed.";
            echo "<script>sessionStorage.setItem('archiveToast', JSON.stringify({type:'restore',msg:" . json_encode($msg) . "})); window.location.href='admn_archive.php';</script>";
            exit;
        }
    }

    // ── Handle restore (re-insert) ───────────────────────────
    if (isset($_POST['restore_record']) && !empty($_POST['id_archive'])) {
        $id  = (int)$_POST['id_archive'];
        $row = $conn->prepare("SELECT * FROM tbl_archive WHERE id_archive = ?");
        $row->execute([$id]);
        $arc = $row->fetch(PDO::FETCH_ASSOC);

        $restored = false;
        if ($arc) {
            try {
                $restored = restore_single_row($conn, $arc);

                if ($restored) {
                    $del = $conn->prepare("DELETE FROM tbl_archive WHERE id_archive = ?");
                    $del->execute([$id]);
                    echo "<script>sessionStorage.setItem('archiveToast', JSON.stringify({type:'restore',msg:'Record restored successfully!'})); window.location.href='admn_archive.php';</script>";
                    exit;
                }
            } catch (Exception $e) {
                $msg = addslashes($e->getMessage());
                echo "<script>alert('SQL Error: $msg');</script>";
            }
        }
    }

    // ── Filter parameters ────────────────────────────────────
    $filter_type    = isset($_GET['type'])    ? $_GET['type']    : '';
    $filter_keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $filter_status  = isset($_GET['status'])  ? $_GET['status']  : 'all';

    // ── Build query ──────────────────────────────────────────
    $where  = [];
    $params = [];

    if ($filter_type !== '') {
        $where[]  = "record_type = ?";
        $params[] = $filter_type;
    }
    if ($filter_keyword !== '') {
        $where[]  = "(full_name LIKE ? OR summary LIKE ?)";
        $params[] = "%$filter_keyword%";
        $params[] = "%$filter_keyword%";
    }
    $sql = "SELECT * FROM tbl_archive";
    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY deleted_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ── Counts per type ──────────────────────────────────────
    $counts_stmt = $conn->query("SELECT record_type, COUNT(*) as cnt FROM tbl_archive GROUP BY record_type");
    $counts = [];
    foreach ($counts_stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        $counts[$c['record_type']] = $c['cnt'];
    }
    $total_archived = array_sum($counts);
?>

<?php include('dashboard_sidebar_start.php'); ?>

<link href="css/admn_pages.css" rel="stylesheet">

<style>
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
.archive-stats { display: flex; flex-wrap: wrap; gap: 10px; }
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
.stat-chip .chip-count { font-weight: 700; font-size: 0.95rem; color: var(--navy); }
.stat-chip .chip-label { font-size: 0.75rem; }
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
.filter-bar .filter-search { flex: 1; min-width: 200px; position: relative; }
.filter-bar .filter-search i {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-light); font-size: 0.85rem; pointer-events: none;
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
    color: #fff; border: none; border-radius: 10px;
    padding: 9px 20px; font-size: 0.845rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.btn-filter-apply:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,45,90,0.25); }
.btn-clear-filter {
    background: var(--white); color: var(--text-mid);
    border: 1.5px solid var(--border); border-radius: 10px;
    padding: 9px 16px; font-size: 0.845rem; font-weight: 500;
    cursor: pointer; transition: all 0.2s; text-decoration: none; white-space: nowrap;
}
.btn-clear-filter:hover { background: var(--cream); color: var(--navy); }
.results-bar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 0.8rem; flex-wrap: wrap; gap: 8px;
}
.results-count { font-size: 0.82rem; color: var(--text-light); font-weight: 500; }
.results-count strong { color: var(--navy); }
.archive-table-wrap {
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 2px 16px rgba(15,45,90,0.08);
    border: 1px solid var(--border); background: var(--white);
}
.archive-table-wrap .table-responsive { border-radius: 0; box-shadow: none; border: none; }
.type-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px;
    text-transform: uppercase; padding: 4px 10px; border-radius: 20px; white-space: nowrap;
}
.type-badge.blue  { background: var(--navy-pale);   color: var(--navy-mid); }
.type-badge.teal  { background: var(--teal-pale);   color: var(--teal);     }
.type-badge.gold  { background: #fdf3e3;             color: var(--gold);     }
.type-badge.red   { background: var(--danger-pale); color: var(--danger);   }
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.7rem; font-weight: 600; padding: 4px 10px; border-radius: 20px;
}
.status-badge.archived { background: #fff3cd; color: #92400e; }
.status-badge.restored { background: var(--success-pale); color: var(--success); }
.btn-restore {
    background: linear-gradient(135deg, var(--success), #34d399) !important;
    color: #ffffff !important; border: none !important; border-radius: 8px !important;
    padding: 5px 14px !important; font-size: 0.78rem !important; font-weight: 600 !important;
    cursor: pointer; transition: all 0.18s !important;
    display: inline-flex; align-items: center; gap: 4px;
}
.btn-restore:hover { transform: translateY(-1px) !important; box-shadow: 0 3px 10px rgba(5,150,105,0.3) !important; }
.btn-perma-delete {
    background: var(--white) !important; color: var(--danger) !important;
    border: 1.5px solid rgba(220,38,38,0.3) !important; border-radius: 8px !important;
    padding: 5px 12px !important; font-size: 0.78rem !important; font-weight: 600 !important;
    cursor: pointer; transition: all 0.18s !important;
    display: inline-flex; align-items: center; gap: 4px;
}
.btn-perma-delete:hover { background: var(--danger-pale) !important; border-color: var(--danger) !important; }
.summary-text {
    font-size: 0.78rem; color: var(--text-light);
    max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.date-cell { font-size: 0.78rem; color: var(--text-mid); white-space: nowrap; }
.archive-empty { text-align: center; padding: 4rem 1rem; color: var(--text-light); }
.archive-empty .empty-icon {
    width: 72px; height: 72px; background: var(--cream); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: var(--text-light); margin: 0 auto 1.2rem;
}
.archive-empty h5 { font-size: 1rem; font-weight: 600; color: var(--text-mid); margin-bottom: 0.4rem; }
.archive-empty p { font-size: 0.85rem; margin: 0; }
.json-detail pre {
    background: var(--cream); border: 1px solid var(--border); border-radius: 10px;
    padding: 12px 16px; font-size: 0.78rem; max-height: 220px; overflow-y: auto;
    color: var(--text-dark); text-align: left; margin: 0;
}
/* Bulk toolbar */
.bulk-toolbar {
    display: none; align-items: center; gap: 10px; flex-wrap: wrap;
    background: #fff5f5; border: 1.5px solid rgba(220,38,38,0.3);
    border-radius: 12px; padding: 10px 16px; margin-bottom: 1rem;
}
.bulk-toolbar .bulk-count { font-weight: 700; font-size: 0.875rem; color: var(--danger); margin-right: 4px; }
.btn-bulk-restore {
    background: linear-gradient(135deg, var(--success), #32a30f);
    color: #fff; border: none; border-radius: 8px;
    padding: 7px 18px; font-size: 0.82rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-bulk-restore:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(60, 255, 0, 0.3); }
.btn-bulk-delete {
    background: linear-gradient(135deg, var(--danger), #ef4444);
    color: #fff; border: none; border-radius: 8px;
    padding: 7px 18px; font-size: 0.82rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-bulk-delete:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.3); }
.btn-bulk-clear {
    background: var(--white); color: var(--text-mid);
    border: 1.5px solid var(--border); border-radius: 8px;
    padding: 7px 14px; font-size: 0.82rem; font-weight: 500; cursor: pointer; transition: all 0.2s;
}
.btn-bulk-clear:hover { background: var(--cream); color: var(--navy); }
.check-col { width: 44px; text-align: center !important; }
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
            <div class="archive-title-icon"><i class="fas fa-archive"></i></div>
            <div>
                <h1>Archive</h1>
                <p class="subtitle">Deleted records — restore or permanently remove</p>
            </div>
        </div>
        <div class="archive-stats">
            <a href="admn_archive.php" class="stat-chip <?= $filter_type === '' ? 'active' : '' ?>">
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

    <!-- ══ Single-action form: lives OUTSIDE the bulk form ══ -->
    <form method="POST" action="admn_archive.php" id="singleActionForm">
        <input type="hidden" name="id_archive" id="singleActionId">
        <input type="hidden" id="singleActionName">
    </form>

    <!-- ══ Bulk form: wraps toolbar + table ══ -->
    <form method="POST" action="admn_archive.php" id="bulkForm">

        <!-- Bulk Toolbar (hidden until rows are checked) -->
        <div class="bulk-toolbar" id="bulkToolbar">
            <span class="bulk-count" id="bulkCount">0 selected</span>
            <button type="button" class="btn-bulk-restore" onclick="openBulkModal('restore')">
                <i class="fas fa-undo"></i> Restore Selected
            </button>
            <button type="button" class="btn-bulk-delete" onclick="openBulkModal('delete')">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <button type="button" class="btn-bulk-clear" onclick="clearAllChecks()">
                <i class="fas fa-times"></i> Clear
            </button>
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
                            <th class="check-col">
                                <input type="checkbox" id="checkAll" title="Select all"
                                    style="width:16px;height:16px;cursor:pointer;">
                            </th>
                            <th style="width:40px;">#</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Details</th>
                            <th>Deleted</th>
                            <th>Deleted By</th>
                            <th style="min-width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $i => $rec):
                            $meta = $type_meta[$rec['record_type']] ?? ['label' => $rec['record_type'], 'icon' => 'fa-file', 'color' => 'blue'];
                        ?>
                        <tr>
                            <td class="check-col">
                                <input type="checkbox" name="selected_archives[]"
                                    value="<?= $rec['id_archive'] ?>"
                                    class="row-check"
                                    style="width:16px;height:16px;cursor:pointer;">
                            </td>
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

                                    <!-- Restore button -->
                                    <button type="button" class="btn-restore"
                                        onclick="submitSingle('restore_record', <?= $rec['id_archive'] ?>,
                                        '<?= addslashes(htmlspecialchars($rec['full_name'])) ?>',
                                        '<?= addslashes($meta['label']) ?> · ID #<?= $rec['record_id'] ?>')">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>

                                    <!-- Permanent delete button -->
                                    <button type="button" class="btn-perma-delete"
                                        onclick="submitSingle('permanent_delete', <?= $rec['id_archive'] ?>,
                                        '<?= addslashes(htmlspecialchars($rec['full_name'])) ?>',
                                        '<?= addslashes($meta['label']) ?> · ID #<?= $rec['record_id'] ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>

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
        </div><!-- /.archive-table-wrap -->

    </form><!-- /#bulkForm -->

    <!-- ══ MODALS — declared ONCE, outside the loop and outside the table ══ -->

    <!-- Restore Modal -->
    <div id="restoreModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:14px; padding:28px 32px; width:100%; max-width:420px; margin:0 16px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
          <div style="width:40px; height:40px; border-radius:10px; background:#d1fae5; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-undo" style="color:#059669; font-size:18px;"></i>
          </div>
          <div>
            <p style="font-size:15px; font-weight:600; margin:0; color:#0f2d5a;">Restore record</p>
            <p style="font-size:13px; color:#6b7280; margin:0;">This will move the record back to its source table.</p>
          </div>
        </div>
        <hr style="margin:16px 0; border-color:#e5e7eb;">
        <div style="background:#f9fafb; border-radius:8px; padding:10px 14px; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
          <i class="fas fa-user" style="color:#9ca3af; font-size:15px;"></i>
          <div>
            <p id="restoreRecordName" style="font-size:13px; font-weight:600; margin:0; color:#111827;"></p>
            <p id="restoreRecordMeta" style="font-size:12px; color:#6b7280; margin:0;"></p>
          </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
          <button onclick="closeModal('restoreModal')" style="padding:8px 18px; font-size:13px; border-radius:8px; cursor:pointer; border:1px solid #d1d5db; background:#fff; color:#6b7280;">Cancel</button>
          <button id="restoreConfirmBtn" style="padding:8px 18px; font-size:13px; font-weight:600; border-radius:8px; cursor:pointer; border:none; background:#d1fae5; color:#065f46;">
            <i class="fas fa-undo" style="margin-right:5px;"></i>Yes, restore
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:14px; padding:28px 32px; width:100%; max-width:420px; margin:0 16px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
          <div style="width:40px; height:40px; border-radius:10px; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-trash" style="color:#dc2626; font-size:18px;"></i>
          </div>
          <div>
            <p style="font-size:15px; font-weight:600; margin:0; color:#0f2d5a;">Permanently delete</p>
            <p style="font-size:13px; color:#6b7280; margin:0;">This action cannot be undone.</p>
          </div>
        </div>
        <hr style="margin:16px 0; border-color:#e5e7eb;">
        <div style="background:#f9fafb; border-radius:8px; padding:10px 14px; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
          <i class="fas fa-user" style="color:#9ca3af; font-size:15px;"></i>
          <div>
            <p id="deleteRecordName" style="font-size:13px; font-weight:600; margin:0; color:#111827;"></p>
            <p id="deleteRecordMeta" style="font-size:12px; color:#6b7280; margin:0;"></p>
          </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
          <button onclick="closeModal('deleteModal')" style="padding:8px 18px; font-size:13px; border-radius:8px; cursor:pointer; border:1px solid #d1d5db; background:#fff; color:#6b7280;">Cancel</button>
          <button id="deleteConfirmBtn" style="padding:8px 18px; font-size:13px; font-weight:600; border-radius:8px; cursor:pointer; border:none; background:#fee2e2; color:#991b1b;">
            <i class="fas fa-trash" style="margin-right:5px;"></i>Yes, delete
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Restore Modal -->
    <div id="bulkRestoreModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:14px; padding:28px 32px; width:100%; max-width:440px; margin:0 16px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
          <div style="width:40px; height:40px; border-radius:10px; background:#d1fae5; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-undo" style="color:#059669; font-size:18px;"></i>
          </div>
          <div>
            <p style="font-size:15px; font-weight:600; margin:0; color:#0f2d5a;">Restore selected records</p>
            <p style="font-size:13px; color:#6b7280; margin:0;">All selected records will be moved back to their source tables.</p>
          </div>
        </div>
        <hr style="margin:16px 0; border-color:#e5e7eb;">
        <div style="background:#f0fdf4; border:1.5px solid #bbf7d0; border-radius:8px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:12px;">
          <i class="fas fa-layer-group" style="color:#059669; font-size:18px; flex-shrink:0;"></i>
          <div>
            <p id="bulkRestoreCount" style="font-size:14px; font-weight:700; margin:0; color:#065f46;"></p>
            <p style="font-size:12px; color:#047857; margin:2px 0 0;">Records will be restored to their original tables.</p>
          </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
          <button onclick="closeModal('bulkRestoreModal')" style="padding:8px 18px; font-size:13px; border-radius:8px; cursor:pointer; border:1px solid #d1d5db; background:#fff; color:#6b7280;">Cancel</button>
          <button id="bulkRestoreConfirmBtn" style="padding:8px 20px; font-size:13px; font-weight:600; border-radius:8px; cursor:pointer; border:none; background:linear-gradient(135deg,#059669,#34d399); color:#fff;">
            <i class="fas fa-undo" style="margin-right:5px;"></i>Yes, restore all
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div id="bulkDeleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:14px; padding:28px 32px; width:100%; max-width:440px; margin:0 16px; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
          <div style="width:40px; height:40px; border-radius:10px; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-trash" style="color:#dc2626; font-size:18px;"></i>
          </div>
          <div>
            <p style="font-size:15px; font-weight:600; margin:0; color:#0f2d5a;">Permanently delete selected</p>
            <p style="font-size:13px; color:#6b7280; margin:0;">This action cannot be undone.</p>
          </div>
        </div>
        <hr style="margin:16px 0; border-color:#e5e7eb;">
        <div style="background:#fef2f2; border:1.5px solid #fecaca; border-radius:8px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:12px;">
          <i class="fas fa-exclamation-triangle" style="color:#dc2626; font-size:18px; flex-shrink:0;"></i>
          <div>
            <p id="bulkDeleteCount" style="font-size:14px; font-weight:700; margin:0; color:#991b1b;"></p>
            <p style="font-size:12px; color:#b91c1c; margin:2px 0 0;">These records will be gone forever — there is no way to recover them.</p>
          </div>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
          <button onclick="closeModal('bulkDeleteModal')" style="padding:8px 18px; font-size:13px; border-radius:8px; cursor:pointer; border:1px solid #d1d5db; background:#fff; color:#6b7280;">Cancel</button>
          <button id="bulkDeleteConfirmBtn" style="padding:8px 20px; font-size:13px; font-weight:600; border-radius:8px; cursor:pointer; border:none; background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff;">
            <i class="fas fa-trash" style="margin-right:5px;"></i>Yes, delete all
          </button>
        </div>
      </div>
    </div>

    <!-- ══ SUCCESS TOAST MODAL ══ -->
    <div id="successToast" style="display:none; position:fixed; bottom:28px; right:28px; z-index:10000; min-width:300px; max-width:400px;">
      <div id="successToastInner" style="border-radius:14px; padding:16px 20px; box-shadow:0 8px 32px rgba(0,0,0,0.18); display:flex; align-items:flex-start; gap:14px; animation: toastSlideIn 0.3s ease;">
        <div id="successToastIcon" style="width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">
          <i id="successToastIconI" style="font-size:16px;"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <p id="successToastTitle" style="font-size:13px; font-weight:700; margin:0 0 2px; color:#111827;"></p>
          <p id="successToastMsg"   style="font-size:12px; margin:0; color:#6b7280; line-height:1.4;"></p>
        </div>
        <button onclick="closeToast()" style="background:none; border:none; cursor:pointer; color:#9ca3af; font-size:16px; padding:0; flex-shrink:0; line-height:1;">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <style>
    @keyframes toastSlideIn {
        from { opacity:0; transform: translateY(16px); }
        to   { opacity:1; transform: translateY(0); }
    }
    </style>

    <br><br>
</div>
<!-- End of Main Content -->

<script>
// ── Success toast ────────────────────────────────────────────
function closeToast() {
    var t = document.getElementById('successToast');
    if (t) { t.style.opacity = '0'; t.style.transition = 'opacity 0.3s'; setTimeout(function(){ t.style.display = 'none'; }, 300); }
}

document.addEventListener('DOMContentLoaded', function () {
    var raw = sessionStorage.getItem('archiveToast');
    if (!raw) return;
    sessionStorage.removeItem('archiveToast');

    try {
        var data  = JSON.parse(raw);
        var isRed = data.type === 'delete';

        var bg        = isRed ? '#fef2f2'  : '#f0fdf4';
        var border    = isRed ? '#fecaca'  : '#bbf7d0';
        var iconBg    = isRed ? '#fee2e2'  : '#d1fae5';
        var iconColor = isRed ? '#dc2626'  : '#059669';
        var iconCls   = isRed ? 'fa-trash' : 'fa-check';
        var title     = isRed ? 'Deleted'  : 'Restored';

        var inner = document.getElementById('successToastInner');
        inner.style.background  = bg;
        inner.style.border      = '1.5px solid ' + border;

        var iconWrap = document.getElementById('successToastIcon');
        iconWrap.style.background = iconBg;

        var iconEl = document.getElementById('successToastIconI');
        iconEl.className    = 'fas ' + iconCls;
        iconEl.style.color  = iconColor;

        document.getElementById('successToastTitle').textContent = title;
        document.getElementById('successToastTitle').style.color = iconColor;
        document.getElementById('successToastMsg').textContent   = data.msg;

        document.getElementById('successToast').style.display = 'block';

        // auto-dismiss after 4 seconds
        setTimeout(closeToast, 4000);
    } catch(e) {}
});

// ── Toggle JSON detail row ───────────────────────────────────
function toggleDetail(id) {
    var row = document.getElementById('detail-' + id);
    if (row) row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}

// ── Close modal ──────────────────────────────────────────────
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// ── Single-action modal trigger ──────────────────────────────
function submitSingle(actionName, archiveId, fullName, typeMeta) {
    if (actionName === 'restore_record') {
        document.getElementById('restoreRecordName').textContent = fullName;
        document.getElementById('restoreRecordMeta').textContent = typeMeta;
        document.getElementById('restoreModal').style.display    = 'flex';
        document.getElementById('restoreConfirmBtn').onclick = function () {
            document.getElementById('singleActionId').value  = archiveId;
            document.getElementById('singleActionName').name = 'restore_record';
            document.getElementById('singleActionForm').submit();
        };
    } else {
        document.getElementById('deleteRecordName').textContent = fullName;
        document.getElementById('deleteRecordMeta').textContent = typeMeta;
        document.getElementById('deleteModal').style.display    = 'flex';
        document.getElementById('deleteConfirmBtn').onclick = function () {
            document.getElementById('singleActionId').value  = archiveId;
            document.getElementById('singleActionName').name = 'permanent_delete';
            document.getElementById('singleActionForm').submit();
        };
    }
}

// ── Bulk modal trigger ───────────────────────────────────────
function openBulkModal(type) {
    var count = document.querySelectorAll('.row-check:checked').length;
    if (count === 0) return;
    var noun = count === 1 ? 'record' : 'records';

    if (type === 'restore') {
        document.getElementById('bulkRestoreCount').textContent = count + ' ' + noun + ' selected for restore';
        document.getElementById('bulkRestoreModal').style.display = 'flex';
        document.getElementById('bulkRestoreConfirmBtn').onclick = function () {
            // inject a hidden submit button into the bulk form and click it
            var btn = document.createElement('button');
            btn.type = 'submit';
            btn.name = 'bulk_restore';
            btn.style.display = 'none';
            document.getElementById('bulkForm').appendChild(btn);
            btn.click();
        };
    } else {
        document.getElementById('bulkDeleteCount').textContent = count + ' ' + noun + ' will be permanently deleted';
        document.getElementById('bulkDeleteModal').style.display = 'flex';
        document.getElementById('bulkDeleteConfirmBtn').onclick = function () {
            var btn = document.createElement('button');
            btn.type = 'submit';
            btn.name = 'bulk_permanent_delete';
            btn.style.display = 'none';
            document.getElementById('bulkForm').appendChild(btn);
            btn.click();
        };
    }
}

// ── Close modals by clicking backdrop ───────────────────────
window.addEventListener('click', function (e) {
    if (e.target.id === 'restoreModal')     closeModal('restoreModal');
    if (e.target.id === 'deleteModal')      closeModal('deleteModal');
    if (e.target.id === 'bulkRestoreModal') closeModal('bulkRestoreModal');
    if (e.target.id === 'bulkDeleteModal')  closeModal('bulkDeleteModal');
});

// ── Bulk checkbox logic ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    var checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = checkAll.checked);
            updateToolbar();
        });
    }
    document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateToolbar));
});

function updateToolbar() {
    var checked = document.querySelectorAll('.row-check:checked');
    var toolbar  = document.getElementById('bulkToolbar');
    var counter  = document.getElementById('bulkCount');
    if (toolbar) toolbar.style.display = checked.length > 0 ? 'flex' : 'none';
    if (counter) counter.textContent   = checked.length + ' selected';
}

function clearAllChecks() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    var ca = document.getElementById('checkAll');
    if (ca) ca.checked = false;
    updateToolbar();
}
</script>

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

<?php include('dashboard_sidebar_end.php'); ?>