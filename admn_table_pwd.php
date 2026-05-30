<?php
   error_reporting(E_ALL ^ E_WARNING);
   ini_set('display_errors', 0);
define('BMIS_ROLE_REQUIRED', 'admin');
require('secure_header.php');
   require('classes/resident.class.php');
   require('classes/conn.php');
   $userdetails = $bmis->get_userdata();
   $bmis->validate_admin();
   $pwd_count = $residentbmis->count_pwd();
?>

<?php include('dashboard_sidebar_start.php'); ?>

<style>
    .input-icons i { position: absolute; }
    .input-icons { width: 30%; margin-bottom: 10px; margin-left: 34%; }
    .icon { padding: 10px; min-width: 40px; }
    .form-control { text-align: center; }
    .pwd-badge {
        background-color: #6f42c1;
        color: #fff;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12 text-center">
            <h1>
                Barangay PWD Residents Table
            </h1>

        </div>
    </div>

    <hr><br>

    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="">
                <div class="input-icons">
                    <i class="fa fa-search icon"></i>
                    <input type="search" class="form-control" name="keyword" value="<?= isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : '' ?>" style="border-radius:30px;"/>
                </div>
                <button class="btn btn-success" style="width: 90px; font-size: 18px; border-radius:30px; margin-left:41.5%;" name="search_pwd">Search</button>
                <a href="admn_table_pwd.php" style="width: 90px; font-size: 18px; border-radius:30px;" class="btn btn-info">Reload</a>
            </form>
            <br><br>
        </div>
    </div>

    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="table-responsive">
            <table class="table table-hover text-center table-bordered" style="min-width: 1000px;">
                <thead style="background-color:#6f42c1; color:#fff;">
                    <tr>
                        <th>#</th>
                        <th>Surname</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Sex</th>
                        <th>Civil Status</th>
                        <th>House No.</th>
                        <th>Street</th>
                        <th>Barangay</th>
                        <th>Municipality</th>
                        <th>Contact #</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if (isset($_POST['search_pwd'])) {
                        $keyword = $_POST['keyword'];
                        $stmnt = $conn->prepare("SELECT * FROM tbl_resident
                            WHERE pwd = 'Yes' AND is_archived = 0
                            AND (lname LIKE :kw OR fname LIKE :kw OR mi LIKE :kw
                            OR sex LIKE :kw OR status LIKE :kw OR houseno LIKE :kw
                            OR street LIKE :kw OR brgy LIKE :kw OR municipal LIKE :kw
                            OR contact LIKE :kw OR email LIKE :kw)");
                        $stmnt->execute([':kw' => "%$keyword%"]);
                        $rows = $stmnt->fetchAll();
                    } else {
                        $stmnt = $conn->prepare("SELECT * FROM tbl_resident WHERE pwd = 'Yes' AND is_archived = 0");
                        $stmnt->execute();
                        $rows = $stmnt->fetchAll();
                    }

                    if (count($rows) > 0) {
                        $i = 1;
                        foreach ($rows as $row) { ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['lname']) ?></td>
                                <td><?= htmlspecialchars($row['fname']) ?></td>
                                <td><?= htmlspecialchars($row['mi']) ?></td>
                                <td><?= htmlspecialchars($row['sex']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['houseno']) ?></td>
                                <td><?= htmlspecialchars($row['street']) ?></td>
                                <td><?= htmlspecialchars($row['brgy']) ?></td>
                                <td><?= htmlspecialchars($row['municipal']) ?></td>
                                <td><?= htmlspecialchars($row['contact']) ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr><td colspan="11" class="text-muted py-3">No PWD residents found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js" integrity="sha512-/HL24m2nmyI2+ccX+dSHphAHqLw60Oj5sK8jf59VWtFWZi9vx7jzoxbZmcBeeTeCUc7z1mTs3LfyXGuBU32t+w==" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width, initial-scale=1 shrink-to-fit=no">
<link href="../BarangaySystem-master/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('dashboard_sidebar_end.php'); ?>