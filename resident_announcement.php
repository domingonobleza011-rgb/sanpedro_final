<?php 
    error_reporting(E_ALL ^ E_WARNING);
    include('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();

    // Check if resident is verified
    $is_verified = $bmis->isResidentVerified($userdetails['id_resident']);

    $dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $tm = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $cdate = $dt->format('Y/m/d');
    $ctime = $tm->format('H');

?>
<?php
    // 1. Get the current user ID
    $current_user_id = $userdetails['id_resident'];

    // 2. Pass the ID to the delete function
    // This calls your hide_announcement logic internally
    if(isset($_POST['delete_announcement'])) {
        $bmis->delete_announcement($current_user_id);
    }

    // 3. Fetch data filtered by the current user's "hidden" list
    $view = $bmis->view_active_announcements($current_user_id); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Announcements – Barangay San Pedro Iriga</title>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- responsive tags for screen compatibility -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- custom css --> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { margin: 0; padding: 0; background-color: #f0f2f5; }
        .page-wrap{max-width:680px;margin:28px auto 60px;padding:0 12px}
        .page-title{font-size:1.35rem;font-weight:700;color:#1c1e21;margin-bottom:18px;display:flex;align-items:center;gap:10px}
        .post-count-badge{background:#e7f3ff;color:#1877f2;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.5px;text-transform:uppercase}
        /* Card */
        .fb-card{background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.12);margin-bottom:16px;overflow:hidden}
        .fb-card-header{display:flex;align-items:center;padding:14px 16px 8px}
        .fb-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#1877f2,#0a5ecf);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.15rem;flex-shrink:0}
        .fb-meta{margin-left:10px}
        .fb-page-name{font-size:.95rem;font-weight:700;color:#1c1e21;line-height:1.2}
        .fb-post-date{font-size:.78rem;color:#65676b;display:flex;align-items:center;gap:5px;margin-top:2px}
        .fb-official-badge{background:#e7f3ff;color:#1877f2;font-size:.68rem;font-weight:700;padding:1px 7px;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
        .fb-card-body{padding:2px 16px 10px}
        .fb-post-text{font-size:.97rem;color:#1c1e21;line-height:1.6;margin:0;white-space:pre-line;word-break:break-word}
        .fb-post-text.large{font-size:1.25rem;font-weight:500}
        .fb-post-img{width:100%;max-height:520px;object-fit:cover;display:block;cursor:zoom-in}
        /* Reaction summary */
        .reaction-summary{padding:6px 16px;display:flex;align-items:center;justify-content:space-between;font-size:.85rem;color:#65676b;border-bottom:1px solid #e4e6ea;min-height:34px}
        .reaction-bubbles{display:flex;align-items:center}
        .reaction-emoji-bubble{width:22px;height:22px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.82rem;border:2px solid #fff;margin-left:-4px;box-shadow:0 1px 2px rgba(0,0,0,.15)}
        .reaction-emoji-bubble:first-child{margin-left:0}
        .reaction-total{margin-left:6px;cursor:default}
        .comment-count-link{cursor:pointer}
        .comment-count-link:hover{text-decoration:underline}
        /* Action bar */
        .fb-react-btn {
    transition: color 0.1s ease-in-out, background 0.2s;
}
        .fb-card-footer{border-top:1px solid #e4e6ea;padding:4px 12px;display:flex;gap:2px}
        .fb-react-btn{flex:1;background:none;border:none;color:#65676b;font-size:.88rem;font-weight:600;padding:8px 4px;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition: color 0.1s ease-in-out, background 0.2s;position:relative;user-select:none}
        .fb-react-btn:hover{background:#f0f2f5;color:#1c1e21}
        .fb-react-btn.reacted-like{color:#1877f2}
        .fb-react-btn.reacted-love{color:#f33e58}
        .fb-react-btn.reacted-haha,.fb-react-btn.reacted-wow,.fb-react-btn.reacted-sad{color:#f7b928}
        .fb-react-btn.reacted-angry{color:#e9710f}
        /* Reaction picker - click based */
        .reaction-picker{display:none;position:absolute;bottom:calc(100% + 8px);left:0;background:#fff;border-radius:30px;box-shadow:0 2px 12px rgba(0,0,0,.2);padding:6px 10px;gap:4px;flex-direction:row;z-index:200;white-space:nowrap}
        .reaction-picker.open{display:flex}
        .reaction-option{font-size:1.5rem;cursor:pointer;border-radius:50%;padding:4px;transition:transform .15s;display:inline-flex;align-items:center;justify-content:center;position:relative}
        .reaction-option:hover{transform:scale(1.35) translateY(-4px)}
        .reaction-option .tip{position:absolute;bottom:calc(100% + 4px);left:50%;transform:translateX(-50%);background:rgba(0,0,0,.75);color:#fff;font-size:.65rem;padding:2px 6px;border-radius:4px;white-space:nowrap;pointer-events:none;opacity:0;transition:opacity .1s}
        .reaction-option:hover .tip{opacity:1}
        /* Comments */
        .comments-section{padding:8px 16px 12px;background:#fff}
        .comment-input-row{display:flex;gap:10px;align-items:flex-start;margin-bottom:10px}
        .comment-user-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#42a5f5,#1565c0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;flex-shrink:0;font-weight:700}
        .comment-input-wrap{flex:1;position:relative}
        .comment-input{width:100%;background:#f0f2f5;border:none;border-radius:20px;padding:9px 40px 9px 16px;font-size:.9rem;outline:none;resize:none;line-height:1.4;max-height:120px;overflow-y:auto;font-family:inherit}
        .comment-input:focus{background:#e4e6eb}
        .comment-send-btn{position:absolute;right:10px;bottom:8px;background:none;border:none;color:#1877f2;cursor:pointer;font-size:1.1rem;padding:0;display:none}
        .comment-send-btn.visible{display:block}
        .comment-item{display:flex;gap:8px;margin-bottom:8px;align-items:flex-start}
        .comment-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#66bb6a,#2e7d32);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;flex-shrink:0;font-weight:700}
        .comment-bubble{background:#f0f2f5;border-radius:16px;padding:8px 12px;max-width:calc(100% - 50px)}
        .comment-author{font-size:.83rem;font-weight:700;color:#1c1e21}
        .comment-text{font-size:.88rem;color:#1c1e21;word-break:break-word}
        .comment-time{font-size:.72rem;color:#65676b;margin-top:3px;padding-left:4px}
        .comment-delete-btn{background:none;border:none;color:#65676b;cursor:pointer;font-size:.75rem;padding:0 4px;opacity:0;transition:opacity .15s}
        .comment-item:hover .comment-delete-btn{opacity:1}
        /* Bubble backgrounds */
        .bg-like{background:#1877f2}.bg-love{background:#f33e58}.bg-haha{background:#f7b928}.bg-wow{background:#f7b928}.bg-sad{background:#f7b928}.bg-angry{background:#e9710f}
        /* Empty */
        .empty-state{text-align:center;padding:48px 20px;background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.1);color:#65676b}
        .empty-state i{font-size:3rem;color:#bcc0c4;display:block;margin-bottom:12px}
.img-container {
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    border-top: 1px solid #ebedf0;
    border-bottom: 1px solid #ebedf0;
}

.fb-post-img {
    max-width: 100%;
    max-height: 500px; /* Limits height so it doesn't break the feed scroll */
    width: auto;       /* Allows the image to keep its natural aspect ratio */
    height: auto;
    display: block;
    cursor: zoom-in;
    transition: opacity 0.2s;
}

.fb-post-img:hover {
    opacity: 0.95;
}
.fb-card-header .btn-link {
    text-decoration: none;
    font-size: 1.2rem;
    line-height: 1;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.fb-card-header .btn-link:hover {
    background-color: #f2f2f2;
}

.dropdown-item {
    font-size: 0.9rem;
    font-weight: 500;
}
        .mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 65px;
    background-color: #ffffff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1050;
    border-top: 1px solid #dee2e6;
}

.mobile-bottom-nav .nav-item {
    text-decoration: none;
    color: #6c757d;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 0.7rem; /* Small text for mobile */
    font-weight: 500;
}

.mobile-bottom-nav .nav-item i {
    font-size: 1.4rem; /* Larger icons for easy tapping */
    margin-bottom: 2px;
}

.mobile-bottom-nav .nav-item:active {
    color: #0d6efd;
}

/* Add padding to the bottom of the body so content isn't hidden by the nav */
@media (max-width: 767px) {
    body {
        padding-bottom: 80px;
    }
}
    </style>
</head>
<body>
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background: rgba(0,0,0,0.9); border: none; position: relative;">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height: 80vh;">
                <button class="btn text-black position-absolute start-0 ms-3" id="prevBtn" onclick="changeImage(-1)" style="font-size: 3rem; z-index: 10;">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <img src="" id="modalImg" style="width: 100%; max-height: 90vh; object-fit: contain;" alt="Zoomed view">

                <button class="btn text-black position-absolute end-0 me-3" id="nextBtn" onclick="changeImage(1)" style="font-size: 3rem; z-index: 10;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
 <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-md-block shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="resident_homepage.php">
            <i class="bi bi-building-fill me-2"></i> Barangay San Pedro
        </a>
        <div class="d-flex ms-auto">
            <a href="resident_homepage.php" class="btn btn-primary me-1"><i class="bi bi-house-door-fill me-1"></i> Home</a>
            <a href="resident_announcement.php" class="btn btn-primary me-1"><i class="bi bi-megaphone-fill me-1"></i> Announcements</a>
            <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-person-badge me-1"></i> Profile</a>
            <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-shield-lock me-1"></i> Password</a>
            <a href="logout.php" class="btn btn-danger ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- MOBILE BOTTOM NAV (Hidden on Desktop) -->
<div class="mobile-bottom-nav d-md-none">
    <a href="resident_homepage.php" class="nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>
    <a href="resident_announcement.php" class="nav-item">
        <i class="bi bi-megaphone-fill"></i>
        <span>News</span>
    </a>
    <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-person-badge"></i>
        <span>Profile</span>
    </a>
    <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-shield-lock"></i>
        <span>Pass</span>
    </a>
    <a href="logout.php" class="nav-item text-danger">
        <i class="bi bi-box-arrow-right"></i>
        <span>Exit</span>
    </a>
</div>

<div class="page-wrap">
    <div class="page-title">
        <i class="bi bi-megaphone-fill text-primary"></i>
        Announcements
        <?php if(is_array($view) && count($view) > 0): ?>
            <span class="post-count-badge"><?= count($view); ?> post<?= count($view) > 1 ? 's' : ''; ?></span>
        <?php endif; ?>
    </div>

<?php 
$emojiMap = ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢','angry'=>'😡'];
if(is_array($view) && count($view) > 0):
    foreach($view as $ann):
        $ann_id    = $ann['id_announcement'];
        $hasImg    = !empty($ann['image']);
        $isShort   = strlen($ann['event']) < 130 && !$hasImg;
        $reactions = $bmis->get_reactions($ann_id);
        $userReact = $bmis->get_user_reaction($ann_id, $current_user_id);
        $comments  = $bmis->get_comments($ann_id);
        $totalReact = array_sum(array_column($reactions, 'count'));
        usort($reactions, fn($a,$b) => $b['count'] - $a['count']);
        $topReactions = array_slice($reactions, 0, 1);
        $reactionLabel = $userReact ? ucfirst($userReact) : 'Like';
        $reactionIcon  = $userReact ? $emojiMap[$userReact] : '👍';
        $initials = strtoupper(substr($userdetails['firstname'],0,1) . substr($userdetails['surname'],0,1));
?>
   <div class="fb-card">
        <div class="fb-card-header">
            <div class="fb-avatar"><i class="bi bi-building-fill"></i></div>
            <div class="fb-meta">
                <div class="fb-page-name">Barangay San Pedro Iriga</div>
                <div class="fb-post-date"><?= date('F j, Y', strtotime($ann['start_date'])); ?> · Official</div>
            </div>
        </div>
        <?php if(!empty($ann['event'])): ?>
        <div class="fb-card-body">
            <p class="fb-post-text"><?= nl2br(htmlspecialchars($ann['event'])); ?></p>
        </div>
        <?php endif; ?>
        <?php 
        if($hasImg): 
            $images = explode(',', $ann['image']);
            $count = count($images);
        ?>
        <div class="fb-image-gallery d-flex flex-wrap bg-light p-2 gap-2">
    <?php 
    $images = explode(',', $ann['image']);
    $images_json = json_encode($images); // Prepare the array for JS
    foreach($images as $index => $img): 
        $img = trim($img);
        if(empty($img)) continue;
    ?>
        <img src="uploads/<?= htmlspecialchars($img); ?>" 
             class="fb-gallery-item" 
             style="width: <?= count($images) > 1 ? 'calc(50% - 4px)' : '100%'; ?>; aspect-ratio: 1/1; object-fit: cover; border-radius: 8px; cursor: pointer;"
             onclick='openGallery(<?= $images_json; ?>, <?= $index; ?>)'>
    <?php endforeach; ?>
</div>
        <?php endif; ?>
        <!-- Reaction Summary -->
        <div class="reaction-summary">
            <div class="d-flex align-items-center">
                <div class="reaction-bubbles" id="bubbles-<?= $ann_id; ?>">
                    <?php foreach($topReactions as $r): if((int)$r['count'] < 1) continue; ?>
                    <div class="reaction-emoji-bubble bg-<?= $r['reaction_type']; ?>" title="<?= ucfirst($r['reaction_type']); ?>"><?= $emojiMap[$r['reaction_type']]; ?></div>
                    <?php endforeach; ?>
                </div>
                <span id="react-total-<?= $ann_id; ?>"><?= $totalReact > 0 ? $totalReact : ''; ?></span>
            </div>
            <span class="comment-count-link" onclick="toggleComments(<?= $ann_id; ?>)" id="cmt-count-<?= $ann_id; ?>">
                <?= count($comments) > 0 ? count($comments) . ' comment' . (count($comments)>1?'s':'') : ''; ?>
            </span>
        </div>
        <!-- Action Bar -->
        <div class="fb-card-footer">
    <button class="fb-react-btn <?= $userReact ? 'reacted-'.$userReact : ''; ?>"
            id="like-btn-<?= $ann_id; ?>"
            data-ann="<?= $ann_id; ?>"
            data-current="<?= $userReact ?: ''; ?>"
            onclick="quickLike(event, <?= $ann_id; ?>)"
            onmousedown="startHold(<?= $ann_id; ?>)"
            onmouseup="cancelHold()"
            onmouseleave="cancelHold()"
            ontouchstart="startHold(<?= $ann_id; ?>)"
            ontouchend="cancelHold()">

        <div class="reaction-picker" id="picker-<?= $ann_id; ?>">
            <?php foreach($emojiMap as $type => $emoji): ?>
            <span class="reaction-option" 
                  onclick="pickReaction(event, <?= $ann_id; ?>,'<?= $type; ?>')"><?= $emoji; ?><span class="tip"><?= ucfirst($type); ?></span></span>
            <?php endforeach; ?>
        </div>

        <span id="like-icon-<?= $ann_id; ?>"><?= $reactionIcon; ?></span>
        <span id="like-label-<?= $ann_id; ?>"><?= $reactionLabel; ?></span>
    </button>

    <button class="fb-react-btn" onclick="toggleComments(<?= $ann_id; ?>)">
        <i class="bi bi-chat"></i> Comment
    </button>
     <form method="POST" onsubmit="return confirm('Delete this post?');" style="display:inline;">
    <input type="hidden" name="id_announcement" value="<?= $ann['id_announcement']; ?>">
    <button type="submit" name="delete_announcement" class="fb-react-btn text-danger">
        <i class="bi bi-trash3"></i> Delete
    </button>
</form>
</div>
        <!-- Comments -->
        <div class="comments-section" id="comments-<?= $ann_id; ?>" style="display:none;">
            <div id="comment-list-<?= $ann_id; ?>">
                <?php foreach($comments as $c):
                    $cInit = strtoupper(substr($c['full_name'],0,1) . (strpos($c['full_name'],' ')!==false ? substr($c['full_name'],strpos($c['full_name'],' ')+1,1) : ''));
                    $isOwn = ((int)$c['user_id'] === (int)$current_user_id);
                ?>
                <div class="comment-item" id="comment-item-<?= $c['id_comment']; ?>">
                    <div class="comment-avatar"><?= htmlspecialchars($cInit); ?></div>
                    <div style="flex:1;">
                        <div class="comment-bubble">
                            <div class="comment-author"><?= htmlspecialchars($c['full_name']); ?></div>
                            <div class="comment-text"><?= nl2br(htmlspecialchars($c['comment_text'])); ?></div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="comment-time"><?= date('M j, Y g:i A', strtotime($c['created_at'])); ?></span>
                            <?php if($isOwn): ?>
                            <button class="comment-delete-btn" onclick="deleteComment(<?= $c['id_comment']; ?>,<?= $ann_id; ?>)"><i class="bi bi-trash3"></i> Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="comment-input-row mt-2">
                <div class="comment-user-avatar"><?= htmlspecialchars($initials); ?></div>
                <div class="comment-input-wrap">
                    <textarea class="comment-input" id="input-<?= $ann_id; ?>" rows="1"
                              placeholder="Write a comment…"
                              oninput="onCommentInput(this,<?= $ann_id; ?>)"
                              onkeydown="commentEnter(event,<?= $ann_id; ?>)"></textarea>
                    <button class="comment-send-btn" id="send-btn-<?= $ann_id; ?>" onclick="submitComment(<?= $ann_id; ?>)">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;
else: ?>
    <div class="empty-state">
        <i class="bi bi-megaphone"></i>
        <p class="fw-semibold mb-1">No announcements yet.</p>
        <small>Check back later for updates from Barangay San Pedro.</small>
    </div>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentImages = [];
let currentIndex = 0;

function openGallery(images, index) {
    currentImages = images;
    currentIndex = index;
    updateModalImage();
    
    const myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
}

function changeImage(direction) {
    currentIndex += direction;

    // Loop back to start or end
    if (currentIndex >= currentImages.length) {
        currentIndex = 0;
    } else if (currentIndex < 0) {
        currentIndex = currentImages.length - 1;
    }

    updateModalImage();
}

function updateModalImage() {
    const modalImg = document.getElementById('modalImg');
    const path = 'uploads/' + currentImages[currentIndex].trim();
    modalImg.src = path;

    // Optional: Hide buttons if there is only 1 image
    const display = currentImages.length > 1 ? 'block' : 'none';
    document.getElementById('prevBtn').style.display = display;
    document.getElementById('nextBtn').style.display = display;
}

// Optional: Add keyboard support (Left/Right arrows)
document.addEventListener('keydown', function(e) {
    if (document.getElementById('imageModal').classList.contains('show')) {
        if (e.key === "ArrowLeft") changeImage(-1);
        if (e.key === "ArrowRight") changeImage(1);
    }
});
    function openImageModal(imgSrc) {
    const modalImg = document.getElementById('modalImg');
    modalImg.src = imgSrc;
    const myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
}

const AJAX = 'announcement_ajax.php';
const EM   = {like:'👍',love:'❤️',haha:'😂',wow:'😮',sad:'😢',angry:'😡'};

// --- Reaction Picker: hold to open ---
let holdTimer = null;
let pickerOpen = null;

function startHold(id) {
    holdTimer = setTimeout(() => {
        openPicker(id);
    }, 400); // hold 400ms to open picker
}

function cancelHold() {
    clearTimeout(holdTimer);
    holdTimer = null;
}

function openPicker(id) {
    // Close any open picker first
    if (pickerOpen && pickerOpen !== id) {
        document.getElementById('picker-' + pickerOpen)?.classList.remove('open');
    }
    const picker = document.getElementById('picker-' + id);
    picker.classList.toggle('open');
    pickerOpen = picker.classList.contains('open') ? id : null;
}

// Close picker when clicking outside
document.addEventListener('click', function(e) {
    if (pickerOpen && !e.target.closest('.fb-react-btn')) {
        document.getElementById('picker-' + pickerOpen)?.classList.remove('open');
        pickerOpen = null;
    }
});

function pickReaction(e, annId, type) {
    e.stopPropagation();
    // Close the picker
    document.getElementById('picker-' + annId)?.classList.remove('open');
    pickerOpen = null;
    react(annId, type);
}

function toggleComments(id){
    const el=document.getElementById('comments-'+id);
    const show=el.style.display==='none';
    el.style.display=show?'block':'none';
    if(show) document.getElementById('input-'+id).focus();
}

function quickLike(e,id){
    // Don't fire if clicking a reaction option or if picker is open
    if(e.target.closest('.reaction-option') || e.target.closest('.reaction-picker')) return;
    if(pickerOpen === id) {
        document.getElementById('picker-' + id)?.classList.remove('open');
        pickerOpen = null;
        return;
    }
    const btn=document.getElementById('like-btn-'+id);
    const cur=btn.dataset.current||'like';
    react(id,cur);
}

function react(annId, type) {
    const btn = document.getElementById('like-btn-' + annId);
    const label = document.getElementById('like-label-' + annId);
    const icon = document.getElementById('like-icon-' + annId);
    const totalEl = document.getElementById('react-total-' + annId);
    
    // 1. OPTIMISTIC UPDATE: Change the UI immediately
    const isRemoving = btn.classList.contains('reacted-' + type);
    const prevClass = btn.className;
    const prevLabel = label.textContent;
    const prevIcon = icon.textContent;
    const prevTotal = parseInt(totalEl.textContent || 0);

    if (isRemoving) {
        // User is clicking the same reaction to remove it
        btn.className = 'fb-react-btn';
        label.textContent = 'Like';
        icon.textContent = '👍';
        totalEl.textContent = (prevTotal - 1) > 0 ? (prevTotal - 1) : '';
        btn.dataset.current = '';
    } else {
        // User is adding or changing a reaction
        const wasReacted = btn.dataset.current !== "";
        btn.className = 'fb-react-btn reacted-' + type;
        label.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        icon.textContent = EM[type];
        
        // If they hadn't reacted before, increase count. If they are just changing types, keep count same.
        if (!wasReacted) {
            totalEl.textContent = prevTotal + 1;
        }
        btn.dataset.current = type;
    }

    // 2. BACKGROUND REQUEST: Send to InfinityFree
    fetch(AJAX, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=toggle_reaction&announcement_id=${annId}&reaction_type=${type}`
    })
    .then(r => r.json())
    .then(d => {
        if (!d.success) {
            // Rollback if server had an error
            btn.className = prevClass;
            label.textContent = prevLabel;
            icon.textContent = prevIcon;
            totalEl.textContent = prevTotal > 0 ? prevTotal : '';
            return;
        }

        // 3. SILENT UPDATE: Update the "bubbles" and final total correctly once server responds
        const total = d.counts.reduce((s, r) => s + parseInt(r.count), 0);
        totalEl.textContent = total > 0 ? total : '';
        
        const sorted = [...d.counts].sort((a, b) => b.count - a.count).slice(0, 3);
        document.getElementById('bubbles-' + annId).innerHTML = sorted
            .filter(r => r.count > 0)
            .map(r => `<div class="reaction-emoji-bubble bg-${r.reaction_type}" title="${r.reaction_type}">${EM[r.reaction_type]}</div>`)
            .join('');
            
        // Final sync of state just in case
        btn.dataset.current = d.user_reaction || '';
    })
    .catch(err => {
        // Rollback on connection failure
        btn.className = prevClass;
        label.textContent = prevLabel;
        icon.textContent = prevIcon;
        totalEl.textContent = prevTotal > 0 ? prevTotal : '';
    });
}
function onCommentInput(ta,id){
    ta.style.height='auto';
    ta.style.height=Math.min(ta.scrollHeight,120)+'px';
    document.getElementById('send-btn-'+id).classList.toggle('visible',ta.value.trim().length>0);
}

function commentEnter(e,id){
    if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();submitComment(id);}
}

function submitComment(id){
    const inp=document.getElementById('input-'+id);
    const txt=inp.value.trim();
    if(!txt)return;
    fetch(AJAX,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=add_comment&announcement_id=${id}&comment_text=${encodeURIComponent(txt)}`})
    .then(r=>r.json()).then(d=>{
        if(!d.success)return;
        const colors=['#ab47bc,#6a1b9a','#ef5350,#b71c1c','#26a69a,#00695c','#5c6bc0,#283593'];
        const col=colors[Math.floor(Math.random()*colors.length)];
        const init=d.full_name.split(' ').map(w=>w[0]||'').join('').substring(0,2).toUpperCase();
        document.getElementById('comment-list-'+id).insertAdjacentHTML('beforeend',`
        <div class="comment-item" id="comment-item-${d.id_comment}">
            <div class="comment-avatar" style="background:linear-gradient(135deg,${col})">${esc(init)}</div>
            <div style="flex:1">
                <div class="comment-bubble">
                    <div class="comment-author">${esc(d.full_name)}</div>
                    <div class="comment-text">${esc(d.comment_text).replace(/\n/g,'<br>')}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="comment-time">${d.created_at}</span>
                    <button class="comment-delete-btn" onclick="deleteComment(${d.id_comment},${id})"><i class="bi bi-trash3"></i> Delete</button>
                </div>
            </div>
        </div>`);
        const tot=document.getElementById('comment-list-'+id).querySelectorAll('.comment-item').length;
        document.getElementById('cmt-count-'+id).textContent=tot+' comment'+(tot>1?'s':'');
        inp.value='';inp.style.height='auto';
        document.getElementById('send-btn-'+id).classList.remove('visible');
    });
}

function deleteComment(cid,aid){
    if(!confirm('Delete your comment?'))return;
    fetch(AJAX,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=delete_comment&comment_id=${cid}`})
    .then(r=>r.json()).then(d=>{
        if(!d.success)return;
        document.getElementById('comment-item-'+cid)?.remove();
        const tot=document.getElementById('comment-list-'+aid).querySelectorAll('.comment-item').length;
        document.getElementById('cmt-count-'+aid).textContent=tot>0?tot+' comment'+(tot>1?'s':''):'';
    });
}

function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
</script>
</body>
</html>