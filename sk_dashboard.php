<?php
error_reporting(E_ALL ^ E_WARNING);
require_once('classes/security.php');
bmis_session_start();
bmis_set_security_headers();
require_once('classes/conn.php');
include('classes/staff.class.php');
include('classes/resident.class.php');

// Enforce: only logged-in staff with position 'Sk Chairperson' may access this page
$userdetails = bmis_require_login();
if ($userdetails['role'] !== 'user' || ($userdetails['position'] ?? '') !== 'Sk Chairperson') {
    http_response_code(403);
    die('Access denied. This page is restricted to the SK Chairperson only.');
}

require_once('classes/main.class.php');
$bmis = new BMISClass();

// Count stats from existing tables
try {
    $total_youth    = (int)$conn->query("SELECT COUNT(*) FROM tbl_youth")->fetchColumn();
    $total_programs = (int)$conn->query("SELECT COUNT(*) FROM tbl_youth_programs")->fetchColumn();
    $upcoming_prog  = (int)$conn->query("SELECT COUNT(*) FROM tbl_youth_programs WHERE status='Upcoming'")->fetchColumn();
    $total_enrolled = (int)$conn->query("SELECT COUNT(*) FROM tbl_youth_enrollment")->fetchColumn();
    $total_posts    = (int)$conn->query("SELECT COUNT(*) FROM tbl_youth_bulletin")->fetchColumn();
    $pinned_posts   = (int)$conn->query("SELECT COUNT(*) FROM tbl_youth_bulletin WHERE is_pinned=1")->fetchColumn();
    $recent_youth   = $conn->query("SELECT * FROM tbl_youth ORDER BY id_youth DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $recent_prog    = $conn->query("SELECT * FROM tbl_youth_programs ORDER BY date_created DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $recent_posts   = $conn->query("SELECT * FROM tbl_youth_bulletin ORDER BY date_posted DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $total_youth=$total_programs=$upcoming_prog=$total_enrolled=$total_posts=$pinned_posts=0;
    $recent_youth=$recent_prog=$recent_posts=[];
}
?>
<?php include('dashboard_sidebar_start_sk.php'); ?>

<style>
:root {
    --sk: #1a4480; --sk-mid: #2b5ea7; --sk-pale: #e8f5ed;
    --gold: #c9943a; --gold-pale: #fdf3e3;
    --shadow-sm: 0 2px 8px rgba(26,107,58,0.07);
    --shadow-md: 0 6px 24px rgba(26,107,58,0.11);
}
.sk-page-header {
    background: linear-gradient(135deg, var(--sk) 0%, #008cff 100%);
    color: #fff; border-radius: 16px; padding: 28px 32px;
    margin-bottom: 28px; display: flex; align-items: center; gap: 18px;
    box-shadow: 0 8px 32px rgba(26,107,58,0.22);
}
.sk-page-header .hdr-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: rgba(255,255,255,0.15); display: flex;
    align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;
}
.sk-page-header h2 { margin: 0; font-size: 1.7rem; font-weight: 700; }
.sk-page-header p  { margin: 4px 0 0; opacity: 0.82; font-size: 0.9rem; }

.stat-card {
    background: #fff; border-radius: 14px; padding: 22px 24px;
    display: flex; align-items: center; gap: 16px;
    box-shadow: var(--shadow-sm); border: 1.5px solid #e8ecf0;
    transition: transform .15s, box-shadow .15s; text-decoration: none; color: inherit;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); color: inherit; }
.stat-icon {
    width: 56px; height: 56px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; flex-shrink: 0;
}
.stat-val { font-size: 2rem; font-weight: 800; line-height: 1; }
.stat-lbl { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; opacity: 0.6; margin-top: 3px; }

.s-green  .stat-icon { background: var(--sk-pale); color: var(--sk); }
.s-green  .stat-val  { color: var(--sk); }
.s-gold   .stat-icon { background: var(--gold-pale); color: var(--gold); }
.s-gold   .stat-val  { color: var(--gold); }
.s-blue   .stat-icon { background: #e8f0fe; color: #1967d2; }
.s-blue   .stat-val  { color: #1967d2; }
.s-teal   .stat-icon { background: #e0f7f5; color: #00796b; }
.s-teal   .stat-val  { color: #00796b; }
.s-purple .stat-icon { background: #f0eafe; color: #6200ea; }
.s-purple .stat-val  { color: #6200ea; }

.panel {
    background: #fff; border-radius: 14px; padding: 22px 24px;
    box-shadow: var(--shadow-sm); border: 1.5px solid #e8ecf0;
}
.panel-title {
    font-size: 0.95rem; font-weight: 700; color: var(--sk);
    display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
}
.panel-title i { color: var(--gold); font-size: 1rem; }

.table-sm td, .table-sm th { padding: 9px 10px; font-size: 0.85rem; vertical-align: middle; }
.badge-upcoming  { background: #e8f0fe; color: #1967d2; border-radius: 6px; padding: 3px 8px; font-size: 0.73rem; font-weight: 700; }
.badge-ongoing   { background: var(--sk-pale); color: var(--sk); border-radius: 6px; padding: 3px 8px; font-size: 0.73rem; font-weight: 700; }
.badge-completed { background: #f0f4f8; color: #555; border-radius: 6px; padding: 3px 8px; font-size: 0.73rem; font-weight: 700; }
.badge-cancelled { background: #fde8e8; color: #c0392b; border-radius: 6px; padding: 3px 8px; font-size: 0.73rem; font-weight: 700; }
.badge-pinned    { background: var(--gold-pale); color: var(--gold); border-radius: 6px; padding: 2px 7px; font-size: 0.7rem; font-weight: 700; }

.post-card { border-left: 4px solid var(--sk-pale); padding: 12px 14px; border-radius: 8px; background: #f9fafb; margin-bottom: 10px; }
.post-card.pinned { border-left-color: var(--gold); background: var(--gold-pale); }
.post-card-title { font-size: 0.92rem; font-weight: 700; color: var(--sk); }
.post-card-meta  { font-size: 0.75rem; color: #888; margin-top: 3px; }

.quick-links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }
.quick-link {
    display: flex; align-items: center; gap: 8px;
    background: var(--sk-pale); color: var(--sk);
    padding: 10px 18px; border-radius: 10px; font-size: 0.85rem; font-weight: 600;
    text-decoration: none; transition: all .2s;
}
.quick-link:hover { background: var(--sk); color: #fff; }
.quick-link i { font-size: 1rem; }
</style>

<div class="container-fluid">

    <!-- Page Header -->
    <div class="sk-page-header">
        <div class="hdr-icon"><i class="fas fa-leaf"></i></div>
        <div>
            <h2>Sangguniang Kabataan Portal</h2>
            <p>Youth Engagement Module — Barangay San Pedro</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <a href="sk_youth_records.php" class="stat-card s-green">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div>
                    <div class="stat-val"><?= $total_youth ?></div>
                    <div class="stat-lbl">Youth Members</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="sk_programs.php" class="stat-card s-blue">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="stat-val"><?= $total_programs ?></div>
                    <div class="stat-lbl">Total Programs</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="sk_programs.php?status=Upcoming" class="stat-card s-gold">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-val"><?= $upcoming_prog ?></div>
                    <div class="stat-lbl">Upcoming Programs</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="sk_enrollment.php" class="stat-card s-teal">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <div class="stat-val"><?= $total_enrolled ?></div>
                    <div class="stat-lbl">Enrollments</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <a href="sk_announcements.php" class="stat-card s-purple">
                <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                <div>
                    <div class="stat-val"><?= $total_posts ?></div>
                    <div class="stat-lbl">Announcements</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card s-green" style="cursor:default;">
                <div class="stat-icon"><i class="fas fa-thumbtack"></i></div>
                <div>
                    <div class="stat-val"><?= $pinned_posts ?></div>
                    <div class="stat-lbl">Pinned Posts</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="panel mb-4">
        <div class="panel-title"><i class="fas fa-bolt"></i> Quick Actions</div>
        <div class="quick-links">
            <a href="sk_youth_records.php?action=add" class="quick-link"><i class="fas fa-user-plus"></i> Add Youth Member</a>
            <a href="sk_programs.php?action=add" class="quick-link"><i class="fas fa-calendar-plus"></i> New Program</a>
            <a href="sk_announcements.php?action=add" class="quick-link"><i class="fas fa-plus-circle"></i> Post Announcement</a>
            <a href="sk_enrollment.php" class="quick-link"><i class="fas fa-list-alt"></i> View Participations</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Youth -->
        <div class="col-lg-6">
            <div class="panel h-100">
                <div class="panel-title"><i class="fas fa-users"></i> Recent Youth Members</div>
                <?php if (empty($recent_youth)): ?>
                    <p class="text-muted text-center py-3">No youth records yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead><tr><th>#</th><th>Name</th><th>Age</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent_youth as $y): ?>
                    <tr>
                        <td><?= $y['id_youth'] ?></td>
                        <td><?= htmlspecialchars($y['lname'].', '.$y['fname'].' '.$y['mi']) ?></td>
                        <td><?= htmlspecialchars($y['age']) ?></td>
                        <td><span class="badge-ongoing"><?= htmlspecialchars($y['emp_status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <a href="sk_youth_records.php" class="btn btn-sm btn-outline-success mt-2">View All</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Programs -->
        <div class="col-lg-6">
            <div class="panel h-100">
                <div class="panel-title"><i class="fas fa-calendar-check"></i> Recent Programs</div>
                <?php if (empty($recent_prog)): ?>
                    <p class="text-muted text-center py-3">No programs yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead><tr><th>Title</th><th>Type</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent_prog as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['program_title']) ?></td>
                        <td><?= htmlspecialchars($p['program_type']) ?></td>
                        <td>
                            <?php
                            $sc = ['Upcoming'=>'badge-upcoming','Ongoing'=>'badge-ongoing','Completed'=>'badge-completed','Cancelled'=>'badge-cancelled'];
                            $cls = $sc[$p['status']] ?? 'badge-completed';
                            ?>
                            <span class="<?= $cls ?>"><?= htmlspecialchars($p['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <a href="sk_programs.php" class="btn btn-sm btn-outline-success mt-2">View All</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="col-12">
            <div class="panel">
                <div class="panel-title"><i class="fas fa-bullhorn"></i> Recent Announcements</div>
                <?php if (empty($recent_posts)): ?>
                    <p class="text-muted text-center py-3">No announcements yet.</p>
                <?php else: ?>
                <div class="row g-3">
                <?php foreach ($recent_posts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card <?= $post['is_pinned'] ? 'pinned' : '' ?>">
                            <div class="post-card-title">
                                <?php if ($post['is_pinned']): ?><span class="badge-pinned me-1"><i class="fas fa-thumbtack"></i> Pinned</span><?php endif; ?>
                                <?= htmlspecialchars($post['post_title']) ?>
                            </div>
                            <div class="post-card-meta">
                                <?= htmlspecialchars($post['post_type']) ?> &middot; 
                                By <?= htmlspecialchars($post['posted_by']) ?> &middot; 
                                <?= date('M d, Y', strtotime($post['date_posted'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <a href="sk_announcements.php" class="btn btn-sm btn-outline-success mt-3">View All Announcements</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('dashboard_sidebar_end.php'); ?>



