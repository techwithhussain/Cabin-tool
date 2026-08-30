<?php use App\Services\ExpiryService; ?>

<!-- ─────────────────────────────────────────
     HERO SECTION
────────────────────────────────────────── -->
<section class="hero" id="home">
    <div class="hero-bg">
        <div class="hero-blob hero-blob--1"></div>
        <div class="hero-blob hero-blob--2"></div>
    </div>

    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="currentColor"/></svg>
                    AES-256 Encrypted &bull; Burn After Read &bull; 100% Anonymous
                </div>

                <h1 class="hero-title">
                    <span class="hero-title--line1">Self-Destructing Notes &amp;</span><br>
                    <span class="hero-title--accent">Encrypted Private Sharing</span>
                </h1>

                <p class="hero-description">
                    Create self-destructing notes, set burn-after-read or custom auto-delete timers, and share sensitive passwords or data securely. No sign up required, zero logs, 100% private.
                </p>

                <div class="hero-actions">
                    <a href="/create" class="btn btn-primary btn-lg btn-pill" id="heroCreateBtn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        Create a Secure Note
                    </a>
                    <a href="#how-it-works" class="btn btn-link btn-lg">
                        Learn More &rarr;
                    </a>
                </div>

                <div class="hero-trust">
                    <div class="trust-item">
                        <span class="trust-icon trust-icon--purple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="#8B5CF6"/></svg>
                        </span>
                        AES-256 Encryption
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon trust-icon--green">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>
                        </span>
                        Burn After Read / Auto-Delete
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon trust-icon--orange">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        </span>
                        No Sign Up / Anonymous
                    </div>
                </div>
            </div>

            <!-- Hero Visual (Exact 3D Layout from Reference Image) -->
            <div class="hero-visual" id="heroVisual">
                <div class="hero-visual-wrapper">
                    <!-- Soft Background Glow -->
                    <div class="hero-visual__glow"></div>

                    <!-- Floating 3D Elements -->
                    <!-- Top-Left 3D Lock -->
                    <div class="float-3d float-3d--lock" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="3"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>

                    <!-- Top-Right 3D Paper Airplane with Dashed Trail -->
                    <div class="float-3d--plane-wrapper" aria-hidden="true">
                        <svg class="plane-trail" width="120" height="90" viewBox="0 0 120 90" fill="none">
                            <path d="M15 75 C 30 15, 80 10, 105 20" stroke="#DDD6FE" stroke-width="2.5" stroke-dasharray="4 5" stroke-linecap="round"/>
                        </svg>
                        <div class="float-3d float-3d--plane">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </div>
                    </div>

                    <!-- Middle-Right Orange 3D Clock -->
                    <div class="float-3d float-3d--clock" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    </div>

                    <!-- Bottom-Right Purple 3D Chat Bubble -->
                    <div class="float-3d float-3d--chat" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>

                    <!-- Left Floating Green Image Badge -->
                    <div class="float-3d float-3d--image" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </div>

                    <!-- Main White UI Mockup Card -->
                    <div class="hero-card">
                        <div class="hero-card__header">
                            <div class="hero-card__dots">
                                <span class="dot dot--red"></span>
                                <span class="dot dot--yellow"></span>
                                <span class="dot dot--green"></span>
                            </div>
                            <div class="hero-card__brand">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="#8B5CF6"/></svg>
                                <span>Cabin</span>
                            </div>
                        </div>

                        <div class="hero-card__body">
                            <!-- Note Area Placeholder -->
                            <div class="hero-card__textarea">
                                <span class="textarea-placeholder">Write your secure note...</span>
                                <div class="textarea-lines">
                                    <span class="line line--full"></span>
                                    <span class="line line--medium"></span>
                                </div>
                            </div>

                            <!-- Options Rows -->
                            <div class="hero-card__field">
                                <div class="field-label">
                                    <span class="field-icon field-icon--orange">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                    </span>
                                    Auto-delete
                                </div>
                                <div class="field-select">
                                    1 hour
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                </div>
                            </div>

                            <div class="hero-card__field">
                                <div class="field-label">
                                    <span class="field-icon field-icon--green">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                    </span>
                                    Encryption
                                </div>
                                <div class="field-select">
                                    AES-256
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                </div>
                            </div>

                            <!-- Action Button inside card -->
                            <button type="button" class="hero-card__submit-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                Create Secure Note
                            </button>
                        </div>

                        <!-- 100% Private Badge attached to bottom -->
                        <div class="hero-card__footer-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="#8B5CF6"/></svg>
                            100% Private
                        </div>
                    </div>

                    <!-- Foreground 3D Padlock Graphics with Shield -->
                    <div class="hero-3d-padlock" aria-hidden="true">
                        <svg width="150" height="170" viewBox="0 0 150 170" fill="none">
                            <defs>
                                <linearGradient id="lockBody" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#9333EA"/>
                                    <stop offset="60%" stop-color="#7C3AED"/>
                                    <stop offset="100%" stop-color="#6B21A8"/>
                                </linearGradient>
                                <linearGradient id="lockShackle" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#C4B5FD"/>
                                    <stop offset="100%" stop-color="#8B5CF6"/>
                                </linearGradient>
                                <filter id="lockShadow" x="-30%" y="-30%" width="160%" height="160%">
                                    <feDropShadow dx="5" dy="15" stdDeviation="12" flood-color="#581C87" flood-opacity="0.3"/>
                                </filter>
                            </defs>

                            <!-- Lock Shackle -->
                            <path d="M42 75 V 45 C 42 27 56 14 75 14 C 94 14 108 27 108 45 V 75" stroke="url(#lockShackle)" stroke-width="18" stroke-linecap="round" fill="none"/>
                            
                            <!-- Main 3D Lock Body -->
                            <rect x="22" y="65" width="106" height="90" rx="26" fill="url(#lockBody)" filter="url(#lockShadow)"/>

                            <!-- Shield Badge on Padlock -->
                            <path d="M75 82 L48 93 V 118 C 48 132 59 143 75 148 C 91 143 102 132 102 118 V 93 L 75 82 Z" fill="white"/>
                            <path d="M66 114 L73 121 L86 106" stroke="#7C3AED" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     FEATURE BADGES
────────────────────────────────────────── -->
<section class="feature-badges" id="features">
    <div class="container">
        <h2 class="sr-only">Key Security Features</h2>
        <div class="badges-grid">
            <div class="feature-badge" data-animate="fade-up" data-delay="0">
                <div class="feature-badge__icon feature-badge__icon--blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="currentColor"/></svg>
                </div>
                <div>
                    <h3>Secure & Private</h3>
                    <p>Your data is encrypted end-to-end and stays private.</p>
                </div>
            </div>
            <div class="feature-badge" data-animate="fade-up" data-delay="80">
                <div class="feature-badge__icon feature-badge__icon--purple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <div>
                    <h3>Password Protection</h3>
                    <p>Add an extra layer of security with password protection.</p>
                </div>
            </div>
            <div class="feature-badge" data-animate="fade-up" data-delay="160">
                <div class="feature-badge__icon feature-badge__icon--orange">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <h3>Expiry Control</h3>
                    <p>Set time limits and auto-delete notes after they expire.</p>
                </div>
            </div>
            <div class="feature-badge" data-animate="fade-up" data-delay="240">
                <div class="feature-badge__icon feature-badge__icon--green">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <div>
                    <h3>Custom URLs</h3>
                    <p>Create private, custom links for your notes easily.</p>
                </div>
            </div>
            <div class="feature-badge" data-animate="fade-up" data-delay="320">
                <div class="feature-badge__icon feature-badge__icon--indigo">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <h3>No Sign Up</h3>
                    <p>No registration, no login. 100% anonymous and hassle-free.</p>
                </div>
            </div>
            <div class="feature-badge" data-animate="fade-up" data-delay="400">
                <div class="feature-badge__icon feature-badge__icon--red">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                </div>
                <div>
                    <h3>Auto Delete</h3>
                    <p>Notes are deleted permanently after expiry.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     HOW IT WORKS
────────────────────────────────────────── -->
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="section-header" data-animate="fade-up">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Create, secure, and share your notes in 3 simple steps</p>
        </div>

        <div class="steps-container">
            <div class="step" data-animate="fade-up" data-delay="0">
                <div class="step-number">01</div>
                <div class="step-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <h3>Create Your Note</h3>
                <p>Write your note, set custom URL, and customise your settings.</p>
            </div>

            <div class="step-connector" data-animate="fade-in" data-delay="150">
                <div class="step-connector__line"></div>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </div>

            <div class="step" data-animate="fade-up" data-delay="200">
                <div class="step-number">02</div>
                <div class="step-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <h3>Secure & Set Expiry</h3>
                <p>Add password protection and set expiry time.</p>
            </div>

            <div class="step-connector" data-animate="fade-in" data-delay="300">
                <div class="step-connector__line"></div>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </div>

            <div class="step" data-animate="fade-up" data-delay="400">
                <div class="step-number">03</div>
                <div class="step-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </div>
                <h3>Share & Done</h3>
                <p>Get a unique link and share it. Note auto-deletes after expiry.</p>
            </div>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     POWERFUL FEATURES
────────────────────────────────────────── -->
<section class="powerful-features" id="powerful-features">
    <div class="container">
        <div class="section-header" data-animate="fade-up">
            <h2 class="section-title">Powerful Features</h2>
            <p class="section-subtitle">Everything you need for secure note sharing</p>
        </div>

        <div class="features-grid">
            <div class="feature-card" data-animate="fade-up" data-delay="0">
                <div class="feature-card__icon feature-card__icon--blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="currentColor"/></svg>
                </div>
                <h3>End-to-End Encryption</h3>
                <p>Your notes are encrypted before they are stored. Only the link holder can access them.</p>
            </div>

            <div class="feature-card" data-animate="fade-up" data-delay="80">
                <div class="feature-card__icon feature-card__icon--purple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <h3>Password Protection</h3>
                <p>Add a password to protect your note from unauthorised access.</p>
            </div>

            <div class="feature-card" data-animate="fade-up" data-delay="160">
                <div class="feature-card__icon feature-card__icon--orange">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Expiry & Auto Delete</h3>
                <p>Set expiry time and notes are automatically deleted after the time is up.</p>
            </div>

            <div class="feature-card" data-animate="fade-up" data-delay="240">
                <div class="feature-card__icon feature-card__icon--green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <h3>Custom Short URLs</h3>
                <p>Create memorable custom links like cabinn.in/my-secret-note with complete privacy.</p>
            </div>

            <div class="feature-card" data-animate="fade-up" data-delay="320">
                <div class="feature-card__icon feature-card__icon--indigo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <h3>No Sign Up Required</h3>
                <p>No registration, no login required. 100% anonymous and hassle-free.</p>
            </div>

            <div class="feature-card" data-animate="fade-up" data-delay="400">
                <div class="feature-card__icon feature-card__icon--teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Clean & Simple</h3>
                <p>A beautiful, minimal and easy to use interface for everyone.</p>
            </div>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     PERFECT FOR
────────────────────────────────────────── -->
<section class="perfect-for" id="use-cases">
    <div class="container">
        <div class="section-header" data-animate="fade-up">
            <h2 class="section-title">Perfect For <span class="section-title--accent">Every Situation</span></h2>
            <p class="section-subtitle">Cabin is designed for anyone who wants to share information simply and privately.</p>
        </div>

        <div class="audience-grid" data-animate="fade-up" data-delay="100">
            <?php
            $audiences = [
                ['icon' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>', 'label' => 'Developers', 'desc' => 'Share code, logs, or configs securely.'],
                ['icon' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>', 'label' => 'Businesses', 'desc' => 'Share confidential information safely.'],
                ['icon' => '<path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>', 'label' => 'Students', 'desc' => 'Share notes, assignments, or study materials.'],
                ['icon' => '<circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/>', 'label' => 'Designers', 'desc' => 'Share images, patterns, or feedback securely.'],
                ['icon' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>', 'label' => 'Anyone', 'desc' => 'Share anything you want privately and securely.'],
            ];
            foreach ($audiences as $a): ?>
            <div class="audience-card">
                <div class="audience-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><?= $a['icon'] ?></svg>
                </div>
                <h3><?= $a['label'] ?></h3>
                <p><?= $a['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     STATISTICS
────────────────────────────────────────── -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card" data-animate="fade-up" data-delay="0">
                <div class="stat-card__number" data-count="100">100%</div>
                <div class="stat-card__label">Secure & Private</div>
            </div>
            <div class="stat-card" data-animate="fade-up" data-delay="100">
                <div class="stat-card__number" data-count="<?= number_format((int)($stats['total'] ?? 50000)) ?>">
                    <?= number_format((int)($stats['total'] ?? 50000)) ?>+
                </div>
                <div class="stat-card__label">Notes Created</div>
            </div>
            <div class="stat-card" data-animate="fade-up" data-delay="200">
                <div class="stat-card__number" data-count="25000">25K+</div>
                <div class="stat-card__label">Happy Users</div>
            </div>
            <div class="stat-card" data-animate="fade-up" data-delay="300">
                <div class="stat-card__number">99.99%</div>
                <div class="stat-card__label">Auto Delete Success</div>
            </div>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     FAQ
────────────────────────────────────────── -->
<section class="faq-section" id="faq">
    <div class="container">
        <div class="section-header" data-animate="fade-up">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know about Cabin</p>
        </div>

        <div class="faq-list" data-animate="fade-up" data-delay="100">
            <?php
            $faqs = [
                [
                    'q' => 'What is a self-destructing note and how does Burn After Read work?',
                    'a' => 'A self-destructing note is an encrypted private message designed to disappear automatically. When you enable "Burn After Read", the note is completely and irreversibly deleted from our database the exact moment the recipient views it.'
                ],
                [
                    'q' => 'Is Cabin a secure alternative to Privnote and OneTimeSecret?',
                    'a' => 'Yes! Cabin is a modern, privacy-first alternative to Privnote and OneTimeSecret. We use military-grade AES-256-GCM encryption, require zero registration, keep no logs or trackers, and allow password protection and custom timers.'
                ],
                [
                    'q' => 'Can I use Cabin to share passwords and API keys securely?',
                    'a' => 'Absolutely. Cabin is built specifically for developers, teams, and individuals to safely share sensitive credentials, passwords, confidential tokens, and private messages without leaving traces in email or chat apps.'
                ],
                [
                    'q' => 'How is my note encrypted?',
                    'a' => 'All note contents are encrypted with AES-256-GCM authenticated encryption before being saved. The encryption keys are never stored alongside your content, ensuring that not even our servers can read your private notes.'
                ],
                [
                    'q' => 'What happens when a note expires?',
                    'a' => 'When a note reaches its set expiration time (e.g. 5 minutes, 1 hour, 24 hours), it is permanently wiped from the database. There are no backups, caches, or recovery methods.'
                ],
                [
                    'q' => 'Do I need to sign up or create an account?',
                    'a' => 'No registration or login is ever required. You can create and share encrypted self-destructing notes anonymously in seconds.'
                ],
                [
                    'q' => 'Can I set a password and a custom URL for my note?',
                    'a' => 'Yes! You can set an optional password hashed with Argon2id and create custom short links like cabinn.in/my-secret-note with complete end-to-end security.'
                ],
            ];
            foreach ($faqs as $i => $faq): ?>
            <div class="faq-item" id="faq-<?= $i ?>">
                <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-<?= $i ?>">
                    <span><?= htmlspecialchars($faq['q']) ?></span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="faq-answer" id="faq-answer-<?= $i ?>" role="region">
                    <p><?= htmlspecialchars($faq['a']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- FAQ Schema JSON-LD for Google Rich Snippets -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                <?php
                $schemaFaqs = [];
                foreach ($faqs as $faq) {
                    $schemaFaqs[] = json_encode([
                        '@type' => 'Question',
                        'name' => $faq['q'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['a']
                        ]
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
                echo implode(",\n", $schemaFaqs);
                ?>
            ]
        }
        </script>
    </div>
</section>

<!-- ─────────────────────────────────────────
     CTA SECTION
────────────────────────────────────────── -->
<section class="cta-section" id="contact">
    <div class="container">
        <div class="cta-card" data-animate="fade-up">
            <div class="cta-bg">
                <div class="cta-blob cta-blob--1"></div>
                <div class="cta-blob cta-blob--2"></div>
            </div>
            <div class="cta-content">
                <h2>Ready to create your <span class="cta-accent">secure note?</span></h2>
                <p>Create, secure, share, and relax. We take care of the rest.</p>
                <a href="/create" class="btn btn-white btn-lg" id="ctaCreateBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Create Your Note Now
                </a>
            </div>
            <div class="cta-visual">
                <div class="cta-illustration">
                    <svg width="200" height="160" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="30" y="20" width="140" height="100" rx="12" fill="white" fill-opacity="0.15" stroke="white" stroke-opacity="0.3" stroke-width="1.5"/>
                        <rect x="45" y="38" width="110" height="8" rx="4" fill="white" fill-opacity="0.4"/>
                        <rect x="45" y="54" width="90" height="6" rx="3" fill="white" fill-opacity="0.25"/>
                        <rect x="45" y="68" width="100" height="6" rx="3" fill="white" fill-opacity="0.25"/>
                        <rect x="45" y="82" width="70" height="6" rx="3" fill="white" fill-opacity="0.25"/>
                        <rect x="45" y="100" width="40" height="10" rx="5" fill="white" fill-opacity="0.5"/>
                        <circle cx="158" cy="118" r="22" fill="#4F5FFF" fill-opacity="0.8"/>
                        <rect x="147" y="110" width="4" height="16" rx="2" fill="white"/>
                        <rect x="147" y="110" width="16" height="4" rx="2" fill="white" transform="translate(4 4)"/>
                        <rect x="139" y="97" width="6" height="14" rx="3" fill="white" fill-opacity="0.5" transform="rotate(-45 139 97)"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>
