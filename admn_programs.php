<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    require('classes/resident.class.php');

    $userdetails = $bmis->get_userdata();
    $bmis->validate_admin();

    // Handle all POST actions
    $bmis->create_program();
    $bmis->update_program();
    $bmis->delete_program();
    $bmis->mark_attendance();
    $bmis->upload_program_media();
    $bmis->delete_program_media();
    $bmis->admin_update_registration_status();

    // Determine active view: list | calendar | detail | registrations | attendance | gallery
    $activeView  = $_GET['view'] ?? 'list';
    $id_program  = isset($_GET['id_program']) ? (int)$_GET['id_program'] : 0;

    // Filters for list view
    $filters = [];
    if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
    if (!empty($_GET['status']))   $filters['status']   = $_GET['status'];
    if (!empty($_GET['search']))   $filters['search']   = $_GET['search'];

    $programs     = $bmis->view_programs($filters);
    $singleProg   = $id_program ? $bmis->get_single_program($id_program) : null;
    $registrations = $id_program ? $bmis->get_program_registrations($id_program) : [];
    $attendance    = $id_program ? $bmis->get_attendance_report($id_program) : [];
    $gallery       = $id_program ? $bmis->get_program_gallery($id_program) : [];

    // Stats for the selected program
    $totalReg     = count($registrations);
    $totalAttended = count(array_filter($attendance, fn($r) => $r['attended']));

    $dt    = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $today = $dt->format('Y-m-d');
?>
<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .main-container { padding: 24px; background-color: #f4f6f9; min-height: 100vh; }
    .card { border: none; border-radius: 14px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); }
    .card-header { border-radius: 14px 14px 0 0 !important; font-weight: 600; }
    .nav-tabs-custom .nav-link { border-radius: 8px 8px 0 0; font-weight: 500; color: #555; }
    .nav-tabs-custom .nav-link.active { background: #fff; color: #007bff; border-bottom: 3px solid #007bff; }
    .badge-category { font-size: 0.72rem; padding: 4px 10px; border-radius: 20px; }
    .program-card { transition: box-shadow 0.2s; cursor: pointer; }
    .program-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.13); }
    .stat-box { background: #fff; border-radius: 12px; padding: 18px 22px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
    .calendar-event { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; }
    .gallery-img { width: 100%; height: 180px; object-fit: cover; border-radius: 10px; }
    .table th { background: #f1f3f5; font-weight: 600; }
    .attended-yes { color: #28a745; font-weight: 600; }
    .attended-no  { color: #dc3545; }
    #calendarEl { min-height: 520px; }
    .fc-event { cursor: pointer; border-radius: 5px; padding: 2px 5px; font-size: 0.8rem; }
</style>

<div class="main-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-calendar-alt text-primary me-2"></i>Programs &amp; Activities</h2>
            <p class="text-muted mb-0">Manage events, registrations, attendance, and gallery</p>
        </div>
        <div class="d-flex gap-2">
            <a href="admn_programs.php?view=list"     class="btn <?= $activeView==='list'?'btn-primary':'btn-outline-secondary' ?> btn-sm"><i class="fas fa-list me-1"></i>List</a>
            <a href="admn_programs.php?view=calendar" class="btn <?= $activeView==='calendar'?'btn-primary':'btn-outline-secondary' ?> btn-sm"><i class="fas fa-calendar me-1"></i>Calendar</a>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#createProgramModal">
                <i class="fas fa-plus me-1"></i>New Program
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <?php
        $totalProgs     = count($programs);
        $activeProgs    = count(array_filter($programs, fn($p) => in_array($p['status'], ['Registration Open','Ongoing','Published'])));
        $completedProgs = count(array_filter($programs, fn($p) => $p['status'] === 'Completed'));
        $totalParts     = array_sum(array_column($programs, 'total_registrations'));
        ?>
        <div class="col-6 col-md-3">
            <div class="stat-box text-center">
                <div class="fs-2 fw-bold text-primary"><?= $totalProgs ?></div>
                <div class="text-muted small">Total Programs</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-box text-center">
                <div class="fs-2 fw-bold text-success"><?= $activeProgs ?></div>
                <div class="text-muted small">Active Programs</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-box text-center">
                <div class="fs-2 fw-bold text-info"><?= $completedProgs ?></div>
                <div class="text-muted small">Completed</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-box text-center">
                <div class="fs-2 fw-bold text-warning"><?= $totalParts ?></div>
                <div class="text-muted small">Total Participants</div>
            </div>
        </div>
    </div>

    <?php if ($activeView === 'calendar'): ?>
    <!-- ============ CALENDAR VIEW ============ -->
    <div class="card">
        <div class="card-header bg-primary text-white"><i class="fas fa-calendar me-2"></i>Event Calendar</div>
        <div class="card-body">
            <!-- Legend -->
            <div class="d-flex flex-wrap gap-3 mb-3">
                <?php
                $catColors = ['Sports'=>'#28a745','Education'=>'#007bff','Livelihood'=>'#ffc107',
                              'Health'=>'#dc3545','Environment'=>'#20c997','Arts'=>'#6f42c1','Community Service'=>'#fd7e14'];
                foreach ($catColors as $cat => $color): ?>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:<?=$color?>;margin-right:4px;"></span><?=$cat?></span>
                <?php endforeach; ?>
            </div>
            <div id="calendarEl"></div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const catColors = {
            'Sports':'#28a745','Education':'#007bff','Livelihood':'#ffc107',
            'Health':'#dc3545','Environment':'#20c997','Arts':'#6f42c1','Community Service':'#fd7e14'
        };
        const events = <?php
            $calEvents = [];
            foreach ($programs as $p) {
                $c = $catColors[$p['category']] ?? '#007bff';
                $calEvents[] = [
                    'id'    => $p['id_program'],
                    'title' => $p['title'],
                    'start' => $p['start_date'],
                    'end'   => $p['end_date'],
                    'color' => $c,
                    'url'   => 'admn_programs.php?view=detail&id_program=' . $p['id_program']
                ];
            }
            echo json_encode($calEvents);
        ?>;

        new FullCalendar.Calendar(document.getElementById('calendarEl'), {
            initialView: 'dayGridMonth',
            headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,listMonth' },
            events: events,
            eventClick: function(info) { info.jsEvent.preventDefault(); window.location.href = info.event.url; }
        }).render();
    });
    </script>

    <?php elseif ($activeView === 'detail' && $singleProg): ?>
    <!-- ============ PROGRAM DETAIL TABS ============ -->
    <div class="mb-3">
        <a href="admn_programs.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back to List</a>
    </div>
    <!-- Program Header Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <?php if (!empty($singleProg['banner_image'])): ?>
                <div class="col-md-3 mb-3 mb-md-0">
                    <img src="<?= htmlspecialchars($singleProg['banner_image']) ?>" class="img-fluid rounded" style="max-height:160px;width:100%;object-fit:cover;">
                </div>
                <?php endif; ?>
                <div class="col">
                    <h3 class="fw-bold"><?= htmlspecialchars($singleProg['title']) ?></h3>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-primary"><?= $singleProg['category'] ?></span>
                        <span class="badge bg-secondary"><?= $singleProg['event_type'] ?></span>
                        <?php
                        $sc = ['Draft'=>'secondary','Published'=>'info','Registration Open'=>'success','Registration Closed'=>'warning','Ongoing'=>'primary','Completed'=>'dark','Cancelled'=>'danger'];
                        $scl = $sc[$singleProg['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $scl ?>"><?= $singleProg['status'] ?></span>
                    </div>
                    <div class="row g-2 text-muted small">
                        <div class="col-auto"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($singleProg['venue']) ?></div>
                        <div class="col-auto"><i class="fas fa-calendar me-1"></i><?= date('M d, Y', strtotime($singleProg['start_date'])) ?> - <?= date('M d, Y', strtotime($singleProg['end_date'])) ?></div>
                        <div class="col-auto"><i class="fas fa-users me-1"></i><?= $singleProg['total_registrations'] ?><?= $singleProg['max_participants'] ? ' / '.$singleProg['max_participants'] : '' ?> Registered</div>
                    </div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-warning btn-sm me-1" data-toggle="modal" data-target="#editProgramModal"><i class="fas fa-edit me-1"></i>Edit</button>
                    <form method="post" style="display:inline" onsubmit="return confirm('Delete this program?')">
                        <input type="hidden" name="id_program" value="<?= $singleProg['id_program'] ?>">
                        <button name="delete_program" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Sub-tabs -->
    <ul class="nav nav-tabs nav-tabs-custom mb-3" id="detailTabs">
        <li class="nav-item"><a class="nav-link <?= (!isset($_GET['tab'])||$_GET['tab']==='overview')?'active':'' ?>" href="admn_programs.php?view=detail&id_program=<?=$id_program?>&tab=overview">Overview</a></li>
        <li class="nav-item"><a class="nav-link <?= (isset($_GET['tab'])&&$_GET['tab']==='registrations')?'active':'' ?>" href="admn_programs.php?view=detail&id_program=<?=$id_program?>&tab=registrations">Registrations <span class="badge bg-secondary"><?= $totalReg ?></span></a></li>
        <li class="nav-item"><a class="nav-link <?= (isset($_GET['tab'])&&$_GET['tab']==='attendance')?'active':'' ?>" href="admn_programs.php?view=detail&id_program=<?=$id_program?>&tab=attendance">Attendance <span class="badge bg-success"><?= $totalAttended ?></span></a></li>
        <li class="nav-item"><a class="nav-link <?= (isset($_GET['tab'])&&$_GET['tab']==='gallery')?'active':'' ?>" href="admn_programs.php?view=detail&id_program=<?=$id_program?>&tab=gallery">Gallery <span class="badge bg-info"><?= count($gallery) ?></span></a></li>
    </ul>

    <?php
    $tab = $_GET['tab'] ?? 'overview';

    if ($tab === 'overview'): ?>
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">Program Details</div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($singleProg['description'])) ?></p>
                    <hr>
                    <div class="row g-2">
                        <div class="col-md-6"><strong>Registration Deadline:</strong><br><?= date('M d, Y h:i A', strtotime($singleProg['registration_deadline'])) ?></div>
                        <div class="col-md-6"><strong>Age Range:</strong><br><?= $singleProg['target_age_min'] ?> – <?= $singleProg['target_age_max'] ?> years old</div>
                        <div class="col-md-6"><strong>Contact Person:</strong><br><?= htmlspecialchars($singleProg['contact_person']) ?></div>
                        <div class="col-md-6"><strong>Contact Number:</strong><br><?= htmlspecialchars($singleProg['contact_number']) ?></div>
                        <?php if (!empty($singleProg['requirements'])): ?>
                        <div class="col-12"><strong>Requirements:</strong><br><?= nl2br(htmlspecialchars($singleProg['requirements'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box mb-3 text-center">
                <div class="fs-3 fw-bold text-primary"><?= $totalReg ?></div>
                <div class="text-muted">Registrations</div>
            </div>
            <div class="stat-box mb-3 text-center">
                <div class="fs-3 fw-bold text-success"><?= $totalAttended ?></div>
                <div class="text-muted">Attended</div>
            </div>
            <div class="stat-box text-center">
                <div class="fs-3 fw-bold text-warning"><?= $totalReg > 0 ? round(($totalAttended/$totalReg)*100) : 0 ?>%</div>
                <div class="text-muted">Attendance Rate</div>
            </div>
        </div>
    </div>

    <?php elseif ($tab === 'registrations'): ?>
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clipboard-list me-2"></i>Registrations</span>
            <a href="admn_programs_export.php?type=registrations&id_program=<?=$id_program?>" class="btn btn-success btn-sm"><i class="fas fa-file-csv me-1"></i>Export CSV</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr>
                        <th class="ps-3">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($registrations as $i => $reg): ?>
                    <tr>
                        <td class="ps-3"><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($reg['firstname'].' '.$reg['surname']) ?></td>
                        <td><?= htmlspecialchars($reg['email']) ?></td>
                        <td><?= htmlspecialchars($reg['phone_number']) ?></td>
                        <td><code><?= $reg['registration_code'] ?></code></td>
                        <td><?= date('M d, Y', strtotime($reg['registration_date'])) ?></td>
                        <td>
                            <?php $sbg=['Approved'=>'success','Pending'=>'warning','Waitlisted'=>'info','Rejected'=>'danger','Cancelled'=>'secondary']; ?>
                            <span class="badge bg-<?= $sbg[$reg['status']] ?? 'secondary' ?>"><?= $reg['status'] ?></span>
                        </td>
                        <td class="text-center">
                            <form method="post" class="d-flex gap-1 justify-content-center">
                                <input type="hidden" name="id_registration" value="<?= $reg['id_registration'] ?>">
                                <select name="reg_status" class="form-select form-select-sm" style="width:auto">
                                    <?php foreach (['Approved','Pending','Waitlisted','Rejected','Cancelled'] as $st): ?>
                                    <option <?= $reg['status']===$st?'selected':'' ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button name="update_reg_status" class="btn btn-primary btn-sm">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($registrations)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No registrations yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($tab === 'attendance'): ?>
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span><i class="fas fa-check-circle me-2"></i>Attendance</span>
            <a href="admn_programs_export.php?type=attendance&id_program=<?=$id_program?>" class="btn btn-success btn-sm"><i class="fas fa-file-csv me-1"></i>Export CSV</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr>
                        <th class="ps-3">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Reg Code</th>
                        <th>Registered</th>
                        <th class="text-center">Attended</th>
                        <th>Attendance Date</th>
                        <th class="text-center">Mark</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($attendance as $i => $row): ?>
                    <tr>
                        <td class="ps-3"><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($row['firstname'].' '.$row['surname']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><code><?= $row['registration_code'] ?></code></td>
                        <td><?= date('M d, Y', strtotime($row['registration_date'])) ?></td>
                        <td class="text-center">
                            <?php if ($row['attended']): ?>
                                <span class="attended-yes"><i class="fas fa-check-circle"></i> Yes</span>
                            <?php else: ?>
                                <span class="attended-no"><i class="fas fa-times-circle"></i> No</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['attendance_date'] ? date('M d, Y h:i A', strtotime($row['attendance_date'])) : '—' ?></td>
                        <td class="text-center">
                            <form method="post" class="d-flex gap-1 justify-content-center">
                                <input type="hidden" name="registration_id" value="<?= $row['id_registration'] ?>">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="attended" <?= $row['attended'] ? 'checked' : '' ?> id="att_<?= $row['id_registration'] ?>">
                                    <label class="form-check-label" for="att_<?= $row['id_registration'] ?>">Present</label>
                                </div>
                                <button name="mark_attendance" class="btn btn-primary btn-sm">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendance)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No approved registrations yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($tab === 'gallery'): ?>
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span><i class="fas fa-images me-2"></i>Photo Gallery</span>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadMediaModal"><i class="fas fa-upload me-1"></i>Upload Media</button>
        </div>
        <div class="card-body">
            <?php if (empty($gallery)): ?>
                <p class="text-center text-muted py-4">No media uploaded yet.</p>
            <?php else: ?>
            <div class="row g-3">
                <?php foreach ($gallery as $g): ?>
                <div class="col-6 col-md-3">
                    <div class="position-relative">
                        <?php if ($g['file_type'] === 'Image'): ?>
                            <a href="<?= htmlspecialchars($g['file_path']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($g['file_path']) ?>" class="gallery-img" alt="gallery">
                            </a>
                        <?php else: ?>
                            <video controls class="gallery-img"><source src="<?= htmlspecialchars($g['file_path']) ?>"></video>
                        <?php endif; ?>
                        <form method="post" class="mt-1" onsubmit="return confirm('Delete this media?')">
                            <input type="hidden" name="id_media" value="<?= $g['id_media'] ?>">
                            <button name="delete_media" class="btn btn-danger btn-sm w-100"><i class="fas fa-trash me-1"></i>Delete</button>
                        </form>
                        <?php if (!empty($g['caption'])): ?>
                            <small class="text-muted d-block text-truncate"><?= htmlspecialchars($g['caption']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upload Media Modal -->
    <div class="modal fade" id="uploadMediaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Media</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="program_id" value="<?= $id_program ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Files (Images / Videos)</label>
                            <input type="file" name="media_files[]" class="form-control" multiple accept="image/*,video/mp4" required>
                            <small class="text-muted">Max 10MB per file. Accepted: JPG, PNG, GIF, MP4</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Caption (optional)</label>
                            <input type="text" name="captions[]" class="form-control" placeholder="Add a caption for the first file...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="upload_media" class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

 <!-- Edit Program Modal -->
<div class="modal fade" id="editProgramModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i> Edit Program
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProgramForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="program_id" id="edit_program_id">
                
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Program Title *</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-select" name="category" id="edit_category" required>
                                <option value="">Select Category</option>
                                <option value="Sports">Sports</option>
                                <option value="Education">Education</option>
                                <option value="Livelihood">Livelihood</option>
                                <option value="Health">Health</option>
                                <option value="Environment">Environment</option>
                                <option value="Arts">Arts & Culture</option>
                                <option value="Community Service">Community Service</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event Type *</label>
                            <select class="form-select" name="event_type" id="edit_event_type" required>
                                <option value="">Select Type</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Training">Training</option>
                                <option value="Competition">Competition</option>
                                <option value="Outreach">Outreach</option>
                                <option value="Festival">Festival</option>
                                <option value="Meeting">Meeting</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Venue *</label>
                            <input type="text" class="form-control" name="venue" id="edit_venue" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Start Date & Time *</label>
                            <input type="datetime-local" class="form-control" name="start_date" id="edit_start_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">End Date & Time *</label>
                            <input type="datetime-local" class="form-control" name="end_date" id="edit_end_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Registration Deadline *</label>
                            <input type="datetime-local" class="form-control" name="registration_deadline" id="edit_registration_deadline" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Participants</label>
                            <input type="number" class="form-control" name="max_participants" id="edit_max_participants" min="1">
                            <small class="text-muted">Leave blank for unlimited</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Min Age *</label>
                            <input type="number" class="form-control" name="target_age_min" id="edit_target_age_min" value="15" min="1" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Age *</label>
                            <input type="number" class="form-control" name="target_age_max" id="edit_target_age_max" value="30" min="1" max="100" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Requirements</label>
                        <textarea class="form-control" name="requirements" id="edit_requirements" rows="2" 
                                  placeholder="e.g., Valid ID, Medical Certificate, etc."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person *</label>
                            <input type="text" class="form-control" name="contact_person" id="edit_contact_person" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Number *</label>
                            <input type="tel" class="form-control" name="contact_number" id="edit_contact_number" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Banner Image</label>
                            <input type="file" class="form-control" name="banner" id="edit_banner" accept="image/*">
                            <small class="text-muted">Leave blank to keep current banner</small>
                            <div id="current_banner_preview" class="mt-2"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                                <option value="Registration Open">Registration Open</option>
                                <option value="Registration Closed">Registration Closed</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <?php else: ?>
    <!-- ============ LIST VIEW ============ -->
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="get" class="row g-2 align-items-center">
                <input type="hidden" name="view" value="list">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search programs..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <?php foreach (['Sports','Education','Livelihood','Health','Environment','Arts','Community Service'] as $c): ?>
                        <option <?= ($_GET['category']??'')===$c?'selected':'' ?>><?=$c?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <?php foreach (['Draft','Published','Registration Open','Registration Closed','Ongoing','Completed','Cancelled'] as $s): ?>
                        <option <?= ($_GET['status']??'')===$s?'selected':'' ?>><?=$s?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="admn_programs.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Programs Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-list me-2"></i>Programs List
            <span class="badge bg-white text-primary ms-2"><?= count($programs) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr>
                        <th class="ps-3">#</th>
                        <th>Program</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th class="text-center">Registrations</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($programs as $i => $prog):
                        $catColors2 = ['Sports'=>'success','Education'=>'primary','Livelihood'=>'warning','Health'=>'danger','Environment'=>'teal','Arts'=>'purple','Community Service'=>'orange'];
                        $sc2 = ['Draft'=>'secondary','Published'=>'info','Registration Open'=>'success','Registration Closed'=>'warning','Ongoing'=>'primary','Completed'=>'dark','Cancelled'=>'danger'];
                    ?>
                    <tr>
                        <td class="ps-3"><?= $i+1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($prog['title']) ?></strong>
                            <div class="text-muted small"><?= $prog['program_code'] ?></div>
                        </td>
                        <td><span class="badge badge-category bg-primary"><?= $prog['category'] ?></span></td>
                        <td><span class="badge bg-light text-dark border"><?= $prog['event_type'] ?></span></td>
                        <td>
                            <small><?= date('M d, Y', strtotime($prog['start_date'])) ?></small>
                        </td>
                        <td><small><?= htmlspecialchars($prog['venue']) ?></small></td>
                        <td class="text-center">
                            <span class="badge bg-info"><?= $prog['total_registrations'] ?><?= $prog['max_participants'] ? '/'.$prog['max_participants'] : '' ?></span>
                        </td>
                        <td><span class="badge bg-<?= $sc2[$prog['status']] ?? 'secondary' ?>"><?= $prog['status'] ?></span></td>
                        <td class="text-center">
                            <a href="admn_programs.php?view=detail&id_program=<?= $prog['id_program'] ?>" class="btn btn-sm btn-primary" title="View"><i class="fas fa-eye"></i></a>
                            <a href="admn_programs.php?view=detail&id_program=<?= $prog['id_program'] ?>&tab=registrations" class="btn btn-sm btn-info" title="Registrations"><i class="fas fa-users"></i></a>
                            <a href="admn_programs.php?view=detail&id_program=<?= $prog['id_program'] ?>&tab=attendance" class="btn btn-sm btn-success" title="Attendance"><i class="fas fa-check"></i></a>
                            <a href="admn_programs.php?view=detail&id_program=<?= $prog['id_program'] ?>&tab=gallery" class="btn btn-sm btn-secondary" title="Gallery"><i class="fas fa-images"></i></a>
                            <form method="post" style="display:inline" onsubmit="return confirm('Delete this program?')">
                                <input type="hidden" name="id_program" value="<?= $prog['id_program'] ?>">
                                <button name="delete_program" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($programs)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-5">No programs found. Click <strong>New Program</strong> to create one.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- end main-container -->

<!-- Create Program Modal -->
<div class="modal fade" id="createProgramModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus me-2"></i>Create New Program</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Program Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="Enter program title">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category</option>
                                <?php foreach (['Sports','Education','Livelihood','Health','Environment','Arts','Community Service'] as $c): ?>
                                <option><?=$c?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="Describe this program..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Event Type <span class="text-danger">*</span></label>
                            <select name="event_type" class="form-select" required>
                                <option value="">Select type</option>
                                <?php foreach (['Workshop','Seminar','Training','Competition','Outreach','Festival','Meeting'] as $t): ?>
                                <option><?=$t?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Venue <span class="text-danger">*</span></label>
                            <input type="text" name="venue" class="form-control" required placeholder="Event venue">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Registration Deadline <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="registration_deadline" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Max Participants</label>
                            <input type="number" name="max_participants" class="form-control" min="1" placeholder="Leave blank for unlimited">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Min Age <span class="text-danger">*</span></label>
                            <input type="number" name="target_age_min" class="form-control" value="15" min="1" max="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Max Age <span class="text-danger">*</span></label>
                            <input type="number" name="target_age_max" class="form-control" value="30" min="1" max="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="contact_person" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                            <input type="tel" name="contact_number" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Requirements</label>
                            <textarea name="requirements" class="form-control" rows="2" placeholder="e.g., Valid ID, Medical Certificate..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Banner Image</label>
                            <input type="file" name="banner" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <?php foreach (['Draft','Published','Registration Open','Registration Closed','Ongoing','Completed','Cancelled'] as $s): ?>
                                <option><?=$s?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_program" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('dashboard_sidebar_end.php'); ?>