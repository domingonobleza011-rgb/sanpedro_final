<?php
error_reporting(E_ALL ^ E_WARNING);
define('BMIS_ROLE_REQUIRED', 'admin');
include('secure_header.php');
include('classes/staff.class.php');
    include('classes/resident.class.php');
    require_once('classes/conn.php');
$userdetails = $bmis->get_userdata();
$bmis->validate_admin();

// ADD
if (isset($_POST['add_program'])) {
    $stmt = $conn->prepare("INSERT INTO tbl_youth_programs (program_title,program_type,description,venue,event_date,event_time,slots,requirements,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$_POST['program_title'],$_POST['program_type'],$_POST['description'],$_POST['venue'],$_POST['event_date'],$_POST['event_time'],$_POST['slots'],$_POST['requirements'],$_POST['status'],$userdetails['firstname'].' '.$userdetails['surname']]);
    header("Location: sk_programs.php?success=added"); exit;
}
// EDIT
if (isset($_POST['edit_program'])) {
    $stmt = $conn->prepare("UPDATE tbl_youth_programs SET program_title=?,program_type=?,description=?,venue=?,event_date=?,event_time=?,slots=?,requirements=?,status=? WHERE id_program=?");
    $stmt->execute([$_POST['program_title'],$_POST['program_type'],$_POST['description'],$_POST['venue'],$_POST['event_date'],$_POST['event_time'],$_POST['slots'],$_POST['requirements'],$_POST['status'],$_POST['id_program']]);
    header("Location: sk_programs.php?success=updated"); exit;
}
// DELETE
if (isset($_POST['delete_program'])) {
    $conn->prepare("DELETE FROM tbl_youth_programs WHERE id_program=?")->execute([$_POST['id_program']]);
    header("Location: sk_programs.php?success=deleted"); exit;
}

$status_filter = $_GET['status'] ?? '';
if ($status_filter) {
    $s = $conn->prepare("SELECT * FROM tbl_youth_programs WHERE status=? ORDER BY event_date DESC");
    $s->execute([$status_filter]);
} else {
    $s = $conn->prepare("SELECT * FROM tbl_youth_programs ORDER BY event_date DESC");
    $s->execute();
}
$programs = $s->fetchAll(PDO::FETCH_ASSOC);

$types = ['Training','Sports','Arts','Leadership','Health','Livelihood','Scholarship','Community Service','Other'];
$statuses = ['Upcoming','Ongoing','Completed','Cancelled'];
?>
<?php include('dashboard_sidebar_start_sk.php'); ?>
<style>
:root { --sk:#1a4480;--gold:#c9943a;--sk-pale:#e8f5ed;--gold-pale:#fdf3e3; }
.page-header { background:linear-gradient(135deg,#1a4480,#2b5ea7);color:#fff;border-radius:16px;padding:26px 30px;margin-bottom:24px;display:flex;align-items:center;gap:16px;box-shadow:0 6px 24px rgba(26,107,58,.2); }
.page-header .hdr-icon { width:60px;height:60px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.7rem;flex-shrink:0; }
.page-header h2 { margin:0;font-size:1.5rem;font-weight:700; }
.page-header p  { margin:4px 0 0;opacity:.82;font-size:.88rem; }
.panel { background:#fff;border-radius:14px;padding:22px 24px;box-shadow:0 2px 8px rgba(26,107,58,.07);border:1.5px solid #e8ecf0; }
.prog-card { background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1.5px solid #e8ecf0;transition:transform .15s,box-shadow .15s; border-left:5px solid #e8ecf0; }
.prog-card:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1); }
.prog-card.upcoming  { border-left-color:#1967d2; }
.prog-card.ongoing   { border-left-color:#1a6b3a; }
.prog-card.completed { border-left-color:#888; }
.prog-card.cancelled { border-left-color:#c0392b; }
.prog-title { font-size:1rem;font-weight:700;color:#1a6b3a; }
.prog-meta  { font-size:.8rem;color:#888;margin-top:4px;display:flex;flex-wrap:wrap;gap:12px; }
.prog-meta span { display:flex;align-items:center;gap:4px; }
.prog-desc  { font-size:.875rem;color:#555;margin:10px 0;line-height:1.55; }
.badge-type { background:#e8f5ed;color:#1a6b3a;border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:700; }
.badge-upcoming  { background:#e8f0fe;color:#1967d2;border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:700; }
.badge-ongoing   { background:#e8f5ed;color:#1a6b3a;border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:700; }
.badge-completed { background:#f0f4f8;color:#555;border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:700; }
.badge-cancelled { background:#fde8e8;color:#c0392b;border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:700; }
.btn-sk { background:#1a4480;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:.875rem;font-weight:600;transition:all .2s; }
.btn-sk:hover { background:#145230;color:#fff; }
.filter-tab { padding:7px 16px;border-radius:8px;border:1.5px solid #e8ecf0;font-size:.82rem;font-weight:600;cursor:pointer;background:#fff;color:#666;text-decoration:none;transition:all .15s; }
.filter-tab:hover,.filter-tab.active { border-color:#1a4480;background:#1a4480;color:#fff; }
.action-btn { border:none;border-radius:7px;padding:5px 12px;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .15s; }
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
        <div class="hdr-icon"><i class="fas fa-calendar-check"></i></div>
        <div>
            <h2>Programs & Activity Monitoring</h2>
            <p>Track, record, and document youth participation in community programs</p>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert-success-custom"><i class="fas fa-check-circle me-2"></i>
        <?= $_GET['success']==='added'?'Program added!':($_GET['success']==='updated'?'Program updated!':'Program deleted.') ?>
    </div>
    <?php endif; ?>

    <div class="panel mb-3">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="d-flex flex-wrap gap-2">
                <a href="sk_programs.php" class="filter-tab <?= !$status_filter?'active':'' ?>">All</a>
                <?php foreach ($statuses as $st): ?>
                <a href="sk_programs.php?status=<?= urlencode($st) ?>" class="filter-tab <?= $status_filter===$st?'active':'' ?>"><?= $st ?></a>
                <?php endforeach; ?>
            </div>
            <button class="btn-sk btn" data-bs-toggle="modal" data-bs-target="#addProgModal"><i class="fas fa-calendar-plus me-1"></i> Add Program</button>
        </div>
    </div>

    <?php if (empty($programs)): ?>
    <div class="panel text-center py-5">
        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
        <p class="text-muted">No programs found<?= $status_filter?" for status: $status_filter":'' ?>.</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
    <?php foreach ($programs as $p): 
        $lcst = strtolower($p['status']);
        $badge_map = ['Upcoming'=>'badge-upcoming','Ongoing'=>'badge-ongoing','Completed'=>'badge-completed','Cancelled'=>'badge-cancelled'];
        $badge = $badge_map[$p['status']] ?? 'badge-completed';
    ?>
    <div class="col-lg-6 col-xl-4">
        <div class="prog-card <?= $lcst ?>">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge-type"><?= htmlspecialchars($p['program_type']) ?></span>
                <span class="<?= $badge ?>"><?= htmlspecialchars($p['status']) ?></span>
            </div>
            <div class="prog-title"><?= htmlspecialchars($p['program_title']) ?></div>
            <div class="prog-meta">
                <?php if ($p['venue']): ?><span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($p['venue']) ?></span><?php endif; ?>
                <?php if ($p['event_date']): ?><span><i class="fas fa-calendar"></i><?= date('M d, Y', strtotime($p['event_date'])) ?></span><?php endif; ?>
                <?php if ($p['event_time']): ?><span><i class="fas fa-clock"></i><?= date('h:i A', strtotime($p['event_time'])) ?></span><?php endif; ?>
                <?php if ($p['slots']): ?><span><i class="fas fa-users"></i><?= $p['slots'] ?> slots</span><?php endif; ?>
            </div>
            <?php if ($p['description']): ?>
            <div class="prog-desc"><?= htmlspecialchars(substr($p['description'],0,120)).(strlen($p['description'])>120?'…':'') ?></div>
            <?php endif; ?>
            <?php if ($p['requirements']): ?>
            <div style="font-size:.78rem;color:#888;margin-bottom:10px;"><i class="fas fa-clipboard me-1"></i><em><?= htmlspecialchars($p['requirements']) ?></em></div>
            <?php endif; ?>
            <div class="d-flex gap-2 mt-2">
                <button class="action-btn edit-btn"
                    data-bs-toggle="modal" data-bs-target="#editProgModal"
                    data-id="<?= $p['id_program'] ?>"
                    data-title="<?= htmlspecialchars($p['program_title'],ENT_QUOTES) ?>"
                    data-type="<?= htmlspecialchars($p['program_type'],ENT_QUOTES) ?>"
                    data-desc="<?= htmlspecialchars($p['description'],ENT_QUOTES) ?>"
                    data-venue="<?= htmlspecialchars($p['venue'],ENT_QUOTES) ?>"
                    data-date="<?= htmlspecialchars($p['event_date'],ENT_QUOTES) ?>"
                    data-time="<?= htmlspecialchars($p['event_time'],ENT_QUOTES) ?>"
                    data-slots="<?= htmlspecialchars($p['slots'],ENT_QUOTES) ?>"
                    data-req="<?= htmlspecialchars($p['requirements'],ENT_QUOTES) ?>"
                    data-status="<?= htmlspecialchars($p['status'],ENT_QUOTES) ?>"
                ><i class="fas fa-edit"></i> Edit</button>
                <button class="action-btn del-btn" data-bs-toggle="modal" data-bs-target="#delProgModal" data-id="<?= $p['id_program'] ?>" data-title="<?= htmlspecialchars($p['program_title'],ENT_QUOTES) ?>">
                    <i class="fas fa-trash"></i>
                </button>
                <a href="sk_enrollment.php?program=<?= $p['id_program'] ?>" class="action-btn" style="background:#e8f5ed;color:#1a6b3a;text-decoration:none;">
                    <i class="fas fa-users"></i> Participants
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Add Program Modal -->
<div class="modal fade" id="addProgModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Add New Program</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label fw-semibold">Program Title</label><input name="program_title" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Type</label>
            <select name="program_type" class="form-select" required>
              <?php foreach($types as $t): ?><option><?= $t ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-12"><label class="form-label fw-semibold">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
          <div class="col-md-8"><label class="form-label fw-semibold">Venue</label><input name="venue" class="form-control" placeholder="e.g. Barangay Hall"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Slots</label><input name="slots" class="form-control" type="number" min="0" value="0"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Event Date</label><input name="event_date" class="form-control" type="date"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Event Time</label><input name="event_time" class="form-control" type="time"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select" required>
              <?php foreach($statuses as $st): ?><option><?= $st ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-12"><label class="form-label fw-semibold">Requirements</label><input name="requirements" class="form-control" placeholder="e.g. Must be 15-30 years old"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add_program" class="btn-sk btn"><i class="fas fa-save me-1"></i> Save Program</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Program Modal -->
<div class="modal fade" id="editProgModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Program</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
      <input type="hidden" name="id_program" id="ep_id">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label fw-semibold">Program Title</label><input name="program_title" id="ep_title" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Type</label>
            <select name="program_type" id="ep_type" class="form-select" required>
              <?php foreach($types as $t): ?><option><?= $t ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-12"><label class="form-label fw-semibold">Description</label><textarea name="description" id="ep_desc" class="form-control" rows="3"></textarea></div>
          <div class="col-md-8"><label class="form-label fw-semibold">Venue</label><input name="venue" id="ep_venue" class="form-control"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Slots</label><input name="slots" id="ep_slots" class="form-control" type="number" min="0"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Event Date</label><input name="event_date" id="ep_date" class="form-control" type="date"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Event Time</label><input name="event_time" id="ep_time" class="form-control" type="time"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Status</label>
            <select name="status" id="ep_status" class="form-select" required>
              <?php foreach($statuses as $st): ?><option><?= $st ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-12"><label class="form-label fw-semibold">Requirements</label><input name="requirements" id="ep_req" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="edit_program" class="btn-sk btn"><i class="fas fa-save me-1"></i> Update</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delProgModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#c0392b;"><h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Delete Program</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><p>Delete program: <strong id="del_prog_title"></strong>?</p><p class="text-danger"><small>All enrollments for this program will also be removed.</small></p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST"><input type="hidden" name="id_program" id="del_prog_id">
          <button type="submit" name="delete_program" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('editProgModal').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('ep_id').value    = b.dataset.id;
    document.getElementById('ep_title').value = b.dataset.title;
    document.getElementById('ep_type').value  = b.dataset.type;
    document.getElementById('ep_desc').value  = b.dataset.desc;
    document.getElementById('ep_venue').value = b.dataset.venue;
    document.getElementById('ep_date').value  = b.dataset.date;
    document.getElementById('ep_time').value  = b.dataset.time;
    document.getElementById('ep_slots').value = b.dataset.slots;
    document.getElementById('ep_req').value   = b.dataset.req;
    document.getElementById('ep_status').value= b.dataset.status;
});
document.getElementById('delProgModal').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('del_prog_id').value           = b.dataset.id;
    document.getElementById('del_prog_title').textContent  = b.dataset.title;
});
</script>

<?php include('dashboard_sidebar_end.php'); ?>
