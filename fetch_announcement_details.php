<?php

    require('classes/resident.class.php');
    // Ensure $bmis is initialized if not done automatically in the require
    // $bmis = new BMISClass(); 

    $id = $_GET['id'];
    $details = $bmis->get_single_announcement($id);
    $reactions = $bmis->get_reactions($id);
    $comments = $bmis->get_comments($id);

    // Calculate total reactions
    $total_r = 0;
    if(is_array($reactions)) {
        foreach($reactions as $r) $total_r += $r['count'];
    }
?>

<?php if($details): ?>
    <div class="card announcement-preview shadow-lg">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="fw-bold text-dark mb-1">📢 <?= $details['addedby']; ?></h4>
                    <small class="text-muted">Posted on <?= $details['start_date']; ?></small>
                </div>
                <span class="badge bg-success">Selected Post</span>
            </div>
            
            <p class="lead mt-3 text-secondary" style="font-size: 1.1rem;">
                <?= nl2br(htmlspecialchars($details['event'])); ?>
            </p>

            <hr>

            <div class="row text-center mb-4">
                <div class="col-6 border-end">
                    <h3 class="fw-bold text-primary mb-0"><?= $total_r; ?></h3>
                    <small class="text-muted text-uppercase">Reactions</small>
                </div>
                <div class="col-6">
                    <h3 class="fw-bold text-success mb-0"><?= is_array($comments) ? count($comments) : 0; ?></h3>
                    <small class="text-muted text-uppercase">Comments</small>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Recent Comments:</h6>
            <div class="comment-list" style="max-height: 300px; overflow-y: auto;">
                <?php if(is_array($comments) && !empty($comments)): ?>
                    <?php foreach($comments as $com): ?>
                        <div class="d-flex mb-3 p-2 bg-light rounded">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold" style="font-size: 0.9rem;"><?= $com['full_name']; ?></span>
                                    <small class="text-muted" style="font-size: 0.7rem;"><?= $com['created_at']; ?></small>
                                </div>
                                <div class="text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($com['comment_text']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted my-4">No comments for this announcement.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">Announcement details not found.</div>
<?php endif; ?>