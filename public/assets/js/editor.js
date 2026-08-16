/* ============================================================
   Cabin – Editor JS (Workspace & Note View)
   ============================================================ */

'use strict';

// ─────────────────────────────────────────────
// Character Counter
// ─────────────────────────────────────────────
(function initCharCounter() {
    const textarea = document.getElementById('noteContent');
    const counter  = document.getElementById('charCounter');
    if (!textarea || !counter) return;

    const maxLen   = parseInt(textarea.getAttribute('maxlength') || '50000');

    const update = () => {
        const len = textarea.value.length;
        counter.textContent = `${len.toLocaleString()} / ${maxLen.toLocaleString()}`;
        counter.classList.toggle('near-limit', len > maxLen * 0.85);
        counter.classList.toggle('at-limit',   len >= maxLen);
    };

    textarea.addEventListener('input', update);
    update();
})();

// ─────────────────────────────────────────────
// Auto-Save Draft to LocalStorage
// ─────────────────────────────────────────────
(function initDraftSave() {
    const textarea = document.getElementById('noteContent');
    const indicator = document.getElementById('draftIndicator');
    if (!textarea) return;

    const DRAFT_KEY = 'cabin_draft_v1';
    let   saveTimer = null;

    // Restore draft on load
    const saved = localStorage.getItem(DRAFT_KEY);
    if (saved && !textarea.value) {
        textarea.value = saved;
        // Trigger update events
        textarea.dispatchEvent(new Event('input'));
    }

    const saveDraft = () => {
        localStorage.setItem(DRAFT_KEY, textarea.value);
        if (indicator) {
            indicator.style.display = 'flex';
            setTimeout(() => indicator.style.display = 'none', 2000);
        }
    };

    textarea.addEventListener('input', () => {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(saveDraft, 1500);
    });
})();

// ─────────────────────────────────────────────
// Image Upload & Dropzone
// ─────────────────────────────────────────────
(function initImageUpload() {
    const dropzone     = document.getElementById('dropzone');
    const imageInput   = document.getElementById('imageInput');
    const uploadList   = document.getElementById('uploadList');
    const imageStrip   = document.getElementById('imageStrip');
    const stripInner   = document.getElementById('imageStripInner');
    const progressWrap = document.getElementById('uploadProgress');
    const progressBar  = document.getElementById('uploadProgressBar');
    const uploadSession = document.getElementById('uploadSession')?.value || '';

    if (!dropzone || !imageInput) return;

    let uploadedFiles = []; // [{filename, original_name, url}]

    // Click to open file picker
    dropzone.addEventListener('click', () => imageInput.click());
    dropzone.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') imageInput.click();
    });

    // Drag and drop
    dropzone.addEventListener('dragover', e => {
        e.preventDefault();
        dropzone.classList.add('drag-over');
    });

    ['dragleave', 'dragend', 'drop'].forEach(event => {
        dropzone.addEventListener(event, () => dropzone.classList.remove('drag-over'));
    });

    dropzone.addEventListener('drop', e => {
        e.preventDefault();
        const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
        handleFiles(files);
    });

    imageInput.addEventListener('change', () => {
        handleFiles(Array.from(imageInput.files));
        imageInput.value = '';
    });

    async function handleFiles(files) {
        const MAX_IMAGES = parseInt(dropzone.closest('[data-max]')?.dataset.max || '5');

        for (const file of files) {
            if (uploadedFiles.length >= MAX_IMAGES) {
                Cabin.toast(`Maximum ${MAX_IMAGES} images allowed.`, 'error');
                break;
            }

            await uploadFile(file);
        }
    }

    async function uploadFile(file) {
        const itemEl = addUploadItem(file);
        const previewUrl = URL.createObjectURL(file);
        if (progressWrap) progressWrap.style.display = 'block';
        if (progressBar)  progressBar.style.width    = '0%';

        try {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('upload_session', uploadSession);
            formData.append('_csrf_token', Cabin.csrfToken());

            // Simulate progress with XHR for better UX
            const result = await uploadWithProgress(formData, (pct) => {
                if (progressBar) progressBar.style.width = `${pct}%`;
            });

            if (result.success) {
                const data = result.data;
                data.previewUrl = data.url || previewUrl;
                uploadedFiles.push(data);
                updateUploadItem(itemEl, 'ok', data.size || '–');
                addToImageStrip(data, uploadedFiles.length - 1);
            } else {
                updateUploadItem(itemEl, 'error', result.message || 'Upload failed');
                Cabin.toast(result.message || 'Upload failed', 'error');
            }
        } catch (err) {
            updateUploadItem(itemEl, 'error', 'Upload failed');
            Cabin.toast('Image upload failed. Please try again.', 'error');
        } finally {
            if (progressWrap) progressWrap.style.display = 'none';
        }
    }

    function uploadWithProgress(formData, onProgress) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', e => {
                if (e.lengthComputable) {
                    onProgress(Math.round(e.loaded / e.total * 100));
                }
            });

            xhr.addEventListener('load', () => {
                try {
                    resolve(JSON.parse(xhr.responseText));
                } catch {
                    reject(new Error('Invalid response'));
                }
            });

            xhr.addEventListener('error', reject);

            xhr.open('POST', '/image/upload');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-Token', Cabin.csrfToken());
            xhr.send(formData);
        });
    }

    function addUploadItem(file) {
        const item = document.createElement('div');
        item.className = 'upload-item';
        item.innerHTML = `
            <img class="upload-item__thumb" src="${URL.createObjectURL(file)}" alt="">
            <div class="upload-item__info">
                <div class="upload-item__name">${escHtml(file.name)}</div>
                <div class="upload-item__size">${formatBytes(file.size)}</div>
            </div>
            <span class="upload-item__status upload-item__status--info">Uploading…</span>
        `;
        if (uploadList) uploadList.appendChild(item);
        return item;
    }

    function updateUploadItem(item, status, label) {
        const statusEl = item.querySelector('.upload-item__status');
        if (statusEl) {
            statusEl.className = `upload-item__status upload-item__status--${status}`;
            statusEl.textContent = status === 'ok' ? `✓ ${label}` : `✗ ${label}`;
        }
    }

    function addToImageStrip(data, index) {
        if (!imageStrip || !stripInner) return;
        imageStrip.style.display = 'block';

        const thumb = document.createElement('div');
        thumb.className = 'image-strip-thumb';
        const imgSrc = data.previewUrl || data.url || '';
        thumb.innerHTML = `
            <img src="${escHtml(imgSrc)}" alt="${escHtml(data.original_name || '')}">
            <button class="image-strip-thumb__remove" data-index="${index}" aria-label="Remove image">✕</button>
        `;

        thumb.querySelector('.image-strip-thumb__remove').addEventListener('click', () => {
            uploadedFiles.splice(index, 1);
            thumb.remove();
            if (stripInner.children.length === 0) imageStrip.style.display = 'none';
        });

        stripInner.appendChild(thumb);
    }

    // Expose for use in note creation
    window.CabinUploader = {
        getFiles: () => uploadedFiles,
        clear: () => {
            uploadedFiles = [];
            if (uploadList)  uploadList.innerHTML = '';
            if (stripInner)  stripInner.innerHTML = '';
            if (imageStrip)  imageStrip.style.display = 'none';
        },
    };
})();

// ─────────────────────────────────────────────
// Create Note & Live Real-Time Auto-Save (like Crumple.me)
// ─────────────────────────────────────────────
(function initCreateNote() {
    const createBtn      = document.getElementById('createNoteBtn');
    const successModal   = document.getElementById('successModal');
    const noteUrlInput   = document.getElementById('noteUrl');
    const copyUrlBtn     = document.getElementById('copyUrlBtn');
    const viewNoteLink   = document.getElementById('viewNoteLink');
    const createAnother  = document.getElementById('createAnotherBtn');
    const metaDisplay    = document.getElementById('noteMetaDisplay');
    const ownerDisplay   = document.getElementById('ownerTokenDisplay');
    const copyTokenBtn   = document.getElementById('copyTokenBtn');
    const noteTextarea   = document.getElementById('noteContent');
    const saveStatus     = document.getElementById('saveStatus');

    if (!noteTextarea) return;

    // Detect initial custom slug if pre-filled in URL (e.g. cabinn.in/5554)
    let currentSlug       = document.getElementById('customSlug')?.value?.trim() || '';
    let isNoteCreated     = false;
    let isSaving          = false;
    let pendingSave       = false;
    let lastSavedContent  = '';
    let autoSaveTimer     = null;
    let createdNoteData   = null;

    function updateSaveStatus(status) {
        if (!saveStatus) return;
        if (status === 'saving') {
            saveStatus.style.display = 'inline-flex';
            saveStatus.style.color   = '#4f5fff';
            saveStatus.style.background = 'rgba(79, 95, 255, 0.1)';
            saveStatus.textContent = '● Saving...';
        } else if (status === 'saved') {
            saveStatus.style.display = 'inline-flex';
            saveStatus.style.color   = '#059669';
            saveStatus.style.background = 'rgba(5, 150, 105, 0.1)';
            saveStatus.textContent = '✓ Saved';
            setTimeout(() => {
                if (saveStatus.textContent === '✓ Saved') {
                    saveStatus.style.display = 'none';
                }
            }, 2000);
        } else if (status === 'error') {
            saveStatus.style.display = 'inline-flex';
            saveStatus.style.color   = '#dc2626';
            saveStatus.style.background = 'rgba(220, 38, 38, 0.1)';
            saveStatus.textContent = '⚠️ Auto-save failed';
        }
    }

    async function performSave(isManualClick = false) {
        const content = noteTextarea.value.trim();
        if (!content) return;
        if (isSaving) {
            pendingSave = true;
            return;
        }
        if (!isManualClick && content === lastSavedContent) return;

        isSaving = true;
        pendingSave = false;
        updateSaveStatus('saving');

        const expiry       = document.getElementById('expirySelect')?.value || '24h';
        const password     = document.getElementById('notePassword')?.value || '';
        const burnAfterRead = document.getElementById('burnAfterRead')?.checked || false;
        const uploadSession = document.getElementById('uploadSession')?.value || '';

        try {
            if (!isNoteCreated) {
                // Initial Note Creation
                const formData = new URLSearchParams();
                formData.append('_csrf_token', Cabin.csrfToken());
                formData.append('content', content);
                formData.append('expiry', expiry);
                if (currentSlug)   formData.append('custom_slug', currentSlug);
                if (password)      formData.append('password', password);
                if (burnAfterRead) formData.append('burn_after_read', '1');
                if (uploadSession) formData.append('upload_session', uploadSession);

                const result = await Cabin.fetch('/note', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString(),
                });

                if (result.success && result.data) {
                    createdNoteData = result.data;
                    currentSlug     = result.data.slug;
                    isNoteCreated   = true;
                    lastSavedContent = content;

                    // Seamlessly update browser URL without refresh (e.g. cabinn.in/5554)
                    if (window.location.pathname !== '/' + currentSlug) {
                        window.history.replaceState(null, '', '/' + currentSlug);
                    }

                    // Update custom slug field & note URL input
                    const slugInput = document.getElementById('customSlug');
                    if (slugInput) slugInput.value = currentSlug;
                    if (noteUrlInput) noteUrlInput.value = result.data.url;
                    if (viewNoteLink) viewNoteLink.href  = result.data.url;

                    updateSaveStatus('saved');
                    localStorage.removeItem('cabin_draft_v1');

                    if (isManualClick) {
                        showSuccessModal(result.data, password, burnAfterRead);
                    }
                } else {
                    updateSaveStatus('error');
                    if (isManualClick) Cabin.toast(result.message || 'Failed to save note.', 'error');
                }
            } else {
                // Subsequent real-time content updates
                const formData = new URLSearchParams();
                formData.append('_csrf_token', Cabin.csrfToken());
                formData.append('content', content);

                const result = await Cabin.fetch(`/note/${currentSlug}/update`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString(),
                });

                if (result.success) {
                    lastSavedContent = content;
                    updateSaveStatus('saved');
                    if (isManualClick && createdNoteData) {
                        showSuccessModal(createdNoteData, password, burnAfterRead);
                    }
                } else {
                    updateSaveStatus('error');
                    if (isManualClick) Cabin.toast(result.message || 'Failed to update note.', 'error');
                }
            }
        } catch (err) {
            updateSaveStatus('error');
            if (isManualClick) Cabin.toast('Failed to save note. Please try again.', 'error');
        } finally {
            isSaving = false;
            if (pendingSave && noteTextarea.value.trim() !== lastSavedContent) {
                performSave(false);
            }
        }
    }

    function showSuccessModal(data, password, burnAfterRead) {
        if (!successModal) return;
        const { url, owner_token, expiry_label } = data;

        if (noteUrlInput)  noteUrlInput.value = url;
        if (viewNoteLink)  viewNoteLink.href  = url;

        if (metaDisplay) {
            const badges = [];
            badges.push(`<span class="badge badge--blue">🔒 Encrypted</span>`);
            if (expiry_label !== 'Never') badges.push(`<span class="badge badge--orange">⏱ ${expiry_label}</span>`);
            if (password)     badges.push(`<span class="badge badge--purple">🔑 Password</span>`);
            if (burnAfterRead) badges.push(`<span class="badge badge--red">🔥 Burn After Read</span>`);
            metaDisplay.innerHTML = badges.join('');
        }

        successModal.style.display = 'flex';
    }

    // Debounced Real-Time Auto-Save on typing (fast 500ms debounce)
    noteTextarea.addEventListener('input', () => {
        clearTimeout(autoSaveTimer);
        const len = noteTextarea.value.trim().length;
        if (len > 0) {
            autoSaveTimer = setTimeout(() => {
                performSave(false);
            }, 500);
        }
    });

    // Manual Save / Share Button Click
    if (createBtn) {
        createBtn.addEventListener('click', async () => {
            const content = noteTextarea.value.trim();
            if (!content) {
                Cabin.toast('Please write something in your note.', 'error');
                noteTextarea.focus();
                return;
            }

            setLoading(true);
            await performSave(true);
            setLoading(false);
        });
    }

    // Copy URL button
    if (copyUrlBtn && noteUrlInput) {
        copyUrlBtn.addEventListener('click', async () => {
            const ok = await Cabin.copyToClipboard(noteUrlInput.value);
            if (ok) {
                copyUrlBtn.textContent = '✓ Copied!';
                setTimeout(() => copyUrlBtn.textContent = 'Copy', 2000);
                Cabin.toast('URL copied to clipboard!', 'success');
            }
        });
    }

    // Copy owner token
    if (copyTokenBtn && ownerDisplay) {
        copyTokenBtn.addEventListener('click', async () => {
            await Cabin.copyToClipboard(ownerDisplay.textContent);
            Cabin.toast('Owner token copied!', 'success');
        });
    }

    // Create another
    if (createAnother && successModal) {
        createAnother.addEventListener('click', () => {
            successModal.style.display = 'none';
            noteTextarea.value = '';
            document.getElementById('notePassword').value = '';
            document.getElementById('burnAfterRead').checked = false;
            document.getElementById('expirySelect').value = '24h';
            const slugInput = document.getElementById('customSlug');
            if (slugInput) slugInput.value = '';
            currentSlug = '';
            isNoteCreated = false;
            lastSavedContent = '';
            createdNoteData = null;
            window.history.replaceState(null, '', '/create');
            window.CabinUploader?.clear();
            noteTextarea.focus();
            noteTextarea.dispatchEvent(new Event('input'));
        });
    }

    // Close modal on overlay click
    if (successModal) {
        successModal.addEventListener('click', e => {
            if (e.target === successModal) successModal.style.display = 'none';
        });
    }

    function setLoading(loading) {
        const btnText    = document.getElementById('createBtnText');
        const btnLoading = document.getElementById('createBtnLoading');
        if (createBtn) createBtn.disabled = loading;
        if (btnText)    btnText.style.display    = loading ? 'none' : 'inline-flex';
        if (btnLoading) btnLoading.style.display = loading ? 'inline-flex' : 'none';
    }
})();

// ─────────────────────────────────────────────
// Password Verification (Note View)
// ─────────────────────────────────────────────
(function initPasswordVerify() {
    const form    = document.getElementById('passwordForm');
    const btn     = document.getElementById('verifyBtn');
    const errorEl = document.getElementById('passwordError');
    const slug    = window.CABIN_SLUG;

    if (!form || !slug) return;

    form.addEventListener('submit', async e => {
        e.preventDefault();

        const password = document.getElementById('notePasswordInput')?.value;
        if (!password) return;

        // Loading state
        btn.disabled = true;
        document.getElementById('verifyBtnText').style.display    = 'none';
        document.getElementById('verifyBtnLoading').style.display = 'inline-flex';
        if (errorEl) errorEl.style.display = 'none';

        try {
            const formData = new URLSearchParams();
            formData.append('_csrf_token', Cabin.csrfToken());
            formData.append('password', password);

            const result = await Cabin.fetch(`/note/${slug}/verify`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString(),
            });

            if (result.success) {
                Cabin.toast('Access granted! Loading note…', 'success');
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (err) {
            const msg = err?.message || 'Incorrect password.';
            if (errorEl) {
                errorEl.textContent    = msg;
                errorEl.style.display  = 'block';
            }
            document.getElementById('notePasswordInput')?.select();
        } finally {
            btn.disabled = false;
            document.getElementById('verifyBtnText').style.display    = 'inline-flex';
            document.getElementById('verifyBtnLoading').style.display = 'none';
        }
    });
})();

// ─────────────────────────────────────────────
// Countdown Timer
// ─────────────────────────────────────────────
(function initCountdown() {
    const display = document.getElementById('countdownDisplay');
    if (!display || !window.CABIN_COUNTDOWN) return;

    let remaining = parseInt(window.CABIN_COUNTDOWN);

    const format = (seconds) => {
        if (seconds <= 0) return 'Expired';
        const d = Math.floor(seconds / 86400);
        const h = Math.floor((seconds % 86400) / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;

        if (d > 0) return `${d}d ${h}h ${m}m`;
        if (h > 0) return `${h}h ${m}m ${s}s`;
        return `${m}m ${s}s`;
    };

    const tick = () => {
        if (remaining <= 0) {
            display.textContent = 'Expired';
            document.getElementById('expiryBanner')?.classList.add('expired');
            return;
        }
        display.textContent = format(remaining);
        remaining--;
    };

    tick();
    setInterval(tick, 1000);
})();

// ─────────────────────────────────────────────
// Note View Actions
// ─────────────────────────────────────────────
(function initNoteActions() {
    // Copy content
    const copyContentBtn = document.getElementById('copyContentBtn');
    if (copyContentBtn) {
        copyContentBtn.addEventListener('click', async () => {
            const content = window.CABIN_NOTE_CONTENT || '';
            const ok = await Cabin.copyToClipboard(content);
            if (ok) {
                copyContentBtn.classList.add('copied');
                copyContentBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Copied!
                `;
                Cabin.toast('Note text copied!', 'success');
                setTimeout(() => {
                    copyContentBtn.classList.remove('copied');
                    copyContentBtn.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
                        </svg>
                        Copy Text
                    `;
                }, 2500);
            }
        });
    }

    // Copy link
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', async () => {
            const url = copyLinkBtn.dataset.url || window.location.href;
            const ok  = await Cabin.copyToClipboard(url);
            if (ok) Cabin.toast('Link copied to clipboard!', 'success');
        });
    }

    // Web Share API
    const shareBtn = document.getElementById('shareNoteBtn');
    if (shareBtn) {
        if (navigator.share) {
            shareBtn.addEventListener('click', async () => {
                try {
                    await navigator.share({
                        title: 'Secure Note',
                        text:  'I shared a secure note with you via Cabin.',
                        url:   shareBtn.dataset.url || window.location.href,
                    });
                } catch {
                    // User cancelled
                }
            });
        } else {
            shareBtn.addEventListener('click', async () => {
                const url = shareBtn.dataset.url || window.location.href;
                const ok  = await Cabin.copyToClipboard(url);
                if (ok) Cabin.toast('Link copied to clipboard!', 'success');
            });
        }
    }

    // Inline Edit Note Handler
    const editBtn       = document.getElementById('editNoteBtn');
    const editBox       = document.getElementById('noteEditBox');
    const editTextarea  = document.getElementById('noteEditTextarea');
    const saveEditBtn   = document.getElementById('saveNoteEditBtn');
    const cancelEditBtn = document.getElementById('cancelNoteEditBtn');
    const contentDisplay = document.getElementById('noteContent');

    if (editBtn && editBox && editTextarea && saveEditBtn && cancelEditBtn && contentDisplay) {
        editBtn.addEventListener('click', () => {
            const currentText = window.CABIN_NOTE_CONTENT || contentDisplay.innerText.trim();
            editTextarea.value = currentText;
            contentDisplay.style.display = 'none';
            editBox.style.display = 'block';
            editTextarea.focus();
        });

        cancelEditBtn.addEventListener('click', () => {
            editBox.style.display = 'none';
            contentDisplay.style.display = 'block';
        });

        saveEditBtn.addEventListener('click', async () => {
            const updatedContent = editTextarea.value.trim();
            if (!updatedContent) {
                Cabin.toast('Note content cannot be empty.', 'error');
                return;
            }

            saveEditBtn.disabled = true;
            saveEditBtn.textContent = 'Saving...';

            try {
                const pathParts = window.location.pathname.split('/');
                const slug = pathParts[pathParts.length - 1];

                const formData = new URLSearchParams();
                formData.append('_csrf_token', Cabin.csrfToken());
                formData.append('content', updatedContent);

                const res = await Cabin.fetch(`/note/${slug}/update`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString(),
                });

                if (res.success) {
                    window.CABIN_NOTE_CONTENT = updatedContent;
                    contentDisplay.innerHTML = escHtml(updatedContent).replace(/\n/g, '<br>');
                    editBox.style.display = 'none';
                    contentDisplay.style.display = 'block';
                    Cabin.toast('Note updated successfully!', 'success');
                } else {
                    Cabin.toast(res.message || 'Failed to update note.', 'error');
                }
            } catch (err) {
                Cabin.toast('Error saving changes. Please try again.', 'error');
            } finally {
                saveEditBtn.disabled = false;
                saveEditBtn.textContent = 'Save Changes';
            }
        });
    }

    // Image download buttons
    document.querySelectorAll('.gallery-download-btn').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.stopPropagation();
            const src  = btn.dataset.src;
            const name = btn.dataset.name || 'image';
            const a    = document.createElement('a');
            a.href     = src;
            a.download = name;
            a.click();
        });
    });

    // Lightbox
    const lightbox    = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxClose = document.getElementById('lightboxClose');

    if (lightbox && lightboxImg) {
        document.querySelectorAll('.gallery-item[data-lightbox]').forEach(item => {
            item.addEventListener('click', () => {
                const img = item.querySelector('img');
                if (!img) return;
                lightboxImg.src = img.src;
                lightboxImg.alt = img.alt;
                lightbox.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        const closeLightbox = () => {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        };

        lightboxClose?.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', e => {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeLightbox();
        });
    }
})();

// ─────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────
function formatBytes(bytes) {
    if (bytes < 1024)    return `${bytes} B`;
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / 1048576).toFixed(2)} MB`;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}
