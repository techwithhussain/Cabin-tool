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
            <h1 class="admin-page-title"><?= $blog ? 'Edit Blog Post' : 'Create New Blog Post' ?></h1>
            <div class="admin-topbar__right">
                <a href="/admin/blogs" class="btn btn-outline btn-sm">&larr; Back to Blogs</a>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-width: 900px;">
            <form method="POST" action="<?= $action ?>">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <!-- Title -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">Post Title *</label>
                    <input type="text" name="title" id="blogTitleInput" required value="<?= htmlspecialchars($blog?->title ?? '') ?>" placeholder="e.g. 5 Safest Ways to Share Passwords Online" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px;">
                </div>

                <!-- Slug -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">URL Slug (auto generated if empty)</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: #64748b; font-size: 14px;">/blog/</span>
                        <input type="text" name="slug" id="blogSlugInput" value="<?= htmlspecialchars($blog?->slug ?? '') ?>" placeholder="e.g. 5-safest-ways-to-share-passwords" style="flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    </div>
                </div>

                <!-- 2 Column: Category & Read Time -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">Category</label>
                        <select name="category" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: white;">
                            <?php
                            $cats = ['Security', 'Guides', 'Technology', 'Privacy', 'Tutorials', 'News'];
                            $currentCat = $blog?->category ?? 'Security';
                            foreach ($cats as $c):
                            ?>
                                <option value="<?= $c ?>" <?= $currentCat === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">Read Time</label>
                        <input type="text" name="read_time" value="<?= htmlspecialchars($blog?->readTime ?? '4 min read') ?>" placeholder="e.g. 4 min read" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">Status</label>
                        <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: white;">
                            <option value="published" <?= ($blog?->status ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="draft" <?= ($blog?->status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                </div>

                <!-- Short Summary / Excerpt -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">Summary / Excerpt (Shows on cards & SEO desc)</label>
                    <textarea name="summary" rows="2" placeholder="Brief summary of what this article covers..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: inherit;"><?= htmlspecialchars($blog?->summary ?? '') ?></textarea>
                </div>

                <!-- Main Content (HTML / Rich text) -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color: #1e293b;">Article Content (HTML supported: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;strong&gt;, etc.) *</label>
                    <textarea name="content" required rows="14" placeholder="Write your blog post content here in HTML or text..." style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: monospace; line-height: 1.6;"><?= htmlspecialchars($blog?->content ?? '') ?></textarea>
                </div>

                <!-- SEO Meta Fields Accordion / Section -->
                <details style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px;">
                    <summary style="font-weight: 600; color: #334155; cursor: pointer;">Advanced SEO Meta Settings (Optional)</summary>
                    <div style="margin-top: 16px;">
                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #475569;">Custom Meta Title</label>
                            <input type="text" name="meta_title" value="<?= htmlspecialchars($blog?->metaTitle ?? '') ?>" placeholder="Leave blank to use post title" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        </div>
                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #475569;">Custom Meta Description</label>
                            <input type="text" name="meta_description" value="<?= htmlspecialchars($blog?->metaDescription ?? '') ?>" placeholder="Leave blank to use excerpt" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: #475569;">Meta Keywords (comma separated)</label>
                            <input type="text" name="meta_keywords" value="<?= htmlspecialchars($blog?->metaKeywords ?? '') ?>" placeholder="e.g. self destructing notes, privnote alternative, privacy" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                        </div>
                    </div>
                </details>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="/admin/blogs" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg" style="padding: 10px 24px;">
                        <?= $blog ? 'Save Changes' : 'Publish Article' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto slug generation from title
const titleInput = document.getElementById('blogTitleInput');
const slugInput  = document.getElementById('blogSlugInput');
if (titleInput && slugInput && !slugInput.value) {
    titleInput.addEventListener('input', function() {
        slugInput.value = this.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .trim()
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    });
}
</script>
