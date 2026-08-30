<?php
$appUrl = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');
$canonicalUrl = $appUrl . '/blog/' . htmlspecialchars($blog->slug);
?>
<!-- Article JSON-LD Schema for Google Rich Snippet -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": <?= json_encode($blog->title) ?>,
    "description": <?= json_encode($blog->summary) ?>,
    "datePublished": "<?= date('c', strtotime($blog->createdAt)) ?>",
    "dateModified": "<?= date('c', strtotime($blog->updatedAt)) ?>",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": <?= json_encode($canonicalUrl) ?>
    },
    "author": {
        "@type": "Person",
        "name": <?= json_encode($blog->author) ?>,
        "url": "https://techwithhussain.online/"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Tech With Hussain",
        "logo": {
            "@type": "ImageObject",
            "url": "<?= $appUrl ?>/assets/img/favicon.svg"
        }
    }
}
</script>

<article class="single-article">
    <!-- Header -->
    <header class="article-header">
        <div class="container container--narrow">
            <div class="article-meta-top">
                <a href="/blog?category=<?= urlencode($blog->category) ?>" class="article-cat-badge"><?= htmlspecialchars($blog->category) ?></a>
                <span class="article-read-time"><?= htmlspecialchars($blog->readTime) ?></span>
                <span class="article-pub-date"><?= date('F j, Y', strtotime($blog->createdAt)) ?></span>
            </div>

            <h1 class="article-title"><?= htmlspecialchars($blog->title) ?></h1>

            <p class="article-lead"><?= htmlspecialchars($blog->summary) ?></p>

            <div class="article-author-bar">
                <div class="author-left">
                    <div class="author-avatar-lg">HL</div>
                    <div>
                        <div class="author-name-lg"><?= htmlspecialchars($blog->author) ?></div>
                        <div class="author-sub-text">Security &amp; Web Developer &bull; Creator of Cabin</div>
                    </div>
                </div>

                <!-- Quick Share Links -->
                <div class="article-share-links">
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($blog->title) ?>&url=<?= urlencode($canonicalUrl) ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-btn--twitter" title="Share on X / Twitter" aria-label="Share on Twitter">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($canonicalUrl) ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-btn--linkedin" title="Share on LinkedIn" aria-label="Share on LinkedIn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    </a>
                    <button type="button" class="share-btn share-btn--copy" id="copyArticleBtn" title="Copy Link" aria-label="Copy Article Link" onclick="navigator.clipboard.writeText('<?= $canonicalUrl ?>'); this.innerText = 'Copied!'; setTimeout(() => this.innerHTML = '<svg width=\'15\' height=\'15\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\'><rect x=\'9\' y=\'9\' width=\'13\' height=\'13\' rx=\'2\'/><path d=\'M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1\'/></svg>', 2000);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="article-body-wrapper">
        <div class="container container--narrow">
            <div class="article-content rich-text">
                <?= $blog->content ?>
            </div>

            <!-- In-Article Promo Card -->
            <div class="article-inline-tool-card">
                <div class="tool-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="#8B5CF6"/></svg>
                </div>
                <div class="tool-card-text">
                    <h4>Need to share a sensitive note or password securely?</h4>
                    <p>Use Cabin to create AES-256 encrypted, self-destructing notes. No sign up required.</p>
                </div>
                <a href="/create" class="btn btn-primary btn-sm">Create Note Free</a>
            </div>

            <!-- Author Bio Section -->
            <div class="article-author-bio">
                <div class="bio-avatar">HL</div>
                <div class="bio-content">
                    <h3>About the Author &mdash; Hussain Lone</h3>
                    <p>Hussain Lone is a Web Developer, Cybersecurity enthusiast, and the creator of Cabin. He writes practical guides on web security, encryption, and privacy architectures.</p>
                    <div class="bio-links">
                        <a href="https://techwithhussain.online/" target="_blank" rel="noopener noreferrer">Portfolio &rarr;</a>
                        <a href="https://github.com/techwithhussain" target="_blank" rel="noopener noreferrer">GitHub &rarr;</a>
                        <a href="/about">About Cabin &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- ─────────────────────────────────────────
     RELATED ARTICLES
────────────────────────────────────────── -->
<?php if (!empty($recentPosts)): ?>
<section class="related-posts-section">
    <div class="container">
        <h2 class="related-title">More Privacy &amp; Security Guides</h2>
        <div class="blog-grid">
            <?php foreach ($recentPosts as $rBlog): ?>
            <article class="blog-card">
                <div class="blog-card__header">
                    <span class="blog-card__cat"><?= htmlspecialchars($rBlog->category) ?></span>
                    <span class="blog-card__read"><?= htmlspecialchars($rBlog->readTime) ?></span>
                </div>
                <h3 class="blog-card__title">
                    <a href="/blog/<?= htmlspecialchars($rBlog->slug) ?>"><?= htmlspecialchars($rBlog->title) ?></a>
                </h3>
                <p class="blog-card__summary"><?= htmlspecialchars($rBlog->summary) ?></p>
                <div class="blog-card__footer">
                    <span class="blog-date"><?= date('M d, Y', strtotime($rBlog->createdAt)) ?></span>
                    <a href="/blog/<?= htmlspecialchars($rBlog->slug) ?>" class="blog-read-link">Read &rarr;</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
