<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar__brand">
            <div class="logo-icon logo-icon--sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/><path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <span class="admin-brand-name">Cabin</span>
                <span class="admin-brand-sub">Admin Panel</span>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="/admin/dashboard" class="admin-nav__link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="/admin/blogs" class="admin-nav__link admin-nav__link--active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Manage Blogs
            </a>
            <a href="/admin/notes" class="admin-nav__link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Notes
            </a>
            <a href="/admin/logs" class="admin-nav__link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><line x1="8" y1="9" x2="10" y2="9"/></svg>
                Audit Logs
            </a>
            <a href="/cron/cleanup?key=<?= htmlspecialchars($_ENV['CRON_SECRET'] ?? '') ?>" class="admin-nav__link" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                Run Cleanup
            </a>
        </nav>

        <form method="POST" action="/admin/logout" class="admin-sidebar__footer">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <button type="submit" class="admin-logout-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </button>
        </form>
    </aside>

    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title">Manage Blog Posts</h1>
            <div class="admin-topbar__right">
                <a href="/admin/blogs/create" class="btn btn-primary btn-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    New Blog Post
                </a>
                <a href="/blog" class="btn btn-outline btn-sm" target="_blank">View Blog Page</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert-box <?= $message['type'] === 'success' ? 'alert-box--success' : 'alert-box--danger' ?>" style="margin-bottom: 20px; padding: 12px 18px; border-radius: 8px; font-weight: 500; <?= $message['type'] === 'success' ? 'background: #dcfce7; color: #166534;' : 'background: #fee2e2; color: #991b1b;' ?>">
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        <div class="admin-section">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Date</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogs)): ?>
                            <tr>
                                <td colspan="6" class="admin-empty">No blog posts created yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($blogs as $blog): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--color-text-primary); margin-bottom: 2px;"><?= htmlspecialchars($blog->title) ?></div>
                                    <div style="font-size: 12px; color: var(--color-text-muted); font-family: var(--font-mono);">/blog/<?= htmlspecialchars($blog->slug) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge--purple">
                                        <?= htmlspecialchars($blog->category) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($blog->isPublished()): ?>
                                        <span class="badge badge--green">Published</span>
                                    <?php else: ?>
                                        <span class="badge badge--orange">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 500;">
                                    <?= number_format($blog->views) ?>
                                </td>
                                <td style="color: var(--color-text-muted); font-size: 13px;">
                                    <?= date('M d, Y', strtotime($blog->createdAt)) ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 8px; align-items: center;">
                                        <a href="/blog/<?= htmlspecialchars($blog->slug) ?>" target="_blank" class="admin-action-link" title="View Public Post">View</a>
                                        <a href="/admin/blogs/edit/<?= $blog->id ?>" class="admin-action-link" title="Edit Post">Edit</a>
                                        <form method="POST" action="/admin/blogs/delete/<?= $blog->id ?>" onsubmit="return confirm('Are you sure you want to delete this blog post?');" style="display: inline;">
                                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button type="submit" class="admin-action-link admin-action-link--danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
