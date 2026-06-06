/**
 * live-stats.js
 * Connects to sse_stats.php and updates all stat elements in real-time.
 * Include this file at the bottom of any page that shows live counters.
 *
 * How to mark elements for live updates:
 *   Add  data-live="KEY"  to any element whose text should update.
 *   Keys match the fields returned by sse_stats.php:
 *
 *   res_male, res_female, res_head, res_member, res_voter,
 *   res_senior, res_pwd, staff_total, staff_male, staff_female,
 *   cmp_total, cmp_pending, cmp_resolved, msg_count, id_pending
 */

(function () {
    'use strict';

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

        // Fade in
        requestAnimationFrame(function () {
            toast.style.opacity = '1';
        });

        // Fade out after 4 s
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
        // Update all [data-live="KEY"] elements
        Object.keys(stats).forEach(function (key) {
            var els = document.querySelectorAll('[data-live="' + key + '"]');
            els.forEach(function (el) {
                animateValue(el, stats[key]);
            });
        });

        // ── Smart notifications ───────────────────────────────
        if (Object.keys(prev).length) {
            // New complaint arrived
            if (stats.cmp_pending > prev.cmp_pending) {
                var diff = stats.cmp_pending - prev.cmp_pending;
                showToast(diff + ' new complaint' + (diff > 1 ? 's' : '') + ' received!', 'warning');
            }
            // New message
            if (stats.msg_count > prev.msg_count) {
                var d = stats.msg_count - prev.msg_count;
                showToast(d + ' new message' + (d > 1 ? 's' : '') + ' from residents.', 'info');
            }
            // New ID upload
            if (stats.id_pending > prev.id_pending) {
                showToast('New ID verification request pending.', 'info');
            }
        }

        prev = Object.assign({}, stats);
    }

    // ── Inline chart label updater (dashboard charts) ─────────
    // If Chart.js charts exist, update their labels too
    function tryUpdateCharts(stats) {
        // Gender pie: window.genderChart expected
        if (window.genderChart && window.genderChart.data) {
            window.genderChart.data.datasets[0].data = [stats.res_male, stats.res_female];
            window.genderChart.data.labels = [
                'Male (' + stats.res_male + ')',
                'Female (' + stats.res_female + ')',
            ];
            window.genderChart.update('none');
        }
        // Resident doughnut: window.residentChart expected
        if (window.residentChart && window.residentChart.data) {
            window.residentChart.data.datasets[0].data = [
                stats.res_head, stats.res_voter, stats.res_senior, stats.res_pwd,
            ];
            window.residentChart.update('none');
        }
    }

    // ── SSE connection ────────────────────────────────────────
    var evtSource;
    var retryDelay = 3000;

    function connect() {
        evtSource = new EventSource('sse_stats.php');

        evtSource.addEventListener('stats', function (e) {
            try {
                var stats = JSON.parse(e.data);
                applyStats(stats);
                tryUpdateCharts(stats);
                retryDelay = 3000; // reset backoff on success
            } catch (err) {
                console.warn('[SSE] parse error', err);
            }
        });

        evtSource.onerror = function () {
            evtSource.close();
            // Exponential backoff, max 30 s
            retryDelay = Math.min(retryDelay * 1.5, 30000);
            setTimeout(connect, retryDelay);
        };
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', connect);
    } else {
        connect();
    }

    // Clean up on page unload
    window.addEventListener('beforeunload', function () {
        if (evtSource) evtSource.close();
    });

})();
