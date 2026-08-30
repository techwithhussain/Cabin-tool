<!-- ─────────────────────────────────────────
     BLOG INDEX HERO
────────────────────────────────────────── -->
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-inner" data-animate="fade-up">
            <div class="hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="currentColor"/></svg>
                Privacy, Security &amp; Encryption Blog
            </div>
            <h1 class="blog-hero-title">Insights on Privacy, Security &amp; <span class="hero-title--accent">Confidential Sharing</span></h1>
            <p class="blog-hero-desc">Explore expert guides, security tips, and tutorials on protecting your sensitive data, API keys, and private messages online.</p>

            <!-- Search & Filters -->
            <form action="/blog" method="GET" class="blog-search-bar">
                <div class="search-input-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="search" name="q" placeholder="Search guides, tutorials, encryption topics..." value="<?= htmlspecialchars($searchQuery) ?>" aria-label="Search blog posts">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
            </form>
        </div>
    </div>
</section>

<!-- ─────────────────────────────────────────
     CATEGORIES & POSTS
────────────────────────────────────────── -->
<section class="blog-section">
    <div class="container">
        <!-- Categories tabs -->
        <div class="blog-categories-wrap" data-animate="fade-up">
            <a href="/blog" class="cat-pill <?= ($activeCategory === 'all' || empty($activeCategory)) ? 'cat-pill--active' : '' ?>">All Topics</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/blog?category=<?= urlencode($cat) ?>" class="cat-pill <?= ($activeCategory === $cat) ? 'cat-pill--active' : '' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($blogs)): ?>
            <div class="blog-empty-state" data-animate="fade-up">
                <div class="empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3>No articles found</h3>
                <p>Try searching for other security topics or browse all categories.</p>
                <a href="/blog" class="btn btn-outline btn-sm">View All Articles</a>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($blogs as $i => $blog): ?>
                <article class="blog-card" data-animate="fade-up" data-delay="<?= ($i % 3) * 100 ?>">
                    <div class="blog-card__header">
                        <span class="blog-card__cat"><?= htmlspecialchars($blog->category) ?></span>
                        <span class="blog-card__read"><?= htmlspecialchars($blog->readTime) ?></span>
                    </div>

                    <h2 class="blog-card__title">
                        <a href="/blog/<?= htmlspecialchars($blog->slug) ?>">
                            <?= htmlspecialchars($blog->title) ?>
                        </a>
                    </h2>

                    <p class="blog-card__summary">
                        <?= htmlspecialchars($blog->summary) ?>
                    </p>

                    <div class="blog-card__footer">
                        <div class="blog-author-info">
                            <div class="blog-author-avatar">HL</div>
                            <div>
                                <span class="blog-author-name"><?= htmlspecialchars($blog->author) ?></span>
                                <span class="blog-date"><?= date('M d, Y', strtotime($blog->createdAt)) ?></span>
                            </div>
                        </div>
                        <a href="/blog/<?= htmlspecialchars($blog->slug) ?>" class="blog-read-link" aria-label="Read full article: <?= htmlspecialchars($blog->title) ?>">
                            Read &rarr;
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ─────────────────────────────────────────
     CTA BANNER
────────────────────────────────────────── -->
<section class="blog-cta-section">
    <div class="container">
        <div class="blog-cta-card" data-animate="fade-up">
            <div class="blog-cta-content">
                <h2>Protect your confidential data now</h2>
                <p>Send self-destructing passwords, sensitive notes, and confidential text with AES-256 encryption. Free and no signup needed.</p>
            </div>
            <a href="/create" class="btn btn-primary btn-lg">Create a Secure Note</a>
        </div>
    </div>
</section>
