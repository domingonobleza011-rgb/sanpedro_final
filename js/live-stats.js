/**
 * live-stats.js
 * Polls poll_stats.php every 60 seconds and updates all stat elements.
 * (Replaces the old SSE / sse_stats.php approach to avoid eating
 *  InfinityFree's daily hit limit with persistent connections.)
 *
 * How to mark elements for live updates:
 *   Add  data-live="KEY"  to any element whose text should update.
 *   Keys returned by poll_stats.php:
 *
 *   res_male, res_female, res_head, res_member, res_voter,
 *   res_senior, res_pwd, staff_total, staff_male, staff_female,
 *   cmp_total, cmp_pending, cmp_resolved, msg_count, id_pending
 */

(function () {
    'use strict';

    var POLL_INTERVAL = 60000; // 60 seconds — change to 30000 if you want 30 s

    // ── Notification toast ────────────────────────────────────
    function showToast(msg, type) {
        var existing = document.getElementById('sse-toast');
        if (existing) existing.remove();

        var colors = {
            info    : { bg: '#1a4480', icon: 'ℹ️' },
            warning : { bg: '#d97706', icon: '⚠️' },
            success : { bg: '#059669', icon: '✅' },
        };
        var c = colors[type] || colors.info;

        var toast = document.createElement('div');
        toast.id = 'sse-toast';
        toast.style.cssText = [
            'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
            'background:' + c.bg, 'color:#fff',
            'padding:12px 20px', 'border-radius:12px',
            'box-shadow:0 8px 24px rgba(0,0,0,0.18)',
            'font-size:14px', 'font-weight:600',
            'display:flex', 'align-items:center', 'gap:10px',
            'transition:opacity .4s', 'opacity:0',
            'max-width:320px',
        ].join(';');
        toast.innerHTML = c.icon + ' ' + msg;
        document.body.appendChild(toast);

        requestAnimationFrame(function () {
            toast.style.opacity = '1';
        });

        setTimeout(function () {
            toast.style.opacity = '0';
            setTimeout(function () { toast.remove(); }, 400);
        }, 4000);
    }

    // ── Animate a number change ───────────────────────────────
    function animateValue(el, newVal) {
        var old = parseInt(el.textContent, 10);
        if (isNaN(old) || old === newVal) {
            el.textContent = newVal;
            return;
        }
        el.style.transition = 'color .3s';
        el.style.color = newVal > old ? '#059669' : '#dc2626';
        el.textContent = newVal;
        setTimeout(function () { el.style.color = ''; }, 1200);
    }

    // ── Apply stats to DOM ────────────────────────────────────
    var prev = {};

    function applyStats(stats) {
        Object.keys(stats).forEach(function (key) {
            var els = document.querySelectorAll('[data-live="' + key + '"]');
            els.forEach(function (el) {
                animateValue(el, stats[key]);
            });
        });

        // ── Smart notifications (only after first successful poll) ──
        if (Object.keys(prev).length) {
            if (stats.cmp_pending > prev.cmp_pending) {
                var diff = stats.cmp_pending - prev.cmp_pending;
                showToast(diff + ' new complaint' + (diff > 1 ? 's' : '') + ' received!', 'warning');
            }
            if (stats.msg_count > prev.msg_count) {
                var d = stats.msg_count - prev.msg_count;
                showToast(d + ' new message' + (d > 1 ? 's' : '') + ' from residents.', 'info');
            }
            if (stats.id_pending > prev.id_pending) {
                showToast('New ID verification request pending.', 'info');
            }
        }

        prev = Object.assign({}, stats);
    }

    // ── Chart updater ─────────────────────────────────────────
    function tryUpdateCharts(stats) {
        if (window.genderChart && window.genderChart.data) {
            window.genderChart.data.datasets[0].data = [stats.res_male, stats.res_female];
            window.genderChart.data.labels = [
                'Male (' + stats.res_male + ')',
                'Female (' + stats.res_female + ')',
            ];
            window.genderChart.update('none');
        }
        if (window.residentChart && window.residentChart.data) {
            window.residentChart.data.datasets[0].data = [
                stats.res_head, stats.res_voter, stats.res_senior, stats.res_pwd,
            ];
            window.residentChart.update('none');
        }
    }

    // ── Single poll request ───────────────────────────────────
    function poll() {
        fetch('poll_stats.php')
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (stats) {
                applyStats(stats);
                tryUpdateCharts(stats);
            })
            .catch(function (err) {
                console.warn('[live-stats] poll failed:', err);
            });
    }

    // ── Start polling ─────────────────────────────────────────
    function init() {
        poll();                            // immediate first fetch on page load
        setInterval(poll, POLL_INTERVAL);  // then every 60 s
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
