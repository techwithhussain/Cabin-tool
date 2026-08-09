/* ============================================================
   Cabin – Landing Page JS
   ============================================================ */

'use strict';

// ─────────────────────────────────────────────
// FAQ Accordion
// ─────────────────────────────────────────────
(function initFaq() {
    const items = document.querySelectorAll('.faq-item');
    if (!items.length) return;

    items.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer   = item.querySelector('.faq-answer');
        if (!question || !answer) return;

        question.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Close all others
            items.forEach(other => {
                if (other !== item) {
                    other.classList.remove('open');
                    const otherAnswer = other.querySelector('.faq-answer');
                    const otherBtn    = other.querySelector('.faq-question');
                    if (otherAnswer) otherAnswer.style.maxHeight = '0';
                    if (otherBtn)    otherBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Toggle this
            if (isOpen) {
                item.classList.remove('open');
                answer.style.maxHeight = '0';
                question.setAttribute('aria-expanded', 'false');
            } else {
                item.classList.add('open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });
})();

// ─────────────────────────────────────────────
// Smooth Scroll for Anchor Links
// ─────────────────────────────────────────────
(function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', e => {
            const targetId = anchor.getAttribute('href').slice(1);
            const target   = document.getElementById(targetId);
            if (!target) return;

            e.preventDefault();
            const navHeight = document.getElementById('navbar')?.offsetHeight || 72;

            window.scrollTo({
                top:      target.getBoundingClientRect().top + window.scrollY - navHeight - 16,
                behavior: 'smooth',
            });
        });
    });
})();

// ─────────────────────────────────────────────
// Stats Counter Animation
// ─────────────────────────────────────────────
(function initStatsCounters() {
    const statCards = document.querySelectorAll('.stat-card');
    if (!statCards.length) return;

    const animateCounter = (el, target) => {
        const duration = 1800;
        const start    = performance.now();
        const startVal = 0;

        const update = (currentTime) => {
            const elapsed  = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out cubic
            const eased    = 1 - Math.pow(1 - progress, 3);
            const current  = Math.floor(startVal + (target - startVal) * eased);

            el.textContent = Cabin.formatNumber(current) + (target >= 1000 ? '+' : '%');

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        };

        requestAnimationFrame(update);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numberEl = entry.target.querySelector('.stat-card__number');
                const count    = parseInt(numberEl?.dataset.count);
                if (numberEl && !isNaN(count)) {
                    animateCounter(numberEl, count);
                }
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statCards.forEach(card => observer.observe(card));
})();

// ─────────────────────────────────────────────
// Hero CTA Button – micro interaction
// ─────────────────────────────────────────────
(function initHeroButtons() {
    const hero = document.getElementById('heroCreateBtn');
    const cta  = document.getElementById('ctaCreateBtn');

    [hero, cta].forEach(btn => {
        if (!btn) return;

        btn.addEventListener('mouseenter', () => {
            btn.style.letterSpacing = '0.02em';
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.letterSpacing = '';
        });
    });
})();
