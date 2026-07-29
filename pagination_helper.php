<?php
/**
 * pagination_helper.php
 * Render Bootstrap 5 pagination controls.
 *
 * Usage:
 *   $paged = $residentbmis->view_resident_paginated(10);
 *   $rows  = $paged['rows'];
 *   // ... render your table using $rows ...
 *   render_pagination($paged);
 *
 * The helper preserves all existing GET params (e.g. keyword, search flag)
 * and only replaces/adds the `page` param.
 */
if (!function_exists('page_url')) {
    function page_url(string $base, string $sep, int $p): string {
        return htmlspecialchars($base . $sep . 'page=' . $p);
    }
}

function render_pagination(array $paged): void {
    $page      = $paged['page'];
    $lastPage  = $paged['last_page'];
    $total     = $paged['total'];
    $perPage   = $paged['per_page'];
    $from      = min($total, ($page - 1) * $perPage + 1);
    $to        = min($total, $page * $perPage);

    if ($lastPage <= 1) return;

    // Build base URL preserving current GET params except 'page'
    $params = $_GET;
    unset($params['page']);
    $base = '?' . http_build_query($params);
    $sep  = empty($params) ? '' : '&';

    $window = 2; // pages shown on each side of current
    ?>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 px-1">
        <small class="text-muted">
            Showing <strong><?= $from ?></strong>–<strong><?= $to ?></strong> of <strong><?= $total ?></strong> records
        </small>
        <nav aria-label="Table pagination">
            <ul class="pagination pagination-sm mb-0">

                <!-- Prev -->
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url($base, $sep, $page - 1) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php
                $start = max(1, $page - $window);
                $end   = min($lastPage, $page + $window);

                if ($start > 1) {
                    echo '<li class="page-item"><a class="page-link" href="' . page_url($base, $sep, 1) . '">1</a></li>';
                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= page_url($base, $sep, $i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($end < $lastPage) {
                    if ($end < $lastPage - 1) echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="' . page_url($base, $sep, $lastPage) . '">' . $lastPage . '</a></li>';
                }
                ?>

                <!-- Next -->
                <li class="page-item <?= $page >= $lastPage ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url($base, $sep, $page + 1) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
    <?php
}