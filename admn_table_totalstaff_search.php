<?php
require 'classes/conn.php';
require_once 'pagination_helper.php';

if (isset($_POST['search_totalstaff'])) {
    $keyword = $_POST['keyword'];
    $kw      = "%$keyword%";
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;

    $countStmt = $conn->prepare("SELECT COUNT(*) FROM `tbl_user` WHERE (`lname` LIKE :kw OR `mi` LIKE :kw OR `fname` LIKE :kw OR `age` LIKE :kw OR `sex` LIKE :kw OR `address` LIKE :kw OR `contact` LIKE :kw OR `position` LIKE :kw OR `role` LIKE :kw OR `email` LIKE :kw)");
    $countStmt->execute([':kw' => $kw]);
    $total = (int)$countStmt->fetchColumn();

    $offset = ($page - 1) * $perPage;
    $stmnt = $conn->prepare("SELECT * FROM `tbl_user` WHERE (`lname` LIKE :kw OR `mi` LIKE :kw OR `fname` LIKE :kw OR `age` LIKE :kw OR `sex` LIKE :kw OR `address` LIKE :kw OR `contact` LIKE :kw OR `position` LIKE :kw OR `role` LIKE :kw OR `email` LIKE :kw) LIMIT $perPage OFFSET $offset");
    $stmnt->execute([':kw' => $kw]);
    $rows = $stmnt->fetchAll();

    $paged = ['rows' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage, 'last_page' => (int)ceil($total / $perPage)];
?>
<table class="table table-hover text-center table-bordered" style="min-width:1000px;">
    <thead class="alert-info"><tr><th>Surname</th><th>First name</th><th>Middle name</th><th>Age</th><th>Sex</th><th>Address</th><th>Contact</th><th>Position</th><th>Role</th></tr></thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['lname']) ?></td>
            <td><?= htmlspecialchars($row['fname']) ?></td>
            <td><?= htmlspecialchars($row['mi']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['sex']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['position']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="9" class="text-muted py-3">No records found.</td></tr><?php endif; ?>
    </tbody>
</table>
<?php render_pagination($paged); ?>

<?php } else {
    $rows  = $view['rows']  ?? [];
    $paged = $view;
?>
<table class="table table-hover text-center table-bordered" style="min-width:1000px;">
    <thead class="alert-info"><tr><th>Surname</th><th>First name</th><th>Middle name</th><th>Age</th><th>Sex</th><th>Address</th><th>Contact</th><th>Position</th><th>Role</th></tr></thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['lname']) ?></td>
            <td><?= htmlspecialchars($row['fname']) ?></td>
            <td><?= htmlspecialchars($row['mi']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['sex']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['position']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="9" class="text-muted py-3">No records found.</td></tr><?php endif; ?>
    </tbody>
</table>
<?php render_pagination($paged); ?>

<?php } $conn = null; ?>