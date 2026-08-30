<?php
$hasBlogs = !empty($blogs);
?>

<?php if (!$hasBlogs): ?>
<!-- ─────────────────────────────────────────
     COMING SOON SECTION
────────────────────────────────────────── -->
<section class="blog-coming-soon">
    <div class="container">
        <div class="coming-soon-card" data-animate="fade-up">
            <div class="coming-soon-badge">
                <span class="pulse-dot"></span>
                <span>Blog &bull; Coming Soon</span>
            </div>

            <h1 class="coming-soon-title">
                Privacy, Security &amp; Encryption <br>
                <span class="hero-title--accent">Insights Coming Soon</span>
            </h1>

            <p class="coming-soon-desc">
                We are currently crafting comprehensive guides, tutorials, and security deep-dives on zero-knowledge encryption, self-destructing notes, and keeping your confidential data private.
            </p>

            <!-- Feature Previews -->
            <div class="coming-soon-topics">
                <div class="topic-item">
                    <div class="topic-icon">🔐</div>
                    <div>
                        <h4>AES-256 Encryption Guides</h4>
                        <p>Learn how modern cryptography secures your data.</p>
                    </div>
                </div>
                <div class="topic-item">
                    <div class="topic-icon">⚡</div>
                    <div>
                        <h4>Self-Destructing Notes</h4>
                        <p>Best practices for sharing temporary, one-time passwords.</p>
                    </div>
                </div>
                <div class="topic-item">
                    <div class="topic-icon">🛡️</div>
                    <div>
                        <h4>Online Privacy &amp; Anonymity</h4>
                        <p>Prevent tracking, data leaks, and chat interception.</p>
                    </div>
                </div>
            </div>

            <div class="coming-soon-actions">
                <a href="/create" class="btn btn-primary btn-lg btn-pill">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Create a Secure Note
                </a>
                <a href="/" class="btn btn-outline btn-lg btn-pill">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</section>

<?php else: ?>

<!-- ─────────────────────────────────────────
     BLOG INDEX HERO (When posts are published)
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

<section class="blog-section">
    <div class="container">
        <!-- Categories tabs -->
        <?php if (!empty($categories)): ?>
        <div class="blog-categories-wrap" data-animate="fade-up">
            <a href="/blog" class="cat-pill <?= ($activeCategory === 'all' || empty($activeCategory)) ? 'cat-pill--active' : '' ?>">All Topics</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/blog?category=<?= urlencode($cat) ?>" class="cat-pill <?= ($activeCategory === $cat) ? 'cat-pill--active' : '' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

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
    </div>
</section>
<?php endif; ?>
