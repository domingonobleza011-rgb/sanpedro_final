<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
requireLogin();

// Get filter parameters
$category = $_GET['category'] ?? null;
$status = $_GET['status'] ?? 'Registration Open';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT p.*, 
        COUNT(DISTINCT pr.id) as total_registrations,
        COUNT(DISTINCT CASE WHEN pr.user_id = :user_id THEN pr.id END) as user_registered
        FROM programs p
        LEFT JOIN program_registrations pr ON p.id = pr.program_id AND pr.status != 'Cancelled'
        WHERE 1=1";

$params = [':user_id' => $_SESSION['user_id']];

if ($category) {
    $sql .= " AND p.category = :category";
    $params[':category'] = $category;
}

if ($status) {
    $sql .= " AND p.status = :status";
    $params[':status'] = $status;
}

if ($search) {
    $sql .= " AND (p.title LIKE :search OR p.description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$sql .= " GROUP BY p.id ORDER BY p.start_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs & Activities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/resident_nav.php'; ?>
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="bi bi-calendar-event"></i> Programs & Activities</h2>
                <p class="text-muted">Browse and register for youth programs and activities</p>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchInput" placeholder="Search programs..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Sports" <?= $category === 'Sports' ? 'selected' : '' ?>>Sports</option>
                    <option value="Education" <?= $category === 'Education' ? 'selected' : '' ?>>Education</option>
                    <option value="Livelihood" <?= $category === 'Livelihood' ? 'selected' : '' ?>>Livelihood</option>
                    <option value="Health" <?= $category === 'Health' ? 'selected' : '' ?>>Health</option>
                    <option value="Environment" <?= $category === 'Environment' ? 'selected' : '' ?>>Environment</option>
                    <option value="Arts" <?= $category === 'Arts' ? 'selected' : '' ?>>Arts & Culture</option>
                    <option value="Community Service" <?= $category === 'Community Service' ? 'selected' : '' ?>>Community Service</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="Registration Open" <?= $status === 'Registration Open' ? 'selected' : '' ?>>Registration Open</option>
                    <option value="Published" <?= $status === 'Published' ? 'selected' : '' ?>>Published</option>
                    <option value="Ongoing" <?= $status === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" onclick="applyFilters()">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </div>
        
        <!-- Programs Grid -->
        <div class="row">
            <?php if (empty($programs)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No programs found matching your criteria.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($programs as $program): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($program['banner_image']): ?>
                                <img src="<?= htmlspecialchars($program['banner_image']) ?>" class="card-img-top" alt="Program banner" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-calendar-event text-white" style="font-size: 48px;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge bg-primary"><?= htmlspecialchars($program['category']) ?></span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($program['status']) ?></span>
                                    <?php if ($program['user_registered'] > 0): ?>
                                        <span class="badge bg-success">Registered</span>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title"><?= htmlspecialchars($program['title']) ?></h5>
                                <p class="card-text text-muted small">
                                    <?= substr(htmlspecialchars($program['description']), 0, 100) ?>...
                                </p>
                                
                                <ul class="list-unstyled small">
                                    <li><i class="bi bi-calendar"></i> <?= date('M d, Y', strtotime($program['start_date'])) ?></li>
                                    <li><i class="bi bi-clock"></i> <?= date('h:i A', strtotime($program['start_date'])) ?></li>
                                    <li><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($program['venue']) ?></li>
                                    <li><i class="bi bi-people"></i> 
                                        <?php if ($program['max_participants']): ?>
                                            <?= $program['total_registrations'] ?> / <?= $program['max_participants'] ?> participants
                                        <?php else: ?>
                                            <?= $program['total_registrations'] ?> participants
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="card-footer bg-white">
                                <a href="?page=program-detail&id=<?= $program['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                
                                <?php if ($program['status'] === 'Registration Open' && $program['user_registered'] == 0): ?>
                                    <button class="btn btn-sm btn-primary" onclick="registerForProgram(<?= $program['id'] ?>)">
                                        <i class="bi bi-check-circle"></i> Register
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            let url = '?page=programs';
            if (search) url += '&search=' + encodeURIComponent(search);
            if (category) url += '&category=' + encodeURIComponent(category);
            if (status) url += '&status=' + encodeURIComponent(status);
            
            window.location.href = url;
        }
        
        function registerForProgram(programId) {
            if (confirm('Do you want to register for this program?')) {
                fetch('api/programs/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ program_id: programId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Registration successful! Check your email for details.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during registration');
                });
            }
        }
        
        // Enter key to search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    </script>
</body>
</html>