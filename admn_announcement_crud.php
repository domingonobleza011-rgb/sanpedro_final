<?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   require('classes/resident.class.php');

   $userdetails = $bmis->get_userdata();
   $bmis->validate_admin();
   $current_admin_id = $userdetails['id_resident'];

   if(isset($_POST['create_announce'])) {
       $bmis->create_announcement();
       header("Location: admn_announcement_crud.php?toast=created");
       exit();
   }

   if(isset($_POST['delete_announcement'])) {
       $bmis->admin_delete_announcement();
       header("Location: admn_announcement_crud.php?toast=deleted");
       exit();
   }

   $view = $bmis->view_announcement(); 
   $announcementcount = $bmis->count_announcement();

   $dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
   $cdate = $dt->format('Y/m/d');   
?>

<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .main-container { padding: 30px; background-color: #f8f9fa; min-height: 100vh; }
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.3s; }
    .card-header { border-top-left-radius: 15px !important; border-top-right-radius: 15px !important; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .btn-primary { background: linear-gradient(45deg, #007bff, #0056b3); border: none; box-shadow: 0 4px 10px rgba(0,123,255,0.3); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,123,255,0.4); }
    .table thead { background-color: #f1f3f5; }
    .announcement-preview { border-left: 8px solid #28a745 !important; background: #fff; }
    .clickable-row { cursor: pointer; transition: background 0.2s; }
    .clickable-row:hover { background-color: #e9ecef !important; }

    /* ── Confirmation modal ── */
    .bmis-modal-backdrop {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.45); align-items: center; justify-content: center;
    }
    .bmis-modal-backdrop.open { display: flex; }
    .bmis-modal-card {
        background: #fff; border-radius: 14px; padding: 28px 32px;
        width: 100%; max-width: 430px; margin: 0 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    }
    .bmis-modal-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .bmis-modal-title { font-size: 15px; font-weight: 600; margin: 0; color: #0f2d5a; }
    .bmis-modal-sub   { font-size: 13px; color: #6b7280; margin: 0; }
    .bmis-modal-info  { border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px; }
    .bmis-modal-info p { margin: 0; }
    .bmis-btn-cancel {
        padding: 8px 18px; font-size: 13px; border-radius: 8px; cursor: pointer;
        border: 1px solid #d1d5db; background: #fff; color: #6b7280;
    }
    .bmis-btn-confirm {
        padding: 8px 20px; font-size: 13px; font-weight: 600; border-radius: 8px;
        cursor: pointer; border: none; color: #fff;
    }

    /* ── Success toast ── */
    #bmisToast {
        display: none; position: fixed; bottom: 28px; right: 28px;
        z-index: 10000; min-width: 300px; max-width: 400px;
    }
    #bmisToastInner {
        border-radius: 14px; padding: 16px 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        display: flex; align-items: flex-start; gap: 14px;
        animation: toastIn 0.3s ease;
    }
    #bmisToastIcon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
    }
    @keyframes toastIn {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

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

body { font-family: 'DM Sans', -apple-system, sans-serif !important; background: var(--cream) !important; color: var(--text-dark) !important; }
h1, h2, h3, h4, h5, h6 { font-family: 'DM Sans', sans-serif !important; }
h4 { font-weight: 700 !important; font-size: 1.05rem !important; color: var(--navy) !important; letter-spacing: 0.2px; display: flex; align-items: center; gap: 10px; }
h4::before { content: ''; display: inline-block; width: 4px; height: 20px; background: linear-gradient(to bottom, var(--gold), var(--gold-light)); border-radius: 4px; flex-shrink: 0; }
hr { border-color: var(--border) !important; opacity: 1 !important; margin: 0.5rem 0 !important; }
.sidebar { background: linear-gradient(180deg, var(--navy) 0%, var(--navy-mid) 60%, #153560 100%) !important; border-right: none !important; box-shadow: 4px 0 24px rgba(15,45,90,0.18); }
.sidebar-brand { padding: 1.6rem 1rem 1.4rem !important; background: rgba(0,0,0,0.12) !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; gap: 10px; }
.sidebar-brand-text { font-family: 'DM Sans', sans-serif !important; font-size: 0.82rem !important; font-weight: 600 !important; letter-spacing: 0.3px !important; color: rgba(255,255,255,0.95) !important; text-transform: none !important; line-height: 1.3; }
.sidebar-divider { border-top-color: rgba(255,255,255,0.08) !important; margin: 0.6rem 1rem !important; }
.sidebar-heading { font-size: 0.65rem !important; font-weight: 700 !important; letter-spacing: 1.8px !important; text-transform: uppercase !important; color: rgba(255,255,255,0.35) !important; padding: 0.8rem 1.2rem 0.4rem !important; }
.sidebar .nav-item .nav-link { color: rgba(255,255,255,0.72) !important; font-size: 0.875rem !important; font-weight: 400 !important; padding: 10px 20px !important; border-radius: 0 !important; transition: all var(--transition) !important; display: flex; align-items: center; gap: 10px; border-left: 3px solid transparent; }
.sidebar .nav-item .nav-link i, .sidebar .nav-item .nav-link .bi { font-size: 0.95rem; width: 18px; text-align: center; flex-shrink: 0; color: rgba(255,255,255,0.5); transition: color var(--transition); }
.sidebar .nav-item .nav-link:hover { color: var(--white) !important; background: rgba(255,255,255,0.07) !important; border-left-color: rgba(201,148,58,0.5) !important; }
.sidebar .nav-item .nav-link:hover i, .sidebar .nav-item .nav-link:hover .bi { color: var(--gold-light); }
.sidebar .nav-item.active .nav-link, .sidebar .nav-item .nav-link.active { color: var(--white) !important; background: rgba(201,148,58,0.15) !important; border-left-color: var(--gold) !important; font-weight: 500 !important; }
.topbar { background: var(--white) !important; box-shadow: 0 2px 16px rgba(15,45,90,0.08) !important; border-bottom: 1px solid var(--border) !important; padding: 0 20px !important; height: 60px; align-items: center; }
.topbar .nav-item .nav-link { color: var(--text-mid) !important; font-size: 0.875rem; font-weight: 500; padding: 8px 14px !important; border-radius: 8px; transition: all var(--transition); display: flex; align-items: center; gap: 8px; }
.topbar .nav-item .nav-link:hover { background: var(--cream); color: var(--navy) !important; }
.topbar .text-gray-800 { color: var(--text-dark) !important; font-weight: 500; }
#content-wrapper { background: var(--cream) !important; }
#content { padding-bottom: 2rem; }
.container-fluid { padding: 1.5rem 2rem !important; }
.card { border: none !important; border-radius: var(--radius) !important; box-shadow: var(--shadow-sm) !important; transition: all var(--transition) !important; overflow: hidden; background: var(--white) !important; }
.card:hover { box-shadow: var(--shadow-md) !important; transform: translateY(-3px); }
.card-body { padding: 1.4rem 1.6rem !important; }
.card.border-left-primary { border-top: 3px solid var(--navy-light) !important; border-left: none !important; }
.card.border-left-info    { border-top: 3px solid var(--teal)       !important; border-left: none !important; }
.card.border-left-danger  { border-top: 3px solid var(--danger)     !important; border-left: none !important; }
.card.border-left-warning { border-top: 3px solid var(--warning)    !important; border-left: none !important; }
.card.border-left-success { border-top: 3px solid var(--success)    !important; border-left: none !important; }
.table { font-size: 0.875rem; }
.table thead th { background: var(--navy); color: var(--white); font-weight: 600; letter-spacing: 0.5px; font-size: 0.78rem; text-transform: uppercase; border: none; padding: 12px 16px; }
.table tbody tr:hover { background: var(--navy-pale); }
.table td, .table th { border-color: var(--border); vertical-align: middle; padding: 10px 16px; }
.btn-primary { background: linear-gradient(135deg, var(--navy), var(--navy-light)) !important; border: none !important; border-radius: 8px !important; font-weight: 600 !important; font-size: 0.875rem !important; letter-spacing: 0.3px; box-shadow: 0 3px 10px rgba(15,45,90,0.25) !important; transition: all var(--transition) !important; }
.btn-primary:hover { transform: translateY(-1px) !important; box-shadow: 0 6px 18px rgba(15,45,90,0.3) !important; }
.sticky-footer { background: var(--white) !important; border-top: 1px solid var(--border) !important; font-size: 0.8rem !important; color: var(--text-light) !important; padding: 16px 24px !important; }
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(15,45,90,0.15); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: rgba(15,45,90,0.28); }
@media (max-width: 768px) { .container-fluid { padding: 1rem 1.2rem !important; } .card:hover { transform: none; } }
</style>

<div class="main-container">
    <div class="row mb-5"> 
        <div class="col-md-12"> 
            <h2 class="text-center fw-bold text-dark">Announcements</h2>
            <p class="text-center text-muted">Create and manage announcements for Barangay San Pedro</p>
        </div>
    </div>
      
    <div class="row g-4"> 
        <!-- CREATE FORM -->
        <div class="col-lg-5"> 
            <div class="card h-100">
                <div class="card-header bg-primary text-white p-3"> 
                   <i class="fas fa-edit me-2"></i> Create New Entry 
                </div>
                <div class="card-body p-4">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-4"> 
                            <label class="form-label fw-bold"><i class="fas fa-bullhorn text-primary me-2"></i>Announcement Message</label>
                            <textarea name="event" class="form-control" rows="5" placeholder="What is happening in the Barangay?" style="border-radius: 10px; border: 1px solid #dee2e6;"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-image text-primary me-2"></i>Upload Posters</label>
                            <input type="file" name="announcement_img[]" class="form-control" accept="image/*" multiple style="border-radius: 10px;">
                            <small class="text-muted">You can select more than one image.</small>
                        </div>
                        <input type="hidden" name="start_date" value="<?= $cdate?>">
                        <input name="addedby" type="hidden" value="<?= $userdetails['surname']?>, <?= $userdetails['firstname']?> <?= $userdetails['mname']?>">
                        <button type="submit" name="create_announce" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px;"> 
                            Publish Announcement 
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- HISTORY LOG -->
        <div class="col-lg-7"> 
            <div class="card h-100">
                <div class="card-header bg-dark text-white p-3"> 
                   <i class="fas fa-list me-2"></i> History Logs 
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead> 
                                <tr>
                                    <th class="ps-4">Content</th>
                                    <th>Date</th>
                                    <th>Posted By</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php if(is_array($view)): ?>
                                    <?php foreach($view as $row): ?>
                                    <tr class="clickable-row" onclick="loadPreview('<?= $row['id_announcement'] ?>')">
                                        <td class="ps-4">
                                            <div class="text-truncate" style="max-width: 200px;">
                                                <?= htmlspecialchars($row['event']) ?>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= $row['start_date'] ?></span></td>
                                        <td><small class="text-muted"><?= $row['addedby'] ?></small></td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-link text-danger p-0"
                                                onclick="event.stopPropagation(); openDeleteModal('<?= $row['id_announcement'] ?>', '<?= addslashes(htmlspecialchars($row['event'])) ?>')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LIVE PREVIEW SECTION -->
    <div class="row mt-5"> 
        <div class="col-12" id="announcement-details-area">
            <div class="card announcement-preview shadow-lg">
                <div class="card-body p-5 text-center text-muted">
                    <i class="fas fa-mouse-pointer mb-3 fa-2x"></i>
                    <p>Select an announcement from the history log to view reactions and comments.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ════════════════════════════════════════════════════════
     HIDDEN DELETE FORM
═══════════════════════════════════════════════════════════ -->
<form id="deleteAnnouncementForm" method="POST" action="admn_announcement_crud.php" style="display:none;">
    <input type="hidden" name="id_announcement" id="deleteAnnouncementId">
    <input type="hidden" name="delete_announcement" value="1">
</form>


<!-- ════════════════════════════════════════════════════════
     DELETE MODAL
═══════════════════════════════════════════════════════════ -->
<div id="deleteAnnouncementModal" class="bmis-modal-backdrop">
  <div class="bmis-modal-card">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div class="bmis-modal-icon" style="background:#fee2e2;">
        <i class="fas fa-trash-alt" style="color:#dc2626; font-size:18px;"></i>
      </div>
      <div>
        <p class="bmis-modal-title">Delete announcement</p>
        <p class="bmis-modal-sub">This action cannot be undone.</p>
      </div>
    </div>
    <hr style="margin:16px 0; border-color:#e5e7eb;">
    <div class="bmis-modal-info" style="background:#fef2f2; border:1.5px solid #fecaca;">
      <i class="fas fa-exclamation-triangle" style="color:#dc2626; font-size:20px; flex-shrink:0;"></i>
      <div>
        <p id="deleteAnnouncementPreview" style="font-size:13px; font-weight:600; color:#991b1b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:280px;"></p>
        <p style="font-size:12px; color:#b91c1c; margin-top:2px;">This announcement will be permanently removed.</p>
      </div>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button class="bmis-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
      <button class="bmis-btn-confirm" style="background:linear-gradient(135deg,#dc2626,#ef4444);"
              onclick="document.getElementById('deleteAnnouncementForm').submit();">
        <i class="fas fa-trash-alt" style="margin-right:5px;"></i> Yes, delete
      </button>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════
     SUCCESS TOAST
═══════════════════════════════════════════════════════════ -->
<div id="bmisToast">
  <div id="bmisToastInner">
    <div id="bmisToastIcon">
      <i id="bmisToastIconI"></i>
    </div>
    <div style="flex:1; min-width:0;">
      <p id="bmisToastTitle" style="font-size:13px; font-weight:700; margin:0 0 2px;"></p>
      <p id="bmisToastMsg"   style="font-size:12px; margin:0; color:#6b7280; line-height:1.4;"></p>
    </div>
    <button onclick="closeToast()" style="background:none; border:none; cursor:pointer; color:#9ca3af; font-size:16px; padding:0; flex-shrink:0; line-height:1;">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>


<script>
// ── Delete modal ─────────────────────────────────────────────
function openDeleteModal(id, preview) {
    document.getElementById('deleteAnnouncementId').value       = id;
    document.getElementById('deleteAnnouncementPreview').textContent = preview;
    document.getElementById('deleteAnnouncementModal').classList.add('open');
}

function closeDeleteModal() {
    document.getElementById('deleteAnnouncementModal').classList.remove('open');
}

// Close on backdrop click
document.getElementById('deleteAnnouncementModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});


// ── Toast ────────────────────────────────────────────────────
var toastConfigs = {
    created: { type: 'success', title: 'Published',  msg: 'Announcement published successfully.' },
    deleted: { type: 'delete',  title: 'Deleted',    msg: 'Announcement permanently deleted.' },
};

function showToast(cfg) {
    var isSuccess = cfg.type === 'success';
    var bg        = isSuccess ? '#f0fdf4' : '#fef2f2';
    var border    = isSuccess ? '#bbf7d0' : '#fecaca';
    var iconBg    = isSuccess ? '#d1fae5' : '#fee2e2';
    var iconColor = isSuccess ? '#059669' : '#dc2626';
    var iconCls   = isSuccess ? 'fa-check-circle' : 'fa-trash-alt';

    var inner = document.getElementById('bmisToastInner');
    inner.style.background = bg;
    inner.style.border     = '1.5px solid ' + border;

    var icon = document.getElementById('bmisToastIcon');
    icon.style.background  = iconBg;

    var iconI = document.getElementById('bmisToastIconI');
    iconI.className        = 'fas ' + iconCls;
    iconI.style.color      = iconColor;
    iconI.style.fontSize   = '16px';

    var titleEl = document.getElementById('bmisToastTitle');
    titleEl.textContent    = cfg.title;
    titleEl.style.color    = iconColor;

    document.getElementById('bmisToastMsg').textContent = cfg.msg;
    document.getElementById('bmisToast').style.display  = 'block';

    setTimeout(closeToast, 4500);
}

function closeToast() {
    var t = document.getElementById('bmisToast');
    t.style.opacity    = '0';
    t.style.transition = 'opacity 0.3s';
    setTimeout(function() { t.style.display = 'none'; t.style.opacity = ''; t.style.transition = ''; }, 300);
}

(function() {
    var params = new URLSearchParams(window.location.search);
    var key    = params.get('toast');
    if (key && toastConfigs[key]) {
        showToast(toastConfigs[key]);
        history.replaceState(null, '', window.location.pathname);
    }
})();


// ── Announcement preview ─────────────────────────────────────
function loadPreview(id) {
    document.getElementById('announcement-details-area').innerHTML =
        '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
    fetch('fetch_announcement_details.php?id=' + id)
        .then(r => r.text())
        .then(data => { document.getElementById('announcement-details-area').innerHTML = data; })
        .catch(() => { showToast({ type: 'delete', title: 'Error', msg: 'Could not load announcement details.' }); });
}
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<?php include('dashboard_sidebar_end.php'); ?>