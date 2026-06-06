<?php
/**
 * ajax_complaints.php
 * Returns the complaint cards HTML + counts as JSON.
 * Called by admn_complaints.php every 8 seconds via fetch().
 */
error_reporting(0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');

header('Content-Type: application/json');

$host   = 'localhost';
$dbname = 'bmis';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => true]);
    exit;
}

// Respect same filters passed as GET params
$filter_status   = $_GET['status']   ?? 'all';
$filter_category = $_GET['category'] ?? '';
$filter_search   = trim($_GET['search'] ?? '');

$where  = [];
$params = [];

if ($filter_status !== 'all') {
    $where[] = 'status = :status';
    $params[':status'] = $filter_status;
}
if ($filter_category !== '') {
    $where[] = 'category LIKE :cat';
    $params[':cat'] = '%' . $filter_category . '%';
}
if ($filter_search !== '') {
    $where[] = '(full_name LIKE :s OR description LIKE :s2 OR location LIKE :s3)';
    $params[':s']  = '%' . $filter_search . '%';
    $params[':s2'] = '%' . $filter_search . '%';
    $params[':s3'] = '%' . $filter_search . '%';
}

$sql = 'SELECT * FROM tbl_complaints';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY date_submitted DESC';

$count_all      = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints")->fetchColumn();
$count_pending  = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='pending'")->fetchColumn();
$count_resolved = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='resolved'")->fetchColumn();

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build HTML fragment
ob_start();
foreach ($complaints as $c):
    $is_pending = $c['status'] === 'pending';
    $card_class = $is_pending ? 'c-pending' : 'c-resolved';
    $modal_id   = 'modal-' . $c['id'];
?>
<div class="c-card <?= $card_class ?>">
    <div class="c-card-header">
        <div>
            <div class="c-id"># <?= $c['id'] ?> · <?= date('M d, Y · h:i A', strtotime($c['date_submitted'])) ?></div>
            <div class="c-name"><?= htmlspecialchars($c['full_name']) ?></div>
            <?php if ($c['contact_number']): ?>
            <div class="c-contact"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['contact_number']) ?></div>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($is_pending): ?>
            <span class="badge-pending"><i class="bi bi-clock-history me-1"></i>Pending</span>
            <?php else: ?>
            <span class="badge-resolved"><i class="bi bi-check-circle me-1"></i>Resolved</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="c-meta">
        <span><i class="bi bi-tag"></i> <span class="c-category-tag"><?= htmlspecialchars($c['category']) ?></span></span>
        <?php if ($c['location']): ?>
        <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($c['location']) ?></span>
        <?php endif; ?>
        <?php if ($c['address']): ?>
        <span><i class="bi bi-house"></i> <?= htmlspecialchars($c['address']) ?></span>
        <?php endif; ?>
    </div>

    <div class="c-description"><?= nl2br(htmlspecialchars($c['description'])) ?></div>

    <?php if ($c['photo_path'] && file_exists(__DIR__ . '/' . $c['photo_path'])): ?>
    <div class="c-photo">
        <img src="<?= htmlspecialchars($c['photo_path']) ?>"
             alt="Complaint photo"
             data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-photo">
        <small class="text-muted d-block mt-1"><i class="bi bi-image me-1"></i>Click photo to enlarge</small>
    </div>
    <?php endif; ?>

    <?php if ($c['admin_remarks']): ?>
    <div class="c-remarks">
        <strong><i class="bi bi-chat-left-text me-1"></i>Admin Remarks:</strong>
        <?= nl2br(htmlspecialchars($c['admin_remarks'])) ?>
    </div>
    <?php endif; ?>

    <div class="c-actions">
        <?php if ($is_pending): ?>
        <button class="btn-resolve" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-resolve">
            <i class="bi bi-check-circle me-1"></i> Resolve
        </button>
        <?php else: ?>
        <form method="POST" class="d-inline">
            <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
            <button type="submit" name="action_pending" class="btn-set-pending">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Set to Pending
            </button>
        </form>
        <?php endif; ?>
        <button class="btn-delete-card" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-delete">
            <i class="bi bi-trash3 me-1"></i> Delete
        </button>
    </div>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="<?= $modal_id ?>-resolve" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Resolve Complaint #<?= $c['id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <p class="mb-3">You are about to mark this complaint as <strong>Resolved</strong>.</p>
                    <p class="mb-2 fw-semibold" style="font-size:.85rem;">Complaint from: <span class="text-primary"><?= htmlspecialchars($c['full_name']) ?></span></p>
                    <p class="mb-3" style="font-size:.85rem;color:#666;"><?= htmlspecialchars(substr($c['description'], 0, 120)) ?><?= strlen($c['description']) > 120 ? '…' : '' ?></p>
                    <label class="form-label fw-semibold">Admin Remarks / Action Taken <span style="color:#aaa;font-weight:400">(optional)</span></label>
                    <textarea name="admin_remarks" class="form-control" rows="3"><?= htmlspecialchars($c['admin_remarks'] ?? '') ?></textarea>
                    <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="action_resolve" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> Mark as Resolved
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="<?= $modal_id ?>-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header" style="background:#e74c3c;">
                <h5 class="modal-title text-white"><i class="bi bi-trash3 me-2"></i>Delete Complaint</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:3rem;"></i>
                <h6 class="mt-3 mb-2">Are you sure?</h6>
                <p class="text-muted" style="font-size:.875rem;">
                    This will permanently delete complaint <strong>#<?= $c['id'] ?></strong>
                    from <strong><?= htmlspecialchars($c['full_name']) ?></strong>. This cannot be undone.
                </p>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                    <button type="submit" name="action_delete" class="btn btn-danger px-4">
                        <i class="bi bi-trash3 me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($c['photo_path'] && file_exists(__DIR__ . '/' . $c['photo_path'])): ?>
<div class="modal fade" id="<?= $modal_id ?>-photo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-image me-2"></i>Complaint Photo — #<?= $c['id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center" style="background:#111;">
                <img src="<?= htmlspecialchars($c['photo_path']) ?>"
                     style="max-width:100%; max-height:70vh; object-fit:contain;" alt="Complaint photo">
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endforeach; ?>
<?php
$html = ob_get_clean();

echo json_encode([
    'html'          => $html,
    'count_all'     => $count_all,
    'count_pending' => $count_pending,
    'count_resolved'=> $count_resolved,
    'total_shown'   => count($complaints),
]);
