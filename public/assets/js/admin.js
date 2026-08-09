/* ============================================================
   Cabin – Admin JS
   ============================================================ */

'use strict';

// ─────────────────────────────────────────────
// Live Clock in Admin Topbar
// ─────────────────────────────────────────────
(function initAdminClock() {
    const timeEl = document.getElementById('adminTime');
    if (!timeEl) return;

    const update = () => {
        timeEl.textContent = new Date().toLocaleTimeString('en-US', {
            hour12: false,
            hour:   '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    };

    update();
    setInterval(update, 1000);
})();

// ─────────────────────────────────────────────
// Admin Password Toggle
// ─────────────────────────────────────────────
(function initAdminPwToggle() {
    const btn   = document.getElementById('adminPwToggle');
    const input = document.getElementById('adminPassword');
    if (!btn || !input) return;

    btn.addEventListener('click', () => {
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
    });
})();
