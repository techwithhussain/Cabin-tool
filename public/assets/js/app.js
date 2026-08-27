/* ============================================================
   Cabin – App.js (Global Utilities)
   ============================================================ */

'use strict';

// ─────────────────────────────────────────────
// CSRF Token Helper
// ─────────────────────────────────────────────
const Cabin = {
    csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('#csrfToken')?.value
            || '';
    },

    /**
     * Fetch wrapper with CSRF header
     */
    async fetch(url, options = {}) {
        const defaults = {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.csrfToken(),
                'Accept': 'application/json',
                ...options.headers,
            },
        };

        const response = await fetch(url, { ...defaults, ...options });
        const data = await response.json();

        if (!response.ok) {
            throw { status: response.status, message: data.message || 'Request failed', data };
        }

        return data;
    },

    /**
     * Copy text to clipboard with fallback
     */
    async copyToClipboard(text) {
        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(text);
                return true;
            }
            // Fallback for older browsers
            const el = document.createElement('textarea');
            el.value = text;
            el.style.position = 'fixed';
            el.style.opacity = '0';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            return true;
        } catch {
            return false;
        }
    },

    /**
     * Show a temporary toast notification
     */
    toast(message, type = 'success', duration = 3000) {
        const existing = document.getElementById('cabin-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'cabin-toast';
        toast.className = `cabin-toast cabin-toast--${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            z-index: 9999;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            animation: toastIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            ${type === 'success' ? 'background: #22c55e; color: white;' : ''}
            ${type === 'error'   ? 'background: #ef4444; color: white;' : ''}
            ${type === 'info'    ? 'background: #4F5FFF; color: white;' : ''}
        `;

        // Inject animation keyframe once
        if (!document.getElementById('cabin-toast-style')) {
            const style = document.createElement('style');
            style.id = 'cabin-toast-style';
            style.textContent = `
                @keyframes toastIn {
                    from { transform: translateY(20px); opacity: 0; }
                    to   { transform: translateY(0);    opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    /**
     * Format number with commas
     */
    formatNumber(n) {
        return new Intl.NumberFormat().format(n);
    },
};

// ─────────────────────────────────────────────
// Navbar scroll effect (Reflow-Optimized with RAF)
// ─────────────────────────────────────────────
(function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    let ticking = false;
    const update = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });
})();

// ─────────────────────────────────────────────
// Mobile menu toggle
// ─────────────────────────────────────────────
(function initMobileMenu() {
    const toggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');
    if (!toggle || !navLinks) return;

    toggle.addEventListener('click', () => {
        const isOpen = navLinks.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen);
    });

    // Close on link click
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!navLinks.contains(e.target) && !toggle.contains(e.target)) {
            navLinks.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();

// ─────────────────────────────────────────────
// Intersection Observer – Scroll Animations
// ─────────────────────────────────────────────
(function initScrollAnimations() {
    const elements = document.querySelectorAll('[data-animate]');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = parseInt(entry.target.dataset.delay || '0');
                setTimeout(() => {
                    entry.target.classList.add('animated');
                }, delay);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    elements.forEach(el => observer.observe(el));
})();

// ─────────────────────────────────────────────
// Password visibility toggle (global)
// ─────────────────────────────────────────────
(function initPasswordToggles() {
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const wrap = btn.closest('.password-input-wrap');
            const input = wrap?.querySelector('input');
            if (!input) return;

            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';

            // Update icon
            const svg = btn.querySelector('svg');
            if (svg) {
                svg.innerHTML = isText
                    ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
                    : '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            }
        });
    });
})();

// Export for use in other scripts
window.Cabin = Cabin;
