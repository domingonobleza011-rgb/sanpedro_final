<?php
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php');
error_reporting(E_ALL ^ E_WARNING);
include('classes/resident.class.php');
$userdetails = $bmis->get_userdata();

$is_verified = $bmis->isResidentVerified($userdetails['id_resident']);

$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$tm = new DateTime("now", new DateTimeZone('Asia/Manila'));
$cdate = $dt->format('Y/m/d');
$ctime = $tm->format('H');

$current_user_id = $userdetails['id_resident'];

// Handle delete via normal POST (fallback) — redirects back cleanly
if(isset($_POST['delete_announcement'])) {
    $bmis->delete_announcement($current_user_id);
}

$view = $bmis->view_active_announcements($current_user_id);

/**
 * Escapes announcement text for safe HTML output, then turns any
 * http://, https://, or www. links inside it into clickable <a> tags
 * that open in a new tab. Apply nl2br() AFTER this, not before.
 */
function bmis_linkify_announcement($text) {
    $escaped = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');

    return preg_replace_callback(
        '/((https?:\/\/|www\.)[^\s<]+)/i',
        function ($m) {
            $url = $m[0];

            // Don't swallow trailing punctuation into the link (e.g. "visit site.com.")
            $trailing = '';
            while ($url !== '' && strpos('.,!?;:)"\'', substr($url, -1)) !== false) {
                $trailing = substr($url, -1) . $trailing;
                $url = substr($url, 0, -1);
            }

            $href = (stripos($url, 'http') === 0) ? $url : 'https://' . $url;

            return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer" '
                 . 'onclick="event.stopPropagation();" '
                 . 'style="color:#1877f2; font-weight:600; text-decoration:underline; word-break:break-all;">'
                 . $url . '</a>' . $trailing;
        },
        $escaped
    );
}

/**
 * Truncate text to a specified word count with "See more" functionality.
 * Returns array with truncated text, full text, and whether it was truncated.
 */
function bmis_truncate_text($text, $word_limit = 20) {
    $words = preg_split('/\s+/', $text);
    if (count($words) <= $word_limit) {
        return [
            'truncated' => false,
            'short' => $text,
            'full' => $text
        ];
    }
    
    $short = implode(' ', array_slice($words, 0, $word_limit));
    return [
        'truncated' => true,
        'short' => $short . '...',
        'full' => $text
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Announcements – Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* ----- GLOBAL RESETS ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            padding-bottom: 85px; /* space for mobile nav */
        }
        @media (min-width: 768px) {
            body { padding-bottom: 0; }
        }

        /* ----- PAGE WRAPPER ----- */
        .page-wrap {
            max-width: 680px;
            margin: 1.25rem auto 2rem;
            padding: 0 12px;
        }

        /* ----- PAGE HEADER ----- */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 8px;
        }
        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1c1e21;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-title i {
            color: #1b74e4;
        }
        .post-count-badge {
            background: #e7f3ff;
            color: #1b74e4;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        /* ----- ANNOUNCEMENT CARD ----- */
        .announce-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
            transition: box-shadow 0.2s;
        }
        .announce-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        /* ----- CARD HEADER ----- */
        .card-header-custom {
            display: flex;
            align-items: center;
            padding: 14px 16px 8px;
            gap: 10px;
        }
        .card-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1b74e4, #0a5ecf);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .card-meta {
            flex: 1;
        }
        .card-page-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1c1e21;
            line-height: 1.2;
        }
        .card-post-date {
            font-size: 0.78rem;
            color: #65676b;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .card-post-date .badge-official {
            background: #e7f3ff;
            color: #1b74e4;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.1rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
        }

        /* ----- CARD BODY ----- */
        .card-body-custom {
            padding: 2px 16px 10px;
        }
        .card-text {
            font-size: 0.97rem;
            color: #1c1e21;
            line-height: 1.6;
            margin: 0;
            white-space: pre-line;
            word-break: break-word;
        }
        
        /* ----- SEE MORE / SEE LESS ----- */
        .see-more-btn {
            background: none;
            border: none;
            color: #65676b;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 4px 0;
            cursor: pointer;
            transition: color 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .see-more-btn:hover {
            color: #1b74e4;
        }
        .see-more-btn i {
            font-size: 0.7rem;
            transition: transform 0.2s;
        }
        .see-more-btn.expanded i {
            transform: rotate(180deg);
        }
        .card-text .full-text {
            display: none;
        }
        .card-text.expanded .short-text {
            display: none;
        }
        .card-text.expanded .full-text {
            display: inline;
        }

        /* ----- IMAGE GALLERY ----- */
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 4px 12px 8px;
            background: #fafbfc;
        }
        .gallery-item {
            border-radius: 8px;
            cursor: pointer;
            object-fit: cover;
            transition: opacity 0.2s;
            flex: 1 1 auto;
        }
        .gallery-item:hover {
            opacity: 0.92;
        }
        .gallery-item.single {
            width: 100%;
            max-height: 400px;
            aspect-ratio: auto;
        }
        .gallery-item.multi {
            width: calc(50% - 2px);
            aspect-ratio: 1/1;
        }
        .gallery-item.multi-3 {
            width: calc(33.33% - 3px);
            aspect-ratio: 1/1;
        }

        /* ----- REACTION SUMMARY ----- */
        .reaction-summary {
            padding: 6px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #65676b;
            border-bottom: 1px solid #e4e6ea;
            min-height: 34px;
        }
        .reaction-bubbles {
            display: flex;
            align-items: center;
        }
        .reaction-emoji-bubble {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            border: 2px solid #fff;
            margin-left: -4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.12);
        }
        .reaction-emoji-bubble:first-child { margin-left: 0; }
        .comment-count-link {
            cursor: pointer;
            font-weight: 600;
        }
        .comment-count-link:hover {
            text-decoration: underline;
            color: #1b74e4;
        }

        /* ----- ACTION BUTTONS ----- */
        .action-bar {
            border-top: 1px solid #e4e6ea;
            padding: 4px 12px;
            display: flex;
            gap: 2px;
        }
        .action-btn {
            flex: 1;
            background: none;
            border: none;
            color: #65676b;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 8px 4px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background 0.15s, color 0.15s;
            position: relative;
            user-select: none;
        }
        .action-btn:hover {
            background: #f0f2f5;
            color: #1c1e21;
        }
        .action-btn.reacted-like { color: #1b74e4; }
        .action-btn.reacted-love { color: #f33e58; }
        .action-btn.reacted-haha,
        .action-btn.reacted-wow,
        .action-btn.reacted-sad { color: #f7b928; }
        .action-btn.reacted-angry { color: #e9710f; }
        .action-btn.text-danger:hover {
            background: #fde8e8;
            color: #dc3545;
        }

        /* ----- REACTION PICKER ----- */
        .reaction-picker {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.2);
            padding: 6px 10px;
            gap: 2px;
            z-index: 200;
            white-space: nowrap;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .reaction-picker.open { display: flex; }
        .reaction-option {
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 50%;
            padding: 4px 6px;
            transition: transform 0.15s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .reaction-option:hover {
            transform: scale(1.4) translateY(-4px);
        }
        .reaction-option .tip {
            position: absolute;
            bottom: calc(100% + 4px);
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.75);
            color: #fff;
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 4px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s;
        }
        .reaction-option:hover .tip { opacity: 1; }

        /* ----- COMMENTS SECTION ----- */
        .comments-section {
            padding: 8px 16px 12px;
            background: #fff;
            border-top: 1px solid #e4e6ea;
        }
        .comment-input-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .comment-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #42a5f5, #1565c0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
            flex-shrink: 0;
            font-weight: 700;
        }
        .comment-input-wrap {
            flex: 1;
            position: relative;
        }
        .comment-input {
            width: 100%;
            background: #f0f2f5;
            border: none;
            border-radius: 20px;
            padding: 9px 40px 9px 16px;
            font-size: 0.9rem;
            outline: none;
            resize: none;
            line-height: 1.4;
            max-height: 120px;
            overflow-y: auto;
            font-family: inherit;
            transition: background 0.2s;
        }
        .comment-input:focus {
            background: #e4e6eb;
        }
        .comment-send-btn {
            position: absolute;
            right: 10px;
            bottom: 8px;
            background: none;
            border: none;
            color: #1b74e4;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0;
            display: none;
        }
        .comment-send-btn.visible { display: block; }

        .comment-item {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            align-items: flex-start;
        }
        .comment-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
            flex-shrink: 0;
            font-weight: 700;
        }
        .comment-bubble {
            background: #f0f2f5;
            border-radius: 16px;
            padding: 8px 12px;
            max-width: calc(100% - 50px);
        }
        .comment-author {
            font-size: 0.83rem;
            font-weight: 700;
            color: #1c1e21;
        }
        .comment-text {
            font-size: 0.88rem;
            color: #1c1e21;
            word-break: break-word;
        }
        .comment-time {
            font-size: 0.72rem;
            color: #65676b;
            margin-top: 3px;
            padding-left: 4px;
        }
        .comment-delete-btn {
            background: none;
            border: none;
            color: #65676b;
            cursor: pointer;
            font-size: 0.75rem;
            padding: 0 4px;
            opacity: 0;
            transition: opacity 0.15s;
        }
        .comment-item:hover .comment-delete-btn { opacity: 1; }

        /* ----- EMPTY STATE ----- */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            color: #65676b;
        }
        .empty-state i {
            font-size: 3rem;
            color: #bcc0c4;
            display: block;
            margin-bottom: 12px;
        }

        /* ----- TOAST ----- */
        #del-toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            background: #323232;
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            animation: toastIn 0.3s ease;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ----- IMAGE MODAL ----- */
        .modal-img-overlay {
            background: rgba(0,0,0,0.92);
        }
        .modal-img-overlay .modal-content {
            background: transparent;
            border: none;
        }
        .modal-img-overlay .btn-close {
            filter: invert(1);
        }
        .modal-img-nav {
            font-size: 3rem;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0 12px;
        }
        .modal-img-nav:hover {
            color: #fff;
        }
        .modal-img {
            width: 100%;
            max-height: 85vh;
            object-fit: contain;
        }

        /* ----- RESPONSIVE ----- */
        @media (max-width: 576px) {
            .page-wrap {
                margin: 0.75rem auto 1.5rem;
                padding: 0 8px;
            }
            .page-title {
                font-size: 1.15rem;
            }
            .card-header-custom {
                padding: 10px 12px 6px;
            }
            .card-body-custom {
                padding: 0 12px 8px;
            }
            .card-text {
                font-size: 0.92rem;
            }
            .action-btn {
                font-size: 0.8rem;
                padding: 6px 2px;
            }
            .action-btn i {
                font-size: 1rem;
            }
            .reaction-picker {
                padding: 4px 6px;
                gap: 0;
            }
            .reaction-option {
                font-size: 1.2rem;
                padding: 2px 4px;
            }
            .comments-section {
                padding: 6px 10px 10px;
            }
            .gallery-item.multi {
                width: calc(50% - 2px);
            }
            .gallery-item.multi-3 {
                width: calc(33.33% - 3px);
            }
            #del-toast {
                bottom: 85px;
                right: 12px;
                left: 12px;
                font-size: 0.85rem;
                padding: 10px 16px;
                border-radius: 10px;
            }
            .see-more-btn {
                font-size: 0.82rem;
            }
        }
    </style>
</head>
<body>

<!-- DELETE TOAST -->
<div id="del-toast"><i class="bi bi-check-circle-fill text-success"></i> Announcement removed.</div>

<!-- IMAGE MODAL -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-img-overlay">
            <div class="modal-header border-0 position-relative" style="z-index:10;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height:60vh;position:relative;">
                <button class="modal-img-nav position-absolute start-0 ms-2" id="prevBtn" onclick="changeImage(-1)" style="display:none;">‹</button>
                <img src="" id="modalImg" class="modal-img" alt="Zoomed view">
                <button class="modal-img-nav position-absolute end-0 me-2" id="nextBtn" onclick="changeImage(1)" style="display:none;">›</button>
            </div>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<?php include __DIR__ . '/resident_navbar.php'; ?>

<div class="page-wrap">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-megaphone-fill"></i>
            Announcements
        </div>
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
        $reactions = $bmis->get_reactions($ann_id);
        $userReact = $bmis->get_user_reaction($ann_id, $current_user_id);
        $comments  = $bmis->get_comments($ann_id);
        $totalReact = array_sum(array_column($reactions, 'count'));
        usort($reactions, fn($a,$b) => $b['count'] - $a['count']);
        $topReactions = array_slice($reactions, 0, 1);
        $reactionLabel = $userReact ? ucfirst($userReact) : 'Like';
        $reactionIcon  = $userReact ? $emojiMap[$userReact] : '👍';
        $initials = strtoupper(substr($userdetails['firstname'],0,1) . substr($userdetails['surname'],0,1));
        $images = $hasImg ? array_map('trim', explode(',', $ann['image'])) : [];
        $imgCount = count($images);
        $imgClass = $imgCount === 1 ? 'gallery-item single' : ($imgCount === 2 ? 'gallery-item multi' : 'gallery-item multi-3');
        
        // ── Process announcement text for "See more" ──
        $announcement_text = $ann['event'] ?? '';
        $linkified_text = bmis_linkify_announcement($announcement_text);
        $word_limit = 20; // Facebook-style word limit
        $words = preg_split('/\s+/', strip_tags($linkified_text));
        $is_long = count($words) > $word_limit;
        
        // Build short and full versions with HTML preserved
        if ($is_long) {
            $short_words = array_slice($words, 0, $word_limit);
            $short_text = implode(' ', $short_words) . '...';
            // Re-linkify the short text
            $short_text = bmis_linkify_announcement($short_text);
            $full_text = $linkified_text;
        } else {
            $short_text = $linkified_text;
            $full_text = $linkified_text;
        }
        
        // Escape for JSON/JS
        $escaped_full = addslashes($full_text);
        $escaped_short = addslashes($short_text);
?>
    <!-- ANNOUNCEMENT CARD -->
    <div class="announce-card" id="ann-card-<?= $ann_id; ?>">

        <!-- Header -->
        <div class="card-header-custom">
            <div class="card-avatar"><i class="bi bi-building-fill"></i></div>
            <div class="card-meta">
                <div class="card-page-name">Barangay San Pedro Iriga</div>
                <div class="card-post-date">
                    <?= date('F j, Y', strtotime($ann['start_date'])); ?>
                    <span class="badge-official">Official</span>
                </div>
            </div>
        </div>

        <!-- Body with See More functionality -->
        <?php if(!empty($announcement_text)): ?>
        <div class="card-body-custom">
            <div class="card-text" id="post-text-<?= $ann_id; ?>">
                <?php if ($is_long): ?>
                    <span class="short-text" id="short-<?= $ann_id; ?>">
                        <?= nl2br($short_text); ?>
                    </span>
                    <span class="full-text" id="full-<?= $ann_id; ?>" style="display:none;">
                        <?= nl2br($full_text); ?>
                    </span>
                    <button class="see-more-btn" id="see-more-btn-<?= $ann_id; ?>" 
                            onclick="toggleSeeMore(<?= $ann_id; ?>)">
                        <span id="see-more-label-<?= $ann_id; ?>">See more</span>
                        <i class="bi bi-chevron-down" id="see-more-icon-<?= $ann_id; ?>"></i>
                    </button>
                <?php else: ?>
                    <span><?= nl2br($full_text); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Image Gallery -->
        <?php if($hasImg && !empty($images)): 
            $images_json = json_encode($images);
        ?>
        <div class="image-gallery">
            <?php foreach($images as $index => $img): if(empty($img)) continue; ?>
                <img src="uploads/<?= htmlspecialchars($img); ?>" 
                     class="<?= $imgClass; ?>" 
                     alt="Announcement image"
                     onclick='openGallery(<?= $images_json; ?>, <?= $index; ?>)'>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Reaction Summary -->
        <div class="reaction-summary">
            <div class="d-flex align-items-center">
                <div class="reaction-bubbles" id="bubbles-<?= $ann_id; ?>">
                    <?php foreach($topReactions as $r): if((int)$r['count'] < 1) continue; ?>
                    <div class="reaction-emoji-bubble bg-<?= $r['reaction_type']; ?>"><?= $emojiMap[$r['reaction_type']]; ?></div>
                    <?php endforeach; ?>
                </div>
                <span id="react-total-<?= $ann_id; ?>"><?= $totalReact > 0 ? $totalReact : ''; ?></span>
            </div>
            <span class="comment-count-link" onclick="toggleComments(<?= $ann_id; ?>)" id="cmt-count-<?= $ann_id; ?>">
                <?= count($comments) > 0 ? count($comments) . ' comment' . (count($comments)>1?'s':'') : ''; ?>
            </span>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <button class="action-btn <?= $userReact ? 'reacted-'.$userReact : ''; ?>"
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
                    <span class="reaction-option" onclick="pickReaction(event,<?= $ann_id; ?>,'<?= $type; ?>')">
                        <?= $emoji; ?>
                        <span class="tip"><?= ucfirst($type); ?></span>
                    </span>
                    <?php endforeach; ?>
                </div>
                <span id="like-icon-<?= $ann_id; ?>"><?= $reactionIcon; ?></span>
                <span id="like-label-<?= $ann_id; ?>"><?= $reactionLabel; ?></span>
            </button>
            <button class="action-btn" onclick="toggleComments(<?= $ann_id; ?>)">
                <i class="bi bi-chat"></i> Comment
            </button>
            <button class="action-btn text-danger" onclick="deleteAnnouncement(<?= $ann_id; ?>)">
                <i class="bi bi-trash3"></i> Delete
            </button>
        </div>

        <!-- Comments Section -->
        <div class="comments-section" id="comments-<?= $ann_id; ?>" style="display:none;">
            <div id="comment-list-<?= $ann_id; ?>">
                <?php foreach($comments as $c):
                    $cInit = strtoupper(substr($c['full_name'],0,1) . (strpos($c['full_name'],' ')!==false ? substr($c['full_name'],strpos($c['full_name'],' ')+1,1) : ''));
                    $isOwn = ((int)$c['user_id'] === (int)$current_user_id);
                    $colors = ['#ab47bc,#6a1b9a','#ef5350,#b71c1c','#26a69a,#00695c','#5c6bc0,#283593','#f57c00,#e65100','#00897b,#004d40'];
                    $cColor = $colors[abs(crc32($c['full_name'])) % count($colors)];
                ?>
                <div class="comment-item" id="comment-item-<?= $c['id_comment']; ?>">
                    <div class="comment-avatar" style="background:linear-gradient(135deg,<?= $cColor ?>)">
                        <?= htmlspecialchars($cInit); ?>
                    </div>
                    <div style="flex:1;">
                        <div class="comment-bubble">
                            <div class="comment-author"><?= htmlspecialchars($c['full_name']); ?></div>
                            <div class="comment-text"><?= nl2br(htmlspecialchars($c['comment_text'])); ?></div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="comment-time"><?= date('M j, Y g:i A', strtotime($c['created_at'])); ?></span>
                            <?php if($isOwn): ?>
                            <button class="comment-delete-btn" onclick="deleteComment(<?= $c['id_comment']; ?>,<?= $ann_id; ?>)">
                                <i class="bi bi-trash3"></i> Delete
                            </button>
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

<script>
// ── Toggle See More / See Less ──────────────────────────────────────────
function toggleSeeMore(postId) {
    const shortEl = document.getElementById('short-' + postId);
    const fullEl = document.getElementById('full-' + postId);
    const btn = document.getElementById('see-more-btn-' + postId);
    const label = document.getElementById('see-more-label-' + postId);
    const icon = document.getElementById('see-more-icon-' + postId);
    
    if (fullEl.style.display === 'none') {
        // Expand
        shortEl.style.display = 'none';
        fullEl.style.display = 'inline';
        label.textContent = 'See less';
        icon.classList.add('bi-chevron-up');
        icon.classList.remove('bi-chevron-down');
        btn.classList.add('expanded');
    } else {
        // Collapse
        shortEl.style.display = 'inline';
        fullEl.style.display = 'none';
        label.textContent = 'See more';
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        btn.classList.remove('expanded');
    }
}

// ── Show toast if redirected back after delete ──────────────────────────
(function(){
    const p = new URLSearchParams(location.search);
    if(p.get('toast') === 'deleted') showDelToast();
})();

function showDelToast(){
    const t = document.getElementById('del-toast');
    t.style.display = 'flex';
    setTimeout(() => { t.style.display = 'none'; }, 3000);
    history.replaceState(null, '', location.pathname);
}

// ── AJAX delete ──────────────────────────────────────────────────────────
function deleteAnnouncement(annId) {
    if (!confirm('Remove this announcement from your feed?')) return;
    fetch('announcement_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=delete_announcement&announcement_id=${annId}`
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const card = document.getElementById('ann-card-' + annId);
            if (card) {
                card.style.transition = 'opacity .3s, transform .3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(.97)';
                setTimeout(() => card.remove(), 300);
            }
            showDelToast();
        }
    })
    .catch(() => {
        // Fallback: normal form POST
        const f = document.createElement('form');
        f.method = 'POST';
        f.innerHTML = `<input name="delete_announcement" value="1">
                       <input name="id_announcement" value="${annId}">`;
        document.body.appendChild(f);
        f.submit();
    });
}

// ── Image gallery ──────────────────────────────────────────────────────
let currentImages = [], currentIndex = 0;
function openGallery(images, index) {
    currentImages = images;
    currentIndex = index;
    updateModalImage();
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
function changeImage(dir) {
    currentIndex = (currentIndex + dir + currentImages.length) % currentImages.length;
    updateModalImage();
}
function updateModalImage() {
    document.getElementById('modalImg').src = 'uploads/' + currentImages[currentIndex].trim();
    const d = currentImages.length > 1 ? 'block' : 'none';
    document.getElementById('prevBtn').style.display = d;
    document.getElementById('nextBtn').style.display = d;
}
document.addEventListener('keydown', e => {
    if (document.getElementById('imageModal').classList.contains('show')) {
        if (e.key === 'ArrowLeft') changeImage(-1);
        if (e.key === 'ArrowRight') changeImage(1);
        if (e.key === 'Escape') bootstrap.Modal.getInstance(document.getElementById('imageModal'))?.hide();
    }
});

// ── Reactions ──────────────────────────────────────────────────────────
const AJAX = 'announcement_ajax.php';
const EM = {like:'👍',love:'❤️',haha:'😂',wow:'😮',sad:'😢',angry:'😡'};
let holdTimer = null, pickerOpen = null;

function startHold(id){ holdTimer = setTimeout(() => openPicker(id), 400); }
function cancelHold(){ clearTimeout(holdTimer); holdTimer = null; }
function openPicker(id){
    if(pickerOpen && pickerOpen !== id) document.getElementById('picker-'+pickerOpen)?.classList.remove('open');
    const p = document.getElementById('picker-'+id);
    p.classList.toggle('open');
    pickerOpen = p.classList.contains('open') ? id : null;
}
document.addEventListener('click', e => {
    if(pickerOpen && !e.target.closest('.action-btn')){
        document.getElementById('picker-'+pickerOpen)?.classList.remove('open');
        pickerOpen = null;
    }
});
function pickReaction(e,annId,type){ e.stopPropagation(); document.getElementById('picker-'+annId)?.classList.remove('open'); pickerOpen=null; react(annId,type); }
function toggleComments(id){ const el=document.getElementById('comments-'+id); const show=el.style.display==='none'; el.style.display=show?'block':'none'; if(show) document.getElementById('input-'+id).focus(); }
function quickLike(e,id){
    if(e.target.closest('.reaction-option')||e.target.closest('.reaction-picker'))return;
    if(pickerOpen===id){ document.getElementById('picker-'+id)?.classList.remove('open'); pickerOpen=null; return; }
    const btn=document.getElementById('like-btn-'+id);
    react(id, btn.dataset.current||'like');
}
function react(annId,type){
    const btn=document.getElementById('like-btn-'+annId);
    const label=document.getElementById('like-label-'+annId);
    const icon=document.getElementById('like-icon-'+annId);
    const totalEl=document.getElementById('react-total-'+annId);
    const isRemoving=btn.classList.contains('reacted-'+type);
    const prev={cls:btn.className,lbl:label.textContent,ico:icon.textContent,tot:parseInt(totalEl.textContent||0)};
    if(isRemoving){ btn.className='action-btn'; label.textContent='Like'; icon.textContent='👍'; totalEl.textContent=(prev.tot-1)>0?(prev.tot-1):''; btn.dataset.current=''; }
    else{ const was=btn.dataset.current!==""; btn.className='action-btn reacted-'+type; label.textContent=type.charAt(0).toUpperCase()+type.slice(1); icon.textContent=EM[type]; if(!was) totalEl.textContent=prev.tot+1; btn.dataset.current=type; }
    fetch(AJAX,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=toggle_reaction&announcement_id=${annId}&reaction_type=${type}`})
    .then(r=>r.json()).then(d=>{
        if(!d.success){ btn.className=prev.cls; label.textContent=prev.lbl; icon.textContent=prev.ico; totalEl.textContent=prev.tot>0?prev.tot:''; return; }
        const total=d.counts.reduce((s,r)=>s+parseInt(r.count),0);
        totalEl.textContent=total>0?total:'';
        const sorted=[...d.counts].sort((a,b)=>b.count-a.count).slice(0,3);
        document.getElementById('bubbles-'+annId).innerHTML=sorted.filter(r=>r.count>0).map(r=>`<div class="reaction-emoji-bubble bg-${r.reaction_type}">${EM[r.reaction_type]}</div>`).join('');
        btn.dataset.current=d.user_reaction||'';
    }).catch(()=>{ btn.className=prev.cls; label.textContent=prev.lbl; icon.textContent=prev.ico; totalEl.textContent=prev.tot>0?prev.tot:''; });
}

// ── Comments ───────────────────────────────────────────────────────────
function onCommentInput(ta,id){ ta.style.height='auto'; ta.style.height=Math.min(ta.scrollHeight,120)+'px'; document.getElementById('send-btn-'+id).classList.toggle('visible',ta.value.trim().length>0); }
function commentEnter(e,id){ if(e.key==='Enter'&&!e.shiftKey){ e.preventDefault(); submitComment(id); } }
function submitComment(id){
    const inp=document.getElementById('input-'+id);
    const txt=inp.value.trim();
    if(!txt)return;
    fetch(AJAX,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=add_comment&announcement_id=${id}&comment_text=${encodeURIComponent(txt)}`})
    .then(r=>r.json()).then(d=>{
        if(!d.success)return;
        const colors=['#ab47bc,#6a1b9a','#ef5350,#b71c1c','#26a69a,#00695c','#5c6bc0,#283593','#f57c00,#e65100','#00897b,#004d40'];
        const col=colors[Math.floor(Math.random()*colors.length)];
        const init=d.full_name.split(' ').map(w=>w[0]||'').join('').substring(0,2).toUpperCase();
        document.getElementById('comment-list-'+id).insertAdjacentHTML('beforeend',`
        <div class="comment-item" id="comment-item-${d.id_comment}">
            <div class="comment-avatar" style="background:linear-gradient(135deg,${col})">${esc(init)}</div>
            <div style="flex:1"><div class="comment-bubble"><div class="comment-author">${esc(d.full_name)}</div><div class="comment-text">${esc(d.comment_text).replace(/\n/g,'<br>')}</div></div>
            <div class="d-flex align-items-center gap-2"><span class="comment-time">${d.created_at}</span>
            <button class="comment-delete-btn" onclick="deleteComment(${d.id_comment},${id})"><i class="bi bi-trash3"></i> Delete</button></div></div></div>`);
        const tot=document.getElementById('comment-list-'+id).querySelectorAll('.comment-item').length;
        document.getElementById('cmt-count-'+id).textContent=tot+' comment'+(tot>1?'s':'');
        inp.value=''; inp.style.height='auto';
        document.getElementById('send-btn-'+id).classList.remove('visible');
    });
}
function deleteComment(cid,aid){
    if(!confirm('Delete your comment?'))return;
    fetch(AJAX,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=delete_comment&comment_id=${cid}`})
    .then(r=>r.json()).then(d=>{ if(!d.success)return; document.getElementById('comment-item-'+cid)?.remove(); const tot=document.getElementById('comment-list-'+aid).querySelectorAll('.comment-item').length; document.getElementById('cmt-count-'+aid).textContent=tot>0?tot+' comment'+(tot>1?'s':''):''; });
}
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
</body>
</html>