<?php
    require('classes/main.class.php');
    $userdetails = $bmis->get_userdata();

    // Pagination & filters
    $page     = max(1, (int)($_GET['page']     ?? 1));
    $per_page = 20;
    $filters  = [
        'search'    => trim($_GET['search']    ?? ''),
        'module'    => trim($_GET['module']    ?? ''),
        'date_from' => trim($_GET['date_from'] ?? ''),
        'date_to'   => trim($_GET['date_to']   ?? ''),
    ];

    $result     = $bmis->get_activity_logs($filters, $page, $per_page);
    $logs       = $result['rows'];
    $total      = $result['total'];
    $total_pages = max(1, (int)ceil($total / $per_page));

    include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIS - Activity Logs</title>
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

        /* Tab bar */
        .tab-bar { display:flex; gap:4px; margin-bottom:20px; }
        .tab-bar a {
            padding:9px 18px; border-radius:10px; font-size:13px; font-weight:500;
            color:#7a91b0; text-decoration:none; border:1.5px solid transparent;
            transition:all .2s;
        }
        .tab-bar a:hover { background:#eef3fb; color:var(--blue-mid); }
        .tab-bar a.active {
            background:var(--blue-mid); color:#fff;
            box-shadow:0 4px 12px rgba(46,95,163,.28);
        }

        /* Filter card */
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
        }

        /* Main card */
        .main-card {
            background:#fff; border-radius:20px; box-shadow:var(--card-shadow); overflow:hidden;
        }
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

        /* Table */
        .log-table { width:100%; border-collapse:collapse; }
        .log-table thead th {
            padding:11px 16px; font-size:11px; font-weight:600; letter-spacing:.5px;
            text-transform:uppercase; color:#7a91b0;
            background:#f8fafd; border-bottom:1px solid var(--border);
            white-space:nowrap;
        }
        .log-table tbody tr { border-bottom:1px solid #f0f4fb; transition:background .15s; }
        .log-table tbody tr:hover { background:#f5f8fe; }
        .log-table td { padding:11px 16px; font-size:13px; color:var(--ink); vertical-align:middle; }
        .log-table td.muted { color:#7a91b0; }

        /* Module badge */
        .badge-module {
            display:inline-block; font-size:10px; font-weight:600; padding:2px 9px;
            border-radius:20px; letter-spacing:.3px;
        }
        .mod-admin    { background:#dbe9ff; color:#185FA5; }
        .mod-resident { background:#e3f3e9; color:#1e6e3e; }
        .mod-document { background:#fef3dc; color:#9a6c00; }
        .mod-blotter  { background:#fde8e8; color:#a32d2d; }
        .mod-settings { background:#ede8fb; color:#4a34a8; }
        .mod-default  { background:#eef1f6; color:#5a6a80; }

        /* Action badge */
        .badge-action {
            display:inline-block; font-size:10px; font-weight:600; padding:2px 9px;
            border-radius:20px; background:#eef3ff; color:#2e5fa3; letter-spacing:.2px;
        }

        /* Empty state */
        .empty-state { text-align:center; padding:60px 20px; color:#a0b4cc; }
        .empty-state i { font-size:40px; margin-bottom:12px; display:block; }
        .empty-state p { font-size:14px; margin:0; }

        /* Pagination */
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

        @media(max-width:768px) {
            .filter-card { flex-direction:column; }
            .fg-search { min-width:100%; }
            .log-table thead th:nth-child(5),
            .log-table td:nth-child(5) { display:none; }
        }
    </style>
</head>
<body id="page-top">
<div class="container-fluid mt-4">

    <!-- Heading -->
    <div class="page-heading">
        <div class="head-icon"><i class="fas fa-history"></i></div>
        <div>
            <h1>Audit & Logs</h1>
            <p>Track all administrator actions and login events</p>
        </div>
    </div>

    <!-- Tab bar -->
    <div class="tab-bar">
        <a href="admn_activity_logs.php" class="active"><i class="fas fa-tasks mr-1"></i> Activity Logs</a>
        <a href="admn_login_history.php"><i class="fas fa-sign-in-alt mr-1"></i> Login History</a>
    </div>

    <!-- Filters -->
    <form method="GET" action="">
        <div class="filter-card">
            <div class="fg fg-search">
                <label>Search</label>
                <input type="text" name="search" placeholder="Name, action, description…" value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="fg">
                <label>Module</label>
                <select name="module">
                    <option value="">All Modules</option>
                    <?php foreach(['Admin','Resident','Document','Blotter','Settings','Announcement','Staff'] as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo $filters['module']===$m?'selected':''; ?>><?php echo $m; ?></option>
                    <?php endforeach; ?>
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
                    <a href="admn_activity_logs.php" class="btn-reset">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Table card -->
    <div class="main-card">
        <div class="card-header-strip">
            <h2><i class="fas fa-list-alt mr-2"></i>Activity Log</h2>
            <span><?php echo number_format($total); ?> record<?php echo $total!=1?'s':''; ?></span>
        </div>

        <?php if(empty($logs)): ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>No activity logs found<?php echo array_filter($filters)?' matching your filters.'.''  :' yet.'; ?></p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="log-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Admin</th>
                    <th>Module</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $row_num = ($page - 1) * $per_page + 1;
            foreach($logs as $log):
                // Pick module badge class
                $mod_class = match(strtolower($log['module'])) {
                    'admin'        => 'mod-admin',
                    'resident'     => 'mod-resident',
                    'document'     => 'mod-document',
                    'blotter'      => 'mod-blotter',
                    'settings'     => 'mod-settings',
                    default        => 'mod-default',
                };
            ?>
            <tr>
                <td class="muted"><?php echo $row_num++; ?></td>
                <td>
                    <div style="font-weight:600;"><?php echo htmlspecialchars($log['admin_name']); ?></div>
                    <div style="font-size:11px;color:#9ab0c8;"><?php echo htmlspecialchars($log['role']); ?></div>
                </td>
                <td><span class="badge-module <?php echo $mod_class; ?>"><?php echo htmlspecialchars($log['module']); ?></span></td>
                <td><span class="badge-action"><?php echo htmlspecialchars($log['action']); ?></span></td>
                <td style="max-width:300px;"><?php echo htmlspecialchars($log['description']); ?></td>
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

        <!-- Pagination -->
        <div class="pagination-row">
            <span>Showing <?php echo min(($page-1)*$per_page+1, $total); ?>–<?php echo min($page*$per_page, $total); ?> of <?php echo number_format($total); ?></span>
            <div class="pagination-links">
                <?php
                $base_url = '?' . http_build_query(array_merge($filters, ['page' => '']));
                if($page > 1): ?>
                    <a href="<?php echo $base_url . ($page-1); ?>"><i class="fas fa-chevron-left"></i></a>
                <?php else: ?>
                    <span class="disabled"><i class="fas fa-chevron-left"></i></span>
                <?php endif;

                // Show a window of page numbers
                $start = max(1, $page-2);
                $end   = min($total_pages, $page+2);
                for($i=$start; $i<=$end; $i++):
                    if($i === $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
                    <?php endif;
                endfor;

                if($page < $total_pages): ?>
                    <a href="<?php echo $base_url . ($page+1); ?>"><i class="fas fa-chevron-right"></i></a>
                <?php else: ?>
                    <span class="disabled"><i class="fas fa-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>