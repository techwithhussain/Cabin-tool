<?php
$appUrl = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');
?>

<!-- ─────────────────────────────────────────
     BLOG HERO
────────────────────────────────────────── -->
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-inner">
            <div class="coming-soon-badge" style="margin-bottom:18px;">
                <span class="pulse-dot"></span>
                <span>Privacy &bull; Security &bull; Encryption</span>
            </div>
            <h1 class="blog-hero-title">Insights &amp; Security Guides</h1>
            <p class="blog-hero-desc">Practical guides on encryption, self-destructing notes, data privacy, and secure communication — written for real people.</p>

            <!-- Search -->
            <form method="GET" action="/blog" role="search" class="blog-search-bar">
                <div class="search-input-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input
                        type="search"
                        name="q"
                        id="blogSearchInput"
                        placeholder="Search articles…"
                        value="<?= htmlspecialchars($searchQuery ?? '') ?>"
                        aria-label="Search blog articles"
                        autocomplete="off"
                    />
                </div>
                <button type="submit" class="btn btn-primary btn-sm" aria-label="Search">Search</button>
            </form>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     CATEGORY PILLS
────────────────────────────────────────── -->
<?php if (!empty($categories)): ?>
<div class="container">
    <nav class="blog-categories-wrap" aria-label="Filter by category">
        <a href="/blog"
           class="cat-pill <?= (($activeCategory ?? 'all') === 'all') ? 'cat-pill--active' : '' ?>">
            All
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="/blog?category=<?= urlencode($cat) ?>"
           class="cat-pill <?= (($activeCategory ?? '') === $cat) ? 'cat-pill--active' : '' ?>">
            <?= htmlspecialchars($cat) ?>
        </a>
        <?php endforeach; ?>
    </nav>
</div>
<?php endif; ?>

<!-- ─────────────────────────────────────────
     BLOG GRID
────────────────────────────────────────── -->
<section class="blog-section">
    <div class="container">

        <?php if (!empty($blogs)): ?>
        <div class="blog-grid" id="blogGrid">
            <?php foreach ($blogs as $post): ?>
            <article class="blog-card">
                <?php if ($post->coverImage): ?>
                <a href="/blog/<?= htmlspecialchars($post->slug) ?>" class="blog-card__thumb-link" tabindex="-1" aria-hidden="true">
                    <div class="blog-card__thumb">
                        <img
                            src="<?= htmlspecialchars($post->coverImage) ?>"
                            alt="<?= htmlspecialchars($post->title) ?>"
                            loading="lazy"
                            class="blog-card__thumb-img"
                        />
                    </div>
                </a>
                <?php endif; ?>

                <div class="blog-card__header">
                    <a href="/blog?category=<?= urlencode($post->category) ?>" class="blog-card__cat"><?= htmlspecialchars($post->category) ?></a>
                    <span class="blog-card__read"><?= htmlspecialchars($post->readTime) ?></span>
                </div>

                <h2 class="blog-card__title">
                    <a href="/blog/<?= htmlspecialchars($post->slug) ?>"><?= htmlspecialchars($post->title) ?></a>
                </h2>

                <p class="blog-card__summary"><?= htmlspecialchars($post->summary) ?></p>

                <div class="blog-card__footer">
                    <div class="blog-author-info">
                        <div class="blog-author-avatar" aria-hidden="true">HL</div>
                        <div>
                            <span class="blog-author-name"><?= htmlspecialchars($post->author) ?></span>
                            <span class="blog-date"><?= date('M d, Y', strtotime($post->createdAt)) ?></span>
                        </div>
                    </div>
                    <a href="/blog/<?= htmlspecialchars($post->slug) ?>" class="blog-read-link" aria-label="Read <?= htmlspecialchars($post->title) ?>">
                        Read &rarr;
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <!-- Empty / No Results State -->
        <div class="blog-empty-state">
            <div class="empty-icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            </div>
            <h2 style="font-size:1.2rem;font-weight:700;color:#0f172a;margin-bottom:8px;">
                <?= !empty($searchQuery) ? 'No results found' : 'No posts yet' ?>
            </h2>
            <p style="font-size:0.9rem;color:#64748b;margin-bottom:20px;">
                <?= !empty($searchQuery)
                    ? 'Try a different keyword or browse all articles.'
                    : 'New articles are coming soon. Check back later.' ?>
            </p>
            <?php if (!empty($searchQuery)): ?>
            <a href="/blog" class="btn btn-outline btn-sm">Clear Search</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ─────────────────────────────────────────
     BOTTOM CTA BANNER
────────────────────────────────────────── -->
<section class="blog-cta-section">
    <div class="container">
        <div class="blog-cta-card">
            <div class="blog-cta-content">
                <h2>Need to share something sensitive right now?</h2>
                <p>Create a free AES-256 encrypted, self-destructing note in seconds — no sign-up, no logs, gone after one read.</p>
            </div>
            <a href="/create" class="btn btn-primary btn-lg btn-pill" id="blogCtaBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Create a Secure Note
            </a>
        </div>
    </div>
</section>
