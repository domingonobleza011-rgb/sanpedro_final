<?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   require('classes/resident.class.php');
   
   $userdetails = $bmis->get_userdata();
   $bmis->validate_staff();
   $current_admin_id = $userdetails['id_resident']; 

   $bmis->create_announcement();
   $bmis->admin_delete_announcement(); 
   
   $view = $bmis->view_announcement(); 
   $announcementcount = $bmis->count_announcement();

   $dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
   $cdate = $dt->format('Y/m/d');   
   
   // DO NOT put $bmis->get_reactions($row['...']) here!
?>

<?php 
    include('dashboard_sidebar_start_staff.php');
?>

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
</style>

<div class="main-container">
    <div class="row mb-5"> 
        <div class="col-md-12"> 
            <h2 class="text-center fw-bold text-dark">Announcements</h2>
            <p class="text-center text-muted">Create and manage announcements for Barangay San Pedro</p>
        </div>
    </div>
      
    <div class="row g-4"> 
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
        <tr class="clickable-row" onclick="loadPreview('<?= $row['id_announcement']; ?>')">
            <td class="ps-4">
                <div class="text-truncate" style="max-width: 200px;">
                    <?= htmlspecialchars($row['event']); ?>
                </div>
            </td>
            <td><span class="badge bg-light text-dark border"><?= $row['start_date'];?></span></td>
            <td><small class="text-muted"><?= $row['addedby'];?></small></td>
            <td class="text-center">     
                <form action="" method="post" onsubmit="event.stopPropagation(); return confirm('Delete this post?');">
                    <input type="hidden" name="id_announcement" value="<?= $row['id_announcement'];?>">
                    <button class="btn btn-link text-danger p-0" type="submit" name="delete_announcement">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
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
        <!-- This area will be replaced by AJAX -->
        <div class="card announcement-preview shadow-lg">
            <div class="card-body p-5 text-center text-muted">
                <i class="fas fa-mouse-pointer mb-3 fa-2x"></i>
                <p>Select an announcement from the history log to view reactions and comments.</p>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function loadPreview(id) {
    // Show a loading state
    document.getElementById('announcement-details-area').innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';

    // Fetch the details
    fetch('fetch_announcement_details.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('announcement-details-area').innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Could not load details.');
        });
}
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="../BarangaySystem/customcss/regiformstyle.css" rel="stylesheet" type="text/css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<?php include('dashboard_sidebar_end.php'); ?>

