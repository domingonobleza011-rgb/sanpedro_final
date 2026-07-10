<?php
require 'classes/conn.php';
require_once 'pagination_helper.php';

if (isset($_POST['search_totalres'])) {
    $keyword = $_POST['keyword'];
    $kw = "%$keyword%";
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;

    $countStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_resident WHERE
        lname LIKE :kw OR mi LIKE :kw OR fname LIKE :kw OR age LIKE :kw OR sex LIKE :kw OR
        status LIKE :kw OR houseno LIKE :kw OR contact LIKE :kw OR bdate LIKE :kw OR
        bplace LIKE :kw OR nationality LIKE :kw OR family_role LIKE :kw OR role LIKE :kw OR
        email LIKE :kw OR brgy LIKE :kw OR street LIKE :kw OR municipal LIKE :kw");
    $countStmt->execute([':kw' => $kw]);
    $total = (int)$countStmt->fetchColumn();

    $offset = ($page - 1) * $perPage;
    $stmnt = $conn->prepare("SELECT * FROM tbl_resident WHERE
        lname LIKE :kw OR mi LIKE :kw OR fname LIKE :kw OR age LIKE :kw OR sex LIKE :kw OR
        status LIKE :kw OR houseno LIKE :kw OR contact LIKE :kw OR bdate LIKE :kw OR
        bplace LIKE :kw OR nationality LIKE :kw OR family_role LIKE :kw OR role LIKE :kw OR
        email LIKE :kw OR brgy LIKE :kw OR street LIKE :kw OR municipal LIKE :kw
        LIMIT $perPage OFFSET $offset");
    $stmnt->execute([':kw' => $kw]);
    $rows = $stmnt->fetchAll();

    $paged = ['rows' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage, 'last_page' => (int)ceil($total / $perPage)];
?>
<table class="table table-hover text-center table-bordered" style="min-width:1000px;">
    <thead class="alert-info">
        <tr>
            <th>Fullname</th><th>Age</th><th>Sex</th><th>Status</th>
            <th>House No.</th><th>Street</th><th>Barangay</th><th>Municipality</th>
            <th>Contact #</th><th>Birth date</th><th>Birth place</th><th>Nationality</th><th>Family Head</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['lname']) ?>, <?= htmlspecialchars($row['fname']) ?> <?= htmlspecialchars($row['mi']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['sex']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['houseno']) ?></td>
            <td><?= htmlspecialchars($row['street']) ?></td>
            <td><?= htmlspecialchars($row['brgy']) ?></td>
            <td><?= htmlspecialchars($row['municipal']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['bdate']) ?></td>
            <td><?= htmlspecialchars($row['bplace']) ?></td>
            <td><?= htmlspecialchars($row['nationality']) ?></td>
            <td><?= htmlspecialchars($row['family_role']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="13" class="text-muted py-3">No records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php render_pagination($paged); ?>

<?php } else {
    // $view is the $paged array from view_resident_paginated()
    $rows  = $view['rows']  ?? [];
    $paged = $view;
?>
<table class="table table-hover text-center table-bordered" style="min-width:1000px;">
    <thead class="alert-info">
        <tr>
            <th>Fullname</th><th>Age</th><th>Sex</th><th>Status</th>
            <th>Complete Address</th><th>Contact #</th><th>Birth date</th>
            <th>Birth place</th><th>Nationality</th><th>Family Head</th><th>Registered Voter</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['lname']) ?>, <?= htmlspecialchars($row['fname']) ?> <?= htmlspecialchars($row['mi']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['sex']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['houseno']) ?>, <?= htmlspecialchars($row['street']) ?>, <?= htmlspecialchars($row['brgy']) ?>, <?= htmlspecialchars($row['municipal']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['bdate']) ?></td>
            <td><?= htmlspecialchars($row['bplace']) ?></td>
            <td><?= htmlspecialchars($row['nationality']) ?></td>
            <td><?= htmlspecialchars($row['family_role']) ?></td>
            <td><?= htmlspecialchars($row['voter']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="11" class="text-muted py-3">No residents found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<?php render_pagination($paged); ?>

<?php } $conn = null; ?>