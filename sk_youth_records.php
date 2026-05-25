<?php
error_reporting(E_ALL ^ E_WARNING);
define('BMIS_ROLE_REQUIRED', 'admin');
include('secure_header.php');
include('classes/staff.class.php');
    include('classes/resident.class.php');
    require_once('classes/conn.php');
$userdetails = $bmis->get_userdata();
    $bmis->validate_admin();
    $bmis->delete_youth();
    $view = $bmis->view_youth();


// Handle ADD
if (isset($_POST['add_youth'])) {
    $stmt = $conn->prepare("INSERT INTO tbl_youth (fname,lname,mi,age,sex,civil_status,contact_number,email_address,educ_attain,emp_status,skill_name) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$_POST['fname'],$_POST['lname'],$_POST['mi'],$_POST['age'],$_POST['sex'],$_POST['civil_status'],$_POST['contact_number'],$_POST['email_address'],$_POST['educ_attain'],$_POST['emp_status'],$_POST['skill_name']]);
    header("Location: sk_youth_records.php?success=added"); exit;
}
// Handle EDIT
if (isset($_POST['edit_youth'])) {
    $stmt = $conn->prepare("UPDATE tbl_youth SET fname=?,lname=?,mi=?,age=?,sex=?,civil_status=?,contact_number=?,email_address=?,educ_attain=?,emp_status=?,skill_name=? WHERE id_youth=?");
    $stmt->execute([$_POST['fname'],$_POST['lname'],$_POST['mi'],$_POST['age'],$_POST['sex'],$_POST['civil_status'],$_POST['contact_number'],$_POST['email_address'],$_POST['educ_attain'],$_POST['emp_status'],$_POST['skill_name'],$_POST['id_youth']]);
    header("Location: sk_youth_records.php?success=updated"); exit;
}
// Handle DELETE
if (isset($_POST['delete_youth'])) {
    $stmt = $conn->prepare("DELETE FROM tbl_youth WHERE id_youth=?");
    $stmt->execute([$_POST['id_youth']]);
    header("Location: sk_youth_records.php?success=deleted"); exit;
}
if (isset($_POST['bulk_delete_youth']) && !empty($_POST['selected_ids'])) {
    $ids = array_map('intval', $_POST['selected_ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("DELETE FROM tbl_youth WHERE id_youth IN ($placeholders)");
    $stmt->execute($ids);
    header("Location: sk_youth_records.php?success=deleted"); exit;
}

$keyword = $_GET['keyword'] ?? '';
if ($keyword) {
    $s = $conn->prepare("SELECT * FROM tbl_youth WHERE fname LIKE ? OR lname LIKE ? OR email_address LIKE ? ORDER BY lname");
    $k = "%$keyword%";
    $s->execute([$k,$k,$k]);
} else {
    $s = $conn->prepare("SELECT * FROM tbl_youth ORDER BY lname");
    $s->execute();
}
$youths = $s->fetchAll(PDO::FETCH_ASSOC);
$total = count($youths);
?>
<?php include('dashboard_sidebar_start_sk.php'); ?>
<style>
:root { --sk: #1a4480; --gold: #c9943a; --sk-pale: #e8f5ed; --gold-pale: #fdf3e3; }
.page-header { background: linear-gradient(135deg,#1a4480,#2b5ea7); color:#fff; border-radius:16px; padding:26px 30px; margin-bottom:24px; display:flex; align-items:center; gap:16px; box-shadow:0 6px 24px rgba(26,107,58,0.2); }
.page-header .hdr-icon { width:60px;height:60px;border-radius:14px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:1.7rem;flex-shrink:0; }
.page-header h2 { margin:0;font-size:1.5rem;font-weight:700; }
.page-header p  { margin:4px 0 0;opacity:.82;font-size:.88rem; }
.panel { background:#fff;border-radius:14px;padding:22px 24px;box-shadow:0 2px 8px rgba(26,107,58,0.07);border:1.5px solid #e8ecf0; }
.badge-emp  { background:#e8f5ed;color:#1a6b3a;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.badge-unemp { background:#fde8e8;color:#c0392b;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.badge-self { background:#fdf3e3;color:#c9943a;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.badge-stud { background:#e8f0fe;color:#1967d2;border-radius:6px;padding:3px 9px;font-size:.75rem;font-weight:700; }
.btn-sk  { background:#1a6b3a;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:.875rem;font-weight:600;transition:all .2s; }
.btn-sk:hover { background:#145230;color:#fff; }
.btn-sk-outline { border:2px solid #1a6b3a;color:#1a6b3a;background:transparent;border-radius:8px;padding:6px 14px;font-size:.82rem;font-weight:600;transition:all .2s; }
.btn-sk-outline:hover { background:#1a6b3a;color:#fff; }
.search-box { border-radius:10px;border:1.5px solid #e0e7ef;font-size:.875rem;padding:9px 14px; }
.search-box:focus { border-color:#1a6b3a;box-shadow:0 0 0 3px rgba(26,107,58,0.1); }
.table thead th { background:#f4f7fb;color:#1a6b3a;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;border-bottom:2px solid #e8ecf0; }
.table td { font-size:.875rem;vertical-align:middle;border-bottom:1px solid #f0f4f8; }
.action-btn { border:none;border-radius:7px;padding:5px 12px;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .15s; }
.edit-btn { background:#e8f0fe;color:#1967d2; }
.edit-btn:hover { background:#1967d2;color:#fff; }
.del-btn { background:#fde8e8;color:#c0392b; }
.del-btn:hover { background:#c0392b;color:#fff; }
.modal-header { background:#1a4480;color:#fff;border-radius:12px 12px 0 0; }
.modal-header .btn-close { filter:invert(1); }
.alert-success-custom { background:#e8f5ed;color:#1a6b3a;border:1.5px solid #1a6b3a;border-radius:10px;padding:10px 18px;font-size:.875rem;font-weight:600;margin-bottom:16px; }
/* ── CUSTOM CHECKBOXES ── */
.form-check-input[type="checkbox"] {
    width: 18px !important;
    height: 18px !important;
    border: 2.5px solid #b0c4d8;
    border-radius: 5px !important;
    cursor: pointer;
    transition: all .2s ease;
    background-color: #fff;
    box-shadow: none;
}
.form-check-input[type="checkbox"]:hover {
    border-color: #c0392b;
    box-shadow: 0 0 0 4px rgba(192,57,43,0.08);
    transform: scale(1.05);
}
.form-check-input[type="checkbox"]:checked {
    background-color: #c0392b !important;
    border-color: #c0392b !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' d='M4 10l4 4 8-8'/%3E%3C/svg%3E") !important;
    background-size: 12px !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    box-shadow: 0 0 0 4px rgba(192,57,43,0.15) !important;
}
#masterCheck[type="checkbox"] {
    width: 20px !important;
    height: 20px !important;
    border-color: #1a6b3a !important;
}
#masterCheck[type="checkbox"]:hover {
    border-color: #1a6b3a !important;
    box-shadow: 0 0 0 4px rgba(26,107,58,0.12) !important;
}
#masterCheck[type="checkbox"]:checked {
    background-color: #1a6b3a !important;
    border-color: #1a6b3a !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' d='M4 10l4 4 8-8'/%3E%3C/svg%3E") !important;
    background-size: 13px !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    box-shadow: 0 0 0 4px rgba(26,107,58,0.18) !important;
}

/* Selected row highlight */
tr:has(.row-check:checked) td {
    background-color: #fff0f0 !important;
    border-bottom: 1px solid #f5c6c6 !important;
}
    /* ── CHECKBOX ALIGNMENT ── */
table thead tr th:first-child,
table tbody tr td:first-child {
    text-align: center;
    vertical-align: middle;
    padding-left: 12px;
    padding-right: 12px;
    width: 44px;
}

.form-check-input[type="checkbox"] {
    display: block;
    margin: 0 auto;
    float: none;
    vertical-align: middle;
    position: relative;
    top: 0;
}
</style>

<div class="container-fluid">
    <div class="page-header">
        <div class="hdr-icon"><i class="fas fa-users"></i></div>
        <div>
            <h2>Youth Member Records</h2>
            <p>Digital database of youth sector members aged 15–30</p>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert-success-custom"><i class="fas fa-check-circle me-2"></i>
        <?= $_GET['success']==='added'?'Youth record added successfully!':($_GET['success']==='updated'?'Record updated!':'Record deleted.') ?>
    </div>
    <?php endif; ?>

    <div class="panel mb-3">
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="keyword" class="form-control search-box" placeholder="Search by name or email…" value="<?= htmlspecialchars($keyword) ?>" style="width:260px;">
            <button type="submit" class="btn-sk btn"><i class="fas fa-search me-1"></i> Search</button>
            <?php if ($keyword): ?><a href="sk_youth_records.php" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
        </form>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span style="font-size:.82rem;color:#888;">Showing <strong><?= $total ?></strong> record(s)</span>
            <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll()">
                <i class="fas fa-check-square me-1"></i> Select All
            </button>
            <button type="button" id="bulkDeleteBtn" class="btn btn-sm btn-danger d-none" data-bs-toggle="modal" data-bs-target="#bulkDelModal">
                <i class="fas fa-trash me-1"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
            <button class="btn-sk btn" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-user-plus me-1"></i> Add Youth Member
            </button>
        </div>
    </div>
</div>

    <div class="panel">
    <form method="POST" id="bulkForm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width:40px;">
                    <input type="checkbox" id="masterCheck" onchange="toggleSelectAll(this.checked)" class="form-check-input">
                </th>
                    <th>#</th><th>Name</th><th>Age</th><th>Sex</th><th>Civil Status</th>
                    <th>Contact</th><th>Email</th><th>Education</th><th>Employment</th><th>Skills</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($youths)): ?>
                <tr><td colspan="11" class="text-center py-4 text-muted">No youth records found.</td></tr>
            <?php else: foreach ($youths as $y): ?>
            <tr>
                <td>
                <input type="checkbox" name="selected_ids[]" value="<?= $y['id_youth'] ?>" 
                    class="form-check-input row-check" onchange="updateBulkBtn()">
            </td>
                <td><?= $y['id_youth'] ?></td>
                <td><strong><?= htmlspecialchars($y['lname'].', '.$y['fname'].' '.$y['mi']) ?></strong></td>
                <td><?= htmlspecialchars($y['age']) ?></td>
                <td><?= htmlspecialchars($y['sex']) ?></td>
                <td><?= htmlspecialchars($y['civil_status']) ?></td>
                <td><?= htmlspecialchars($y['contact_number']) ?></td>
                <td><?= htmlspecialchars($y['email_address']) ?></td>
                <td><?= htmlspecialchars($y['educ_attain']) ?></td>
                <td>
                    <?php
                    $bc=['Employed'=>'badge-emp','Unemployed'=>'badge-unemp','Self-Employed'=>'badge-self','Student'=>'badge-stud'];
                    $bc2=$bc[$y['emp_status']]??'badge-emp';
                    ?><span class="<?= $bc2 ?>"><?= htmlspecialchars($y['emp_status']) ?></span>
                </td>
                <td><?= htmlspecialchars($y['skill_name']) ?></td>
                <td>
                    <button class="action-btn edit-btn me-1"
                        data-bs-toggle="modal" data-bs-target="#editModal"
                        data-id="<?= $y['id_youth'] ?>"
                        data-fname="<?= htmlspecialchars($y['fname'],ENT_QUOTES) ?>"
                        data-lname="<?= htmlspecialchars($y['lname'],ENT_QUOTES) ?>"
                        data-mi="<?= htmlspecialchars($y['mi'],ENT_QUOTES) ?>"
                        data-age="<?= htmlspecialchars($y['age'],ENT_QUOTES) ?>"
                        data-sex="<?= htmlspecialchars($y['sex'],ENT_QUOTES) ?>"
                        data-civil="<?= htmlspecialchars($y['civil_status'],ENT_QUOTES) ?>"
                        data-contact="<?= htmlspecialchars($y['contact_number'],ENT_QUOTES) ?>"
                        data-email="<?= htmlspecialchars($y['email_address'],ENT_QUOTES) ?>"
                        data-educ="<?= htmlspecialchars($y['educ_attain'],ENT_QUOTES) ?>"
                        data-emp="<?= htmlspecialchars($y['emp_status'],ENT_QUOTES) ?>"
                        data-skill="<?= htmlspecialchars($y['skill_name'],ENT_QUOTES) ?>"
                    ><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" class="action-btn del-btn" 
                    data-bs-toggle="modal" data-bs-target="#delModal" 
                    data-id="<?= $y['id_youth'] ?>" 
                    data-name="<?= htmlspecialchars($y['lname'].', '.$y['fname'],ENT_QUOTES) ?>">
                    <i class="fas fa-trash"></i>
                </button>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
      </div>
    </form>
    </div>
</div>

<!-- Add Modal -->

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Youth Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
      <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label fw-semibold">Last Name</label><input name="lname" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">First Name</label><input name="fname" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">Middle Initial</label><input name="mi" class="form-control" maxlength="5"></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Age</label><input name="age" class="form-control" type="number" min="15" max="30" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Sex</label>
                <select name="sex" class="form-select" required><option value="">--</option><option>Male</option><option>Female</option></select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Civil Status</label>
                <select name="civil_status" class="form-select" required><option value="">--</option><option>Single</option><option>Married</option><option>Solo Parent</option><option>Widowed</option></select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Contact Number</label><input name="contact_number" class="form-control" maxlength="15"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Email Address</label><input name="email_address" class="form-control" type="email"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Educational Attainment</label><input name="educ_attain" class="form-control" placeholder="e.g. College Graduate"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Employment Status</label>
                <select name="emp_status" class="form-select" required><option value="">--</option><option>Employed</option><option>Unemployed</option><option>Self-Employed</option><option>Student</option></select>
            </div>
            <div class="col-12"><label class="form-label fw-semibold">Skills</label><input name="skill_name" class="form-control" placeholder="e.g. Programming, Arts, Sports"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="add_youth" class="btn-sk btn"><i class="fas fa-save me-1"></i> Save Record</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Youth Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
      <input type="hidden" name="id_youth" id="edit_id">
      <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label fw-semibold">Last Name</label><input name="lname" id="edit_lname" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">First Name</label><input name="fname" id="edit_fname" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">Middle Initial</label><input name="mi" id="edit_mi" class="form-control" maxlength="5"></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Age</label><input name="age" id="edit_age" class="form-control" type="number" min="15" max="30" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Sex</label>
                <select name="sex" id="edit_sex" class="form-select" required><option value="">--</option><option>Male</option><option>Female</option></select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Civil Status</label>
                <select name="civil_status" id="edit_civil" class="form-select" required><option value="">--</option><option>Single</option><option>Married</option><option>Solo Parent</option><option>Widowed</option></select>
            </div>
            <div class="col-md-6"><label class="form-label fw-semibold">Contact Number</label><input name="contact_number" id="edit_contact" class="form-control" maxlength="15"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Email Address</label><input name="email_address" id="edit_email" class="form-control" type="email"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Educational Attainment</label><input name="educ_attain" id="edit_educ" class="form-control"></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Employment Status</label>
                <select name="emp_status" id="edit_emp" class="form-select" required><option value="">--</option><option>Employed</option><option>Unemployed</option><option>Self-Employed</option><option>Student</option></select>
            </div>
            <div class="col-12"><label class="form-label fw-semibold">Skills</label><input name="skill_name" id="edit_skill" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="edit_youth" class="btn-sk btn"><i class="fas fa-save me-1"></i> Update Record</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#c0392b;">
        <h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Delete Youth Record</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="del_name"></strong>?</p>
        <p class="text-danger"><small>This action cannot be undone.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST">
          <input type="hidden" name="id_youth" id="del_id">
          <button type="submit" name="delete_youth" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDelModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#c0392b;">
        <h5 class="modal-title text-white"><i class="fas fa-trash me-2"></i>Delete Selected Records</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong><span id="bulkCount">0</span> selected record(s)</strong>?</p>
        <p class="text-danger"><small>This action cannot be undone.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="submitBulkDelete()">
            <i class="fas fa-trash me-1"></i> Delete All Selected
        </button>
      </div>
    </div>
  </div>
</div>
<script>
function toggleSelectAll(checked) {
    const boxes = document.querySelectorAll('.row-check');
    const master = document.getElementById('masterCheck');
    // called from button or checkbox
    const state = (checked !== undefined) ? checked : !allChecked();
    boxes.forEach(b => b.checked = state);
    if (master) master.checked = state;
    updateBulkBtn();
}

function allChecked() {
    const boxes = document.querySelectorAll('.row-check');
    return [...boxes].every(b => b.checked);
}

function updateBulkBtn() {
    const checked = document.querySelectorAll('.row-check:checked');
    const btn = document.getElementById('bulkDeleteBtn');
    const count = document.getElementById('selectedCount');
    const bulkCount = document.getElementById('bulkCount');
    const master = document.getElementById('masterCheck');
    const total = document.querySelectorAll('.row-check').length;

    count.textContent = checked.length;
    if (bulkCount) bulkCount.textContent = checked.length;
    btn.classList.toggle('d-none', checked.length === 0);
    if (master) master.checked = checked.length === total && total > 0;
}

function submitBulkDelete() {
    const form = document.getElementById('bulkForm');
    
    // Add bulk_delete_youth flag
    let hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'bulk_delete_youth';
    hidden.value = '1';
    form.appendChild(hidden);

    // Copy checked IDs into the form explicitly
    document.querySelectorAll('.row-check:checked').forEach(function(cb) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    form.method = 'POST';
    form.action = 'sk_youth_records.php';
    form.submit();
}

</script>
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('edit_id').value    = b.dataset.id;
    document.getElementById('edit_lname').value = b.dataset.lname;
    document.getElementById('edit_fname').value = b.dataset.fname;
    document.getElementById('edit_mi').value    = b.dataset.mi;
    document.getElementById('edit_age').value   = b.dataset.age;
    document.getElementById('edit_sex').value   = b.dataset.sex;
    document.getElementById('edit_civil').value = b.dataset.civil;
    document.getElementById('edit_contact').value= b.dataset.contact;
    document.getElementById('edit_email').value = b.dataset.email;
    document.getElementById('edit_educ').value  = b.dataset.educ;
    document.getElementById('edit_emp').value   = b.dataset.emp;
    document.getElementById('edit_skill').value = b.dataset.skill;
});
document.getElementById('delModal').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('del_id').value   = b.dataset.id;
    document.getElementById('del_name').textContent = b.dataset.name;
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.x.x/dist/js/bootstrap.bundle.min.js"></script>
<?php include('dashboard_sidebar_end.php'); ?>
