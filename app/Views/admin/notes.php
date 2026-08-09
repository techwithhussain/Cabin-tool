<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar__brand">
            <div class="logo-icon logo-icon--sm"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/><path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <div><span class="admin-brand-name">Cabin</span><span class="admin-brand-sub">Admin Panel</span></div>
        </div>
        <nav class="admin-nav">
            <a href="/admin/dashboard" class="admin-nav__link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
            <a href="/admin/notes" class="admin-nav__link admin-nav__link--active"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Notes</a>
            <a href="/admin/logs" class="admin-nav__link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Audit Logs</a>
        </nav>
        <form method="POST" action="/admin/logout" class="admin-sidebar__footer"><input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><button type="submit" class="admin-logout-btn"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout</button></form>
    </aside>
    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title">All Notes</h1>
            <a href="/" class="btn btn-outline btn-sm" target="_blank">View Site</a>
        </div>
        <div class="admin-section">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Slug</th><th>Created</th><th>Expires</th><th>Views</th><th>Password</th><th>Burn</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($note['slug']) ?></code></td>
                            <td><?= htmlspecialchars(date('M j, Y H:i', strtotime($note['created_at']))) ?></td>
                            <td><?= $note['expires_at'] ? htmlspecialchars(date('M j H:i', strtotime($note['expires_at']))) : '–' ?></td>
                            <td><?= number_format((int)$note['view_count']) ?></td>
                            <td><?= $note['password_hash'] ?? false ? '✓' : '–' ?></td>
                            <td><?= $note['burn_after_read'] ? '✓' : '–' ?></td>
                            <td>
                                <?php if ($note['deleted_at']): ?><span class="badge badge--red">Deleted</span>
                                <?php elseif ($note['is_expired']): ?><span class="badge badge--orange">Expired</span>
                                <?php else: ?><span class="badge badge--green">Active</span><?php endif; ?>
                            </td>
                            <td>
                                <a href="/note/<?= htmlspecialchars($note['slug']) ?>" class="admin-action-link" target="_blank">View</a>
                                <?php if (!$note['deleted_at']): ?>
                                <form method="POST" action="/admin/note/<?= htmlspecialchars($note['slug']) ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this note permanently?')">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <button type="submit" class="admin-action-link admin-action-link--danger">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($notes)): ?><tr><td colspan="8" class="admin-empty">No notes found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
