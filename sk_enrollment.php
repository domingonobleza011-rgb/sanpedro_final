<?php
error_reporting(E_ALL ^ E_WARNING);
require_once('classes/security.php');
bmis_session_start();
bmis_set_security_headers();
require_once('classes/conn.php');
include('classes/staff.class.php');
include('classes/resident.class.php');

// Enforce: only SK Chairperson may access this page
$userdetails = bmis_require_login();
if ($userdetails['role'] !== 'user' || ($userdetails['position'] ?? '') !== 'Sk Chairperson') {
    http_response_code(403);
    die('Access denied. This page is restricted to the SK Chairperson only.');
}

require_once('classes/main.class.php');
$bmis = new BMISClass();
?>
<?php include('dashboard_sidebar_start_sk.php'); ?>
<style>
:root { --sk:#1a4480;--gold:#c9943a;--sk-pale:#e8f5ed;--gold-pale:#fdf3e3; }
.page-header { background:linear-gradient(135deg,#1a4480,#2b5ea7);color:#fff;border-radius:16px;padding:26px 30px;margin-bottom:24px;display:flex;align-items:center;gap:16px;box-shadow:0 6px 24px rgba(26,107,58,.2); }
.page-header .hdr-icon { width:60px;height:60px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.7rem;flex-shrink:0; }
.page-header h2 { margin:0;font-size:1.5rem;font-weight:700; }
.page-header p  { margin:4px 0 0;opacity:.82;font-size:.88rem; }
.panel { background:#fff;border-radius:14px;padding:22px 24px;box-shadow:0 2px 8px rgba(26,107,58,.07);border:1.5px solid #e8ecf0; }
.stat-card { background:#fff;border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);border:1.5px solid #e8ecf0; }
.stat-icon { width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0; }
.stat-val  { font-size:1.7rem;font-weight:800;line-height:1; }
.stat-lbl  { font-size:.73rem;text-transform:uppercase;letter-spacing:.06em;font-weight:600;opacity:.6;margin-top:2px; }
.s-green .stat-icon{background:#e8f5ed;color:#1a6b3a;} .s-green .stat-val{color:#1a6b3a;}
.s-blue  .stat-icon{background:#e8f0fe;color:#1967d2;} .s-blue  .stat-val{color:#1967d2;}
.s-gold  .stat-icon{background:#fdf3e3;color:#c9943a;} .s-gold  .stat-val{color:#c9943a;}
.s-red   .stat-icon{background:#fde8e8;color:#c0392b;} .s-red   .stat-val{color:#c0392b;}
.badge-enrolled { background:#e8f0fe;color:#1967d2;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.badge-attended { background:#e8f5ed;color:#1a6b3a;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.badge-dropped  { background:#fde8e8;color:#c0392b;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.btn-sk { background:#1a6b3a;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:.875rem;font-weight:600;transition:all .2s; }
.btn-sk:hover { background:#145230;color:#fff; }
.filter-bar { background:#fff;border-radius:12px;padding:16px 20px;margin-bottom:16px;box-shadow:0 2px 8px rgba(0,0,0,.05);display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end; }
.table thead th { background:#f4f7fb;color:#1a6b3a;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;border-bottom:2px solid #e8ecf0; }
.table td { font-size:.875rem;vertical-align:middle;border-bottom:1px solid #f0f4f8; }
.action-btn { border:none;border-radius:7px;padding:5px 10px;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .15s; }
.edit-btn { background:#e8f0fe;color:#1967d2; }
.edit-btn:hover { background:#1967d2;color:#fff; }
.del-btn { background:#fde8e8;color:#c0392b; }
.del-btn:hover { background:#c0392b;color:#fff; }
.modal-header { background:#1a4480;color:#fff;border-radius:12px 12px 0 0; }
.modal-header .btn-close { filter:invert(1); }
.alert-success-custom { background:#e8f5ed;color:#1a6b3a;border:1.5px solid #1a6b3a;border-radius:10px;padding:10px 18px;font-size:.875rem;font-weight:600;margin-bottom:16px; }
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="hdr-icon"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <h2>Youth Participation Tracking</h2>
            <p>Monitor youth enrollment and attendance in SK programs and activities</p>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert-success-custom"><i class="fas fa-check-circle me-2"></i>
        <?= $_GET['success']==='added'?'Enrollment added!':($_GET['success']==='updated'?'Status updated!':'Enrollment removed.') ?>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="stat-card s-green"><div class="stat-icon"><i class="fas fa-users"></i></div><div><div class="stat-val"><?= $total_e ?></div><div class="stat-lbl">Total</div></div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card s-blue"><div class="stat-icon"><i class="fas fa-user-check"></i></div><div><div class="stat-val"><?= $enrolled ?></div><div class="stat-lbl">Enrolled</div></div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card s-gold"><div class="stat-icon"><i class="fas fa-star"></i></div><div><div class="stat-val"><?= $attended ?></div><div class="stat-lbl">Attended</div></div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card s-red"><div class="stat-icon"><i class="fas fa-user-times"></i></div><div><div class="stat-val"><?= $dropped ?></div><div class="stat-lbl">Dropped</div></div></div></div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-end w-100">
            <div>
                <label class="form-label fw-semibold mb-1" style="font-size:.82rem;">Filter by Program</label>
                <select name="program" class="form-select" style="min-width:240px;" onchange="this.form.submit()">
                    <option value="">— All Programs —</option>
                    <?php foreach ($programs as $pr): ?>
                    <option value="<?= $pr['id_program'] ?>" <?= $prog_filter==$pr['id_program']?'selected':'' ?>><?= htmlspecialchars($pr['program_title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-sk btn"><i class="fas fa-filter me-1"></i> Filter</button>
            <?php if ($prog_filter): ?><a href="sk_enrollment.php" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
            <div class="ms-auto">
                <button type="button" class="btn-sk btn" data-bs-toggle="modal" data-bs-target="#addEnrollModal"><i class="fas fa-plus me-1"></i> Add Participant</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>#</th><th>Youth Name</th><th>Contact</th><th>Program</th><th>Type</th><th>Event Date</th><th>Status</th><th>Enrolled At</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($enrollments)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">No enrollment records found.</td></tr>
            <?php else: foreach ($enrollments as $e): 
                $sb=['Enrolled'=>'badge-enrolled','Attended'=>'badge-attended','Dropped'=>'badge-dropped'];
                $sb2=$sb[$e['status']]??'badge-enrolled';
            ?>
            <tr>
                <td><?= $e['id_enrollment'] ?></td>
                <td><strong><?= htmlspecialchars($e['youth_name']) ?></strong></td>
                <td><?= htmlspecialchars($e['contact']) ?></td>
                <td><?= htmlspecialchars($e['program_title']) ?></td>
                <td><?= htmlspecialchars($e['program_type']) ?></td>
                <td><?= $e['event_date'] ? date('M d, Y', strtotime($e['event_date'])) : '—' ?></td>
                <td><span class="<?= $sb2 ?>"><?= htmlspecialchars($e['status']) ?></span></td>
                <td><?= date('M d, Y', strtotime($e['enrolled_at'])) ?></td>
                <td>
                    <button class="action-btn edit-btn me-1"
                        data-bs-toggle="modal" data-bs-target="#editEnrollModal"
                        data-id="<?= $e['id_enrollment'] ?>"
                        data-prog="<?= $e['id_program'] ?>"
                        data-status="<?= htmlspecialchars($e['status'],ENT_QUOTES) ?>"
                        data-name="<?= htmlspecialchars($e['youth_name'],ENT_QUOTES) ?>"
                    ><i class="fas fa-edit"></i></button>
                    <button class="action-btn del-btn"
                        data-bs-toggle="modal" data-bs-target="#delEnrollModal"
                        data-id="<?= $e['id_enrollment'] ?>"
                        data-prog="<?= $e['id_program'] ?>"
                        data-name="<?= htmlspecialchars($e['youth_name'],ENT_QUOTES) ?>"
                    ><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Add Enrollment Modal -->
<div class="modal fade" id="addEnrollModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Participant</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST">
      <div class="modal-body">
        <div class="mb-3"><label class="form-label fw-semibold">Program</label>
          <select name="id_program" class="form-select" required>
            <?php foreach($programs as $pr): ?><option value="<?= $pr['id_program'] ?>" <?= $prog_filter==$pr['id_program']?'selected':'' ?>><?= htmlspecialchars($pr['program_title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3"><label class="form-label fw-semibold">Youth Member</label>
          <select name="id_youth" class="form-select" required>
            <option value="">— Select Youth —</option>
            <?php foreach($youth_list as $yl): ?><option value="<?= $yl['id_youth'] ?>"><?= htmlspecialchars($yl['lname'].', '.$yl['fname']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3"><label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select" required><option>Enrolled</option><option>Attended</option><option>Dropped</option></select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add_enrollment" class="btn-sk btn"><i class="fas fa-save me-1"></i> Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Enrollment Modal -->
<div class="modal fade" id="editEnrollModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-edit me-2"></i>Update Participation Status</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST">
      <input type="hidden" name="id_enrollment" id="ee_id">
      <input type="hidden" name="id_program"   id="ee_prog">
      <div class="modal-body">
        <p>Updating status for: <strong id="ee_name"></strong></p>
        <div class="mb-3"><label class="form-label fw-semibold">Status</label>
          <select name="status" id="ee_status" class="form-select" required><option>Enrolled</option><option>Attended</option><option>Dropped</option></select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="update_enrollment" class="btn-sk btn"><i class="fas fa-save me-1"></i> Update</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delEnrollModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#c0392b;"><h5 class="modal-title text-white">Remove Participant</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Remove <strong id="del_enroll_name"></strong> from this program?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST"><input type="hidden" name="id_enrollment" id="del_enroll_id"><input type="hidden" name="id_program" id="del_enroll_prog">
          <button type="submit" name="delete_enrollment" class="btn btn-danger">Remove</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('editEnrollModal').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('ee_id').value     = b.dataset.id;
    document.getElementById('ee_prog').value   = b.dataset.prog;
    document.getElementById('ee_status').value = b.dataset.status;
    document.getElementById('ee_name').textContent = b.dataset.name;
});
document.getElementById('delEnrollModal').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('del_enroll_id').value            = b.dataset.id;
    document.getElementById('del_enroll_prog').value          = b.dataset.prog;
    document.getElementById('del_enroll_name').textContent    = b.dataset.name;
});
</script>

<?php include('dashboard_sidebar_end.php'); ?>
