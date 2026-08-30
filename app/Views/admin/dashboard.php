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
            <a href="/admin/dashboard" class="admin-nav__link admin-nav__link--active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="/admin/blogs" class="admin-nav__link">
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

    <!-- Main -->
    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title">Dashboard</h1>
            <div class="admin-topbar__right">
                <span class="admin-topbar__time" id="adminTime"></span>
                <a href="/" class="btn btn-outline btn-sm" target="_blank">View Site</a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card admin-stat-card--blue">
                <div class="admin-stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="admin-stat-card__content">
                    <div class="admin-stat-card__number"><?= number_format((int)$stats['total']) ?></div>
                    <div class="admin-stat-card__label">Total Notes</div>
                </div>
            </div>
            <div class="admin-stat-card admin-stat-card--green">
                <div class="admin-stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="admin-stat-card__content">
                    <div class="admin-stat-card__number"><?= number_format((int)$stats['active']) ?></div>
                    <div class="admin-stat-card__label">Active Notes</div>
                </div>
            </div>
            <div class="admin-stat-card admin-stat-card--orange">
                <div class="admin-stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div class="admin-stat-card__content">
                    <div class="admin-stat-card__number"><?= number_format((int)$stats['expired']) ?></div>
                    <div class="admin-stat-card__label">Expired Notes</div>
                </div>
            </div>
            <div class="admin-stat-card admin-stat-card--purple">
                <div class="admin-stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
                <div class="admin-stat-card__content">
                    <div class="admin-stat-card__number"><?= htmlspecialchars($totalStorage) ?></div>
                    <div class="admin-stat-card__label">Storage Used</div>
                </div>
            </div>
        </div>

        <!-- Recent Notes -->
        <div class="admin-section">
            <div class="admin-section__header">
                <h2>Recent Notes</h2>
                <a href="/admin/notes" class="btn btn-ghost btn-sm">View All →</a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Slug</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentNotes as $note): ?>
                        <tr>
                            <td><code class="admin-slug"><?= htmlspecialchars($note['slug']) ?></code></td>
                            <td><?= htmlspecialchars(date('M j, Y H:i', strtotime($note['created_at']))) ?></td>
                            <td><?= $note['expires_at'] ? htmlspecialchars(date('M j H:i', strtotime($note['expires_at']))) : '<span class="badge badge--grey">Never</span>' ?></td>
                            <td><?= number_format((int)$note['view_count']) ?></td>
                            <td>
                                <?php if ($note['deleted_at']): ?>
                                <span class="badge badge--red">Deleted</span>
                                <?php elseif ($note['is_expired'] || ($note['expires_at'] && strtotime($note['expires_at']) < time())): ?>
                                <span class="badge badge--orange">Expired</span>
                                <?php else: ?>
                                <span class="badge badge--green">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/note/<?= htmlspecialchars($note['slug']) ?>" class="admin-action-link" target="_blank">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentNotes)): ?>
                        <tr><td colspan="6" class="admin-empty">No notes yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="admin-section">
            <div class="admin-section__header">
                <h2>Recent Audit Logs</h2>
                <a href="/admin/logs" class="btn btn-ghost btn-sm">View All →</a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Note</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><span class="badge badge--<?= str_contains($log['action'], 'fail') || str_contains($log['action'], 'error') || str_contains($log['action'], 'rate') ? 'red' : 'blue' ?>"><?= htmlspecialchars($log['action']) ?></span></td>
                            <td><?= $log['note_slug'] ? '<code>' . htmlspecialchars($log['note_slug']) . '</code>' : '–' ?></td>
                            <td><?= htmlspecialchars(date('M j H:i:s', strtotime($log['created_at']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="3" class="admin-empty">No logs yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
