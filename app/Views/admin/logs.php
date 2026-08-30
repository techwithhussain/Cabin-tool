<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar__brand"><div class="logo-icon logo-icon--sm"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/><path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div><span class="admin-brand-name">Cabin</span><span class="admin-brand-sub">Admin Panel</span></div></div>
        <nav class="admin-nav">
            <a href="/admin/dashboard" class="admin-nav__link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
            <a href="/admin/blogs" class="admin-nav__link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Manage Blogs</a>
            <a href="/admin/notes" class="admin-nav__link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Notes</a>
            <a href="/admin/logs" class="admin-nav__link admin-nav__link--active"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Audit Logs</a>
        </nav>
        <form method="POST" action="/admin/logout" class="admin-sidebar__footer"><input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><button type="submit" class="admin-logout-btn"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout</button></form>
    </aside>
    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title">Audit Logs</h1>
            <a href="/" class="btn btn-outline btn-sm" target="_blank">View Site</a>
        </div>
        <div class="admin-section">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Action</th><th>Note</th><th>IP Hash</th><th>User Agent</th><th>Time</th></tr></thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><span class="badge badge--<?= str_contains($log['action'], 'fail') || str_contains($log['action'], 'error') ? 'red' : (str_contains($log['action'], 'delete') ? 'orange' : 'blue') ?>"><?= htmlspecialchars($log['action']) ?></span></td>
                            <td><?= $log['note_slug'] ? '<code>' . htmlspecialchars($log['note_slug']) . '</code>' : '–' ?></td>
                            <td><code class="admin-hash"><?= htmlspecialchars(substr($log['ip_hash'], 0, 12)) ?>…</code></td>
                            <td class="admin-ua" title="<?= htmlspecialchars($log['user_agent'] ?? '') ?>"><?= htmlspecialchars(substr($log['user_agent'] ?? '–', 0, 40)) ?><?= strlen($log['user_agent'] ?? '') > 40 ? '…' : '' ?></td>
                            <td><?= htmlspecialchars(date('M j, Y H:i:s', strtotime($log['created_at']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?><tr><td colspan="5" class="admin-empty">No audit logs yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
