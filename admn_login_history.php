<?php
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
require('classes/main.class.php');
$userdetails = $bmis->get_userdata();

    // ==========================================
    // BACKEND: Handle Bulk Deletion Action
    // ==========================================
    $delete_message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
        $ids_to_delete = isset($_POST['selected_ids']) ? array_map('intval', $_POST['selected_ids']) : [];
        
        if (!empty($ids_to_delete)) {
            if (method_exists($bmis, 'delete_login_history')) {
                $success = $bmis->delete_login_history($ids_to_delete);
            } else {
                $success = false; 
            }

            if ($success) {
                $delete_message = '<div class="alert alert-success" style="background:#e3f3e9; color:#1e6e3e; padding:12px 20px; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:500;"><i class="fas fa-check-circle mr-2"></i> Successfully deleted ' . count($ids_to_delete) . ' record(s).</div>';
            }
        }
    }

    // ==========================================
    // DATA FETCHING & FILTERS
    // ==========================================
    $page     = max(1, (int)($_GET['page']     ?? 1));
    $per_page = 20;
    $filters  = [
        'search'    => trim($_GET['search']    ?? ''),
        'event'     => trim($_GET['event']     ?? ''),
        'date_from' => trim($_GET['date_from'] ?? ''),
        'date_to'   => trim($_GET['date_to']   ?? ''),
    ];

    // Re-run fetching after a successful post statement to update the table immediately
    $result      = $bmis->get_login_history($filters, $page, $per_page);
    $logs        = $result['rows'];
    $total       = $result['total'];
    $total_pages = max(1, (int)ceil($total / $per_page));

    include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIS - Login History</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue-deep:   #1a2e4d;
            --blue-mid:    #2e5fa3;
            --blue-bright: #4a90d9;
            --blue-glow:   rgba(74,144,217,.16);
            --gold:        #c9a84c;
            --ink:         #0f1825;
            --mist:        #f4f7fb;
            --border:      rgba(46,95,163,.16);
            --card-shadow: 0 20px 60px rgba(26,46,77,.12),0 2px 8px rgba(26,46,77,.07);
        }
        body { background:var(--mist); font-family:'DM Sans',sans-serif; }

        .page-heading { display:flex; align-items:center; gap:14px; margin-bottom:28px; }
        .page-heading .head-icon {
            width:46px; height:46px; border-radius:12px;
            background:linear-gradient(135deg,var(--blue-deep),var(--blue-mid));
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:18px;
            box-shadow:0 4px 14px rgba(46,95,163,.3);
        }
        .page-heading h1 { font-family:'Playfair Display',serif; font-size:22px; color:var(--blue-deep); margin:0; }
        .page-heading p  { font-size:12px; color:#7a91b0; margin:2px 0 0; }

        .tab-bar { display:flex; gap:4px; margin-bottom:20px; }
        .tab-bar a {
            padding:9px 18px; border-radius:10px; font-size:13px; font-weight:500;
            color:#7a91b0; text-decoration:none; border:1.5px solid transparent;
            transition:all .2s;
        }
        .tab-bar a:hover { background:#eef3fb; color:var(--blue-mid); }
        .tab-bar a.active { background:var(--blue-mid); color:#fff; box-shadow:0 4px 12px rgba(46,95,163,.28); }

        .filter-card {
            background:#fff; border-radius:16px; box-shadow:var(--card-shadow);
            padding:20px 24px; margin-bottom:20px;
            display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
        }
        .filter-card .fg { display:flex; flex-direction:column; gap:5px; }
        .filter-card label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:var(--blue-deep); }
        .filter-card input,
        .filter-card select {
            padding:8px 12px; border:1.5px solid var(--border); border-radius:8px;
            font-family:'DM Sans',sans-serif; font-size:13px; color:var(--ink);
            background:var(--mist); outline:none;
            transition:border-color .2s, box-shadow .2s;
        }
        .filter-card input:focus,
        .filter-card select:focus { border-color:var(--blue-bright); background:#fff; box-shadow:0 0 0 3px var(--blue-glow); }
        .fg-search { flex:1; min-width:200px; }
        .btn-filter {
            padding:9px 20px; border-radius:8px; border:none;
            background:linear-gradient(135deg,var(--blue-mid),var(--blue-bright));
            color:#fff; font-size:13px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; gap:6px;
            box-shadow:0 4px 12px rgba(46,95,163,.25);
        }
        .btn-reset {
            padding:9px 16px; border-radius:8px; border:1.5px solid var(--border);
            background:transparent; color:#7a91b0; font-size:13px; cursor:pointer;
            text-decoration:none; display:inline-flex; align-items:center;
        }

        /* Stat pills */
        .stat-row { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; }
        .stat-pill {
            background:#fff; border-radius:12px; padding:14px 20px;
            display:flex; align-items:center; gap:12px;
            box-shadow:0 4px 16px rgba(26,46,77,.08);
            flex:1; min-width:140px;
        }
        .stat-pill .s-icon {
            width:38px; height:38px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            font-size:16px;
        }
        .stat-pill .s-val { font-size:22px; font-weight:600; color:var(--ink); line-height:1; }
        .stat-pill .s-lbl { font-size:11px; color:#7a91b0; margin-top:2px; }

        .main-card { background:#fff; border-radius:20px; box-shadow:var(--card-shadow); overflow:hidden; }
        .main-card::before {
            content:''; display:block; height:5px;
            background:linear-gradient(90deg,var(--blue-deep),var(--blue-bright),var(--gold));
        }
        .card-header-strip {
            background:linear-gradient(135deg,var(--blue-deep),var(--blue-mid));
            padding:18px 28px; display:flex; align-items:center; justify-content:space-between;
        }
        .card-header-strip h2 { font-family:'Playfair Display',serif; font-size:18px; color:#fff; margin:0; }
        .card-header-strip span { font-size:12px; color:rgba(255,255,255,.6); }

        .log-table { width:100%; border-collapse:collapse; }
        .log-table thead th {
            padding:11px 16px; font-size:11px; font-weight:600; letter-spacing:.5px;
            text-transform:uppercase; color:#7a91b0;
            background:#f8fafd; border-bottom:1px solid var(--border); white-space:nowrap;
        }
        .log-table tbody tr { border-bottom:1px solid #f0f4fb; transition:background .15s; }
        .log-table tbody tr:hover { background:#f5f8fe; }
        .log-table td { padding:11px 16px; font-size:13px; color:var(--ink); vertical-align:middle; }
        .log-table td.muted { color:#7a91b0; }

        .ev-badge {
            display:inline-flex; align-items:center; gap:5px;
            font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px;
        }
        .ev-login  { background:#e3f3e9; color:#1e6e3e; }
        .ev-logout { background:#eef1f6; color:#5a6a80; }
        .ev-failed { background:#fde8e8; color:#a32d2d; }

        .empty-state { text-align:center; padding:60px 20px; color:#a0b4cc; }
        .empty-state i { font-size:40px; margin-bottom:12px; display:block; }
        .empty-state p { font-size:14px; margin:0; }

        .pagination-row {
            padding:16px 24px; display:flex; align-items:center;
            justify-content:space-between; border-top:1px solid var(--border);
            flex-wrap:wrap; gap:10px;
        }
        .pagination-row span { font-size:12px; color:#7a91b0; }
        .pagination-links { display:flex; gap:4px; }
        .pagination-links a, .pagination-links span {
            padding:5px 11px; border-radius:7px; font-size:12px; font-weight:500;
            border:1.5px solid var(--border); text-decoration:none; color:var(--blue-mid);
            transition:background .15s;
        }
        .pagination-links a:hover { background:var(--blue-glow); }
        .pagination-links .current { background:var(--blue-mid); color:#fff; border-color:var(--blue-mid); }
        .pagination-links .disabled { color:#c0cdd8; pointer-events:none; }

        .btn-bulk-delete {
            background: linear-gradient(135deg, #d9534f, #c9302c);
            box-shadow: 0 4px 12px rgba(217, 83, 79, 0.25);
            display: none;
        }

        @media(max-width:768px) {
            .filter-card { flex-direction:column; }
            .fg-search { min-width:100%; }
        }
    </style>
</head>
<body id="page-top">
<div class="container-fluid mt-4">

    <?php echo $delete_message; ?>

    <div class="page-heading">
        <div class="head-icon"><i class="fas fa-history"></i></div>
        <div>
            <h1>Audit &amp; Logs</h1>
            <p>Track all administrator actions and login events</p>
        </div>
    </div>

    <div class="tab-bar">
        <a href="admn_activity_logs.php"><i class="fas fa-tasks mr-1"></i> Activity Logs</a>
        <a href="admn_login_history.php" class="active"><i class="fas fa-sign-in-alt mr-1"></i> Login History</a>
    </div>

    <?php
    $all = $bmis->get_login_history([], 1, 999999);
    $count_login  = count(array_filter($all['rows'], fn($r) => $r['event']==='login'));
    $count_logout = count(array_filter($all['rows'], fn($r) => $r['event']==='logout'));
    $count_failed = count(array_filter($all['rows'], fn($r) => $r['event']==='failed'));
    ?>
    <div class="stat-row">
        <div class="stat-pill">
            <div class="s-icon" style="background:#e3f3e9;color:#1e6e3e;"><i class="fas fa-sign-in-alt"></i></div>
            <div>
                <div class="s-val"><?php echo number_format($count_login); ?></div>
                <div class="s-lbl">Successful Logins</div>
            </div>
        </div>
        <div class="stat-pill">
            <div class="s-icon" style="background:#eef1f6;color:#5a6a80;"><i class="fas fa-sign-out-alt"></i></div>
            <div>
                <div class="s-val"><?php echo number_format($count_logout); ?></div>
                <div class="s-lbl">Logouts</div>
            </div>
        </div>
        <div class="stat-pill">
            <div class="s-icon" style="background:#fde8e8;color:#a32d2d;"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <div class="s-val"><?php echo number_format($count_failed); ?></div>
                <div class="s-lbl">Failed Attempts</div>
            </div>
        </div>
        <div class="stat-pill">
            <div class="s-icon" style="background:#dbe9ff;color:#185FA5;"><i class="fas fa-database"></i></div>
            <div>
                <div class="s-val"><?php echo number_format($all['total']); ?></div>
                <div class="s-lbl">Total Events</div>
            </div>
        </div>
    </div>

    <form method="GET" action="">
        <div class="filter-card">
            <div class="fg fg-search">
                <label>Search</label>
                <input type="text" name="search" placeholder="Name or email…" value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="fg">
                <label>Event</label>
                <select name="event">
                    <option value="">All Events</option>
                    <option value="login"  <?php echo $filters['event']==='login' ?'selected':''; ?>>Login</option>
                    <option value="logout" <?php echo $filters['event']==='logout'?'selected':''; ?>>Logout</option>
                    <option value="failed" <?php echo $filters['event']==='failed'?'selected':''; ?>>Failed</option>
                </select>
            </div>
            <div class="fg">
                <label>Date From</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
            </div>
            <div class="fg">
                <label>Date To</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
            </div>
            <div class="fg">
                <label>&nbsp;</label>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
                    <a href="admn_login_history.php" class="btn-reset">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <form method="POST" action="" id="bulkDeleteForm" onsubmit="return confirm('Are you sure you want to permanently delete the selected login history entries?');">
        <div class="main-card">
            <div class="card-header-strip">
                <h2><i class="fas fa-sign-in-alt mr-2"></i>Login History</h2>
                <div style="display:flex; align-items:center; gap:16px;">
                    <button type="submit" name="bulk_delete" id="btnBulkDelete" class="btn-filter btn-bulk-delete">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <span><?php echo number_format($total); ?> record<?php echo $total!=1?'s':''; ?></span>
                </div>
            </div>

            <?php if(empty($logs)): ?>
            <div class="empty-state">
                <i class="fas fa-user-clock"></i>
                <p>No login history found<?php echo array_filter($filters)?' matching your filters.':' yet.'; ?></p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table class="log-table">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">
                            <input type="checkbox" id="selectAll" style="transform: scale(1.2); cursor: pointer;">
                        </th>
                        <th>#</th>
                        <th>Admin / User</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>IP Address</th>
                        <th>Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $row_num = ($page - 1) * $per_page + 1;
                foreach($logs as $log):
                    $ev = $log['event'];
                    $ev_class = match($ev) { 'login'=>'ev-login','logout'=>'ev-logout',default=>'ev-failed' };
                    $ev_icon  = match($ev) { 'login'=>'fa-sign-in-alt','logout'=>'fa-sign-out-alt',default=>'fa-times-circle' };
                    
                    // FIXED: Maps to your actual primary database key column name
                    $log_id = $log['id_history'] ?? $log['id'] ?? $log['log_id'] ?? 0;
                ?>
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" name="selected_ids[]" value="<?php echo $log_id; ?>" class="log-checkbox" style="transform: scale(1.1); cursor: pointer;">
                    </td>
                    <td class="muted"><?php echo $row_num++; ?></td>
                    <td>
                        <div style="font-weight:600;"><?php echo htmlspecialchars($log['admin_name']); ?></div>
                    </td>
                    <td class="muted" style="font-size:12px;"><?php echo htmlspecialchars($log['role'] ?: '—'); ?></td>
                    <td class="muted" style="font-size:12px;"><?php echo htmlspecialchars($log['email'] ?? '—'); ?></td>
                    <td>
                        <span class="ev-badge <?php echo $ev_class; ?>">
                            <i class="fas <?php echo $ev_icon; ?>"></i>
                            <?php echo ucfirst($ev); ?>
                        </span>
                    </td>
                    <td class="muted" style="font-size:12px;font-family:monospace;"><?php echo htmlspecialchars($log['ip_address'] ?? '—'); ?></td>
                    <td class="muted" style="white-space:nowrap;">
                        <?php
                            $dt = new DateTime($log['created_at']);
                            echo $dt->format('M j, Y') . '<br><span style="font-size:11px;">' . $dt->format('h:i A') . '</span>';
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <div class="pagination-row">
                <span>Showing <?php echo min(($page-1)*$per_page+1,$total); ?>–<?php echo min($page*$per_page,$total); ?> of <?php echo number_format($total); ?></span>
                <div class="pagination-links">
                    <?php
                    $base_url = '?' . http_build_query(array_merge($filters, ['page'=>'']));
                    if($page > 1): ?>
                        <a href="<?php echo $base_url.($page-1); ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-left"></i></span>
                    <?php endif;

                    $start = max(1,$page-2); $end = min($total_pages,$page+2);
                    for($i=$start;$i<=$end;$i++):
                        if($i===$page): ?><span class="current"><?php echo $i; ?></span>
                        <?php else: ?><a href="<?php echo $base_url.$i; ?>"><?php echo $i; ?></a>
                        <?php endif;
                    endfor;

                    if($page < $total_pages): ?>
                        <a href="<?php echo $base_url.($page+1); ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllBox = document.getElementById('selectAll');
    const rowBoxes = document.querySelectorAll('.log-checkbox');
    const deleteBtn = document.getElementById('btnBulkDelete');

    function refreshDeleteButtonState() {
        const checkCount = document.querySelectorAll('.log-checkbox:checked').length;
        if (checkCount > 0) {
            deleteBtn.style.display = 'inline-flex';
            deleteBtn.innerHTML = `<i class="fas fa-trash"></i> Delete Selected (${checkCount})`;
        } else {
            deleteBtn.style.display = 'none';
        }
    }

    if (selectAllBox) {
        selectAllBox.addEventListener('change', function () {
            rowBoxes.forEach(box => {
                box.checked = selectAllBox.checked;
            });
            refreshDeleteButtonState();
        });
    }

    rowBoxes.forEach(box => {
        box.addEventListener('change', function () {
            if (!this.checked) {
                selectAllBox.checked = false;
            } else {
                const totalRowsChecked = Array.from(rowBoxes).every(cb => cb.checked);
                selectAllBox.checked = totalRowsChecked;
            }
            refreshDeleteButtonState();
        });
    });
});
</script>
</body>
</html>