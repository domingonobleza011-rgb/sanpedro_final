<?php
   error_reporting(E_ALL ^ E_WARNING);
   ini_set('display_errors', 1);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
   require('classes/resident.class.php');
   require('classes/conn.php');
   $userdetails = $bmis->get_userdata();
   $bmis->validate_staff_or_admin();
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
                    </tr>
                </thead>
                <tbody>
                <?php
                    require_once 'pagination_helper.php';
                    $page    = max(1, (int)($_GET['page'] ?? 1));
                    $perPage = 10;
                    $pwdWhere = "(pwd = 'Yes' OR pwd = '1' OR pwd = 1) AND is_archived = 0";

                    if (isset($_POST['search_pwd'])) {
                        $keyword = $_POST['keyword'];
                        $kw = "%$keyword%";
                        $searchExtra = " AND (lname LIKE :kw OR fname LIKE :kw OR mi LIKE :kw OR sex LIKE :kw OR status LIKE :kw OR houseno LIKE :kw OR street LIKE :kw OR brgy LIKE :kw OR municipal LIKE :kw OR contact LIKE :kw OR email LIKE :kw)";
                        $countStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_resident WHERE $pwdWhere $searchExtra");
                        $countStmt->execute([':kw' => $kw]);
                        $total = (int)$countStmt->fetchColumn();
                        $offset = ($page - 1) * $perPage;
                        $stmnt = $conn->prepare("SELECT * FROM tbl_resident WHERE $pwdWhere $searchExtra LIMIT $perPage OFFSET $offset");
                        $stmnt->execute([':kw' => $kw]);
                        $rows = $stmnt->fetchAll();
                    } else {
                        $countStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_resident WHERE $pwdWhere");
                        $countStmt->execute();
                        $total = (int)$countStmt->fetchColumn();
                        $offset = ($page - 1) * $perPage;
                        $stmnt = $conn->prepare("SELECT * FROM tbl_resident WHERE $pwdWhere LIMIT $perPage OFFSET $offset");
                        $stmnt->execute();
                        $rows = $stmnt->fetchAll();
                    }
                    $paged = ['rows' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage, 'last_page' => (int)ceil($total / $perPage)];

                    if (count($rows) > 0) {
                        $i = ($page - 1) * $perPage + 1;
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
                                
                            </tr>
                        <?php }
                    } else { ?>
                        <tr><td colspan="11" class="text-muted py-3">No PWD residents found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php render_pagination($paged); ?>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>

</div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

<?php include('dashboard_sidebar_end.php'); ?>