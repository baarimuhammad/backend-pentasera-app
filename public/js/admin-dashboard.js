/**
 * Admin Dashboard — JavaScript
 * Fetches stats, pending events, and handles approve/reject actions.
 * public/js/admin-dashboard.js
 */
(function () {
    'use strict';

    // ─── Auth Guard ───
    if (typeof requireAuth === 'function') requireAuth();

    const user = typeof getUser === 'function' ? getUser() : null;
    if (!user || user.role !== 'admin') {
        window.location.href = '/';
        return;
    }

    // ─── Set admin user info in sidebar ───
    const avatarEl = document.getElementById('admin-avatar-letter');
    const nameEl = document.getElementById('admin-user-name');
    if (avatarEl && user.nama) avatarEl.textContent = user.nama.charAt(0).toUpperCase();
    if (nameEl && user.nama) nameEl.textContent = user.nama;

    // ─── Date display ───
    const dateEl = document.getElementById('admin-date-display');
    if (dateEl) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateEl.textContent = now.toLocaleDateString('id-ID', options);
    }

    // ─── Toast helper ───
    function showToast(message, type = 'success') {
        const container = document.getElementById('admin-toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `admin-toast ${type}`;
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ─── Fetch Stats ───
    async function loadStats() {
        try {
            const res = await apiGet('/admin/stats');
            if (!res || !res._ok) {
                console.error('Failed to load admin stats:', res?.message);
                return;
            }

            const d = res.data;

            const totalUsersEl = document.getElementById('stat-total-users');
            const usersDetailEl = document.getElementById('stat-users-detail');
            const totalEventsEl = document.getElementById('stat-total-events');
            const eventsDetailEl = document.getElementById('stat-events-detail');
            const pendingEl = document.getElementById('stat-pending');
            const revenueEl = document.getElementById('stat-revenue');
            const transDetailEl = document.getElementById('stat-transactions-detail');

            if (totalUsersEl) totalUsersEl.textContent = d.total_users ?? 0;
            if (usersDetailEl) usersDetailEl.textContent = `${d.total_creators ?? 0} creator · ${d.total_buyers ?? 0} buyer`;
            if (totalEventsEl) totalEventsEl.textContent = d.total_events ?? 0;
            if (eventsDetailEl) eventsDetailEl.textContent = `${d.published_events ?? 0} published`;
            if (pendingEl) pendingEl.textContent = d.pending_approval ?? 0;
            if (revenueEl) revenueEl.textContent = d.revenue_formatted ?? 'Rp 0';
            if (transDetailEl) transDetailEl.textContent = `${d.total_transactions ?? 0} transaksi`;

        } catch (err) {
            console.error('Error loading stats:', err);
        }
    }

    // ─── Fetch & Render Pending Events ───
    async function loadPendingEvents() {
        const loadingEl = document.getElementById('pending-loading');
        const tableEl = document.getElementById('pending-events-table');
        const bodyEl = document.getElementById('pending-events-body');
        const emptyEl = document.getElementById('pending-empty');
        const countBadge = document.getElementById('pending-count-badge');
        const navBadge = document.getElementById('nav-pending-badge');

        try {
            const res = await apiGet('/admin/pending-events');
            if (!res || !res._ok) {
                console.error('Failed to load pending events:', res?.message);
                if (loadingEl) loadingEl.style.display = 'none';
                return;
            }

            const events = res.data || [];

            // Hide loading
            if (loadingEl) loadingEl.style.display = 'none';

            // Update badge counts
            if (countBadge) {
                countBadge.textContent = events.length;
                countBadge.style.display = events.length > 0 ? 'inline' : 'none';
            }
            if (navBadge) {
                navBadge.textContent = events.length;
                navBadge.style.display = events.length > 0 ? 'inline' : 'none';
            }

            if (events.length === 0) {
                if (tableEl) tableEl.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }

            // Show table
            if (tableEl) tableEl.style.display = 'table';
            if (emptyEl) emptyEl.style.display = 'none';

            // Render rows
            if (bodyEl) {
                bodyEl.innerHTML = events.map(ev => {
                    const eventDate = ev.event_datetime
                        ? new Date(ev.event_datetime).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                        : '-';

                    const submittedDate = ev.created_at
                        ? new Date(ev.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
                        : '-';

                    const thumbHtml = ev.image_src
                        ? `<img class="event-thumb" src="${ev.image_src}" alt="${ev.nama_event}" onerror="this.src='/assets/hero-banner.jpg'">`
                        : `<div class="event-thumb-placeholder"><i data-lucide="image" class="w-5 h-5"></i></div>`;

                    return `
                        <tr data-event-id="${ev.id}">
                            <td>
                                <div class="event-info">
                                    ${thumbHtml}
                                    <div>
                                        <a href="/admin/event-review/${ev.id}" class="event-name" style="color: var(--admin-accent); text-decoration: none; cursor: pointer;" title="Lihat detail event">${escapeHtml(ev.nama_event)}</a>
                                        <div class="event-creator">${escapeHtml(ev.creator_name)} · ${escapeHtml(ev.organizer_name)}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="event-category">${escapeHtml(ev.kategori_event || 'Umum')}</span>
                            </td>
                            <td>
                                <span class="event-meta">${eventDate}</span>
                            </td>
                            <td>
                                <span class="event-meta">${ev.total_tickets ?? 0} tipe · ${ev.total_capacity ?? 0} kuota</span>
                            </td>
                            <td>
                                <span class="event-meta">${submittedDate}</span>
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <a href="/admin/event-review/${ev.id}" class="btn-approve" title="Review Detail" style="text-decoration: none;">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Review
                                    </a>
                                    <button class="btn-approve" onclick="approveEvent(${ev.id})" title="Setujui">
                                        <i data-lucide="check" class="w-4 h-4"></i> Approve
                                    </button>
                                    <button class="btn-reject" onclick="openRejectModal(${ev.id})" title="Tolak">
                                        <i data-lucide="x" class="w-4 h-4"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                // Re-init lucide icons for dynamic elements
                if (window.lucide) window.lucide.createIcons();
            }

        } catch (err) {
            console.error('Error loading pending events:', err);
            if (loadingEl) loadingEl.style.display = 'none';
        }
    }

    // ─── Approve Event ───
    window.approveEvent = async function (eventId) {
        const btn = document.querySelector(`tr[data-event-id="${eventId}"] .btn-approve`);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> ...';
        }

        try {
            const res = await apiPost(`/admin/events/${eventId}/approve`);
            if (res && res._ok) {
                showToast('Event berhasil di-approve dan dipublikasikan!', 'success');
                // Reload data
                await Promise.all([loadStats(), loadPendingEvents()]);
            } else {
                showToast(res?.message || 'Gagal approve event', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Approve';
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        } catch (err) {
            console.error('Approve error:', err);
            showToast('Gagal menghubungi server', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Approve';
                if (window.lucide) window.lucide.createIcons();
            }
        }
    };

    // ─── Reject Modal ───
    window.openRejectModal = function (eventId) {
        const modal = document.getElementById('reject-modal');
        const input = document.getElementById('reject-event-id');
        const reason = document.getElementById('reject-reason');

        if (input) input.value = eventId;
        if (reason) reason.value = '';
        if (modal) modal.classList.add('active');
    };

    window.closeRejectModal = function () {
        const modal = document.getElementById('reject-modal');
        if (modal) modal.classList.remove('active');
    };

    window.confirmReject = async function () {
        const eventId = document.getElementById('reject-event-id')?.value;
        const reason = document.getElementById('reject-reason')?.value.trim();
        const btn = document.getElementById('btn-confirm-reject');

        if (!eventId) return;

        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        }

        try {
            const res = await apiPost(`/admin/events/${eventId}/reject`, {
                alasan: reason || null,
            });

            if (res && res._ok) {
                showToast('Event ditolak dan dikembalikan ke draft', 'success');
                closeRejectModal();
                await Promise.all([loadStats(), loadPendingEvents()]);
            } else {
                showToast(res?.message || 'Gagal menolak event', 'error');
            }
        } catch (err) {
            console.error('Reject error:', err);
            showToast('Gagal menghubungi server', 'error');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Tolak Event';
            }
        }
    };

    // ─── Escape HTML helper ───
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ─── Close modal on backdrop click ───
    const rejectModal = document.getElementById('reject-modal');
    if (rejectModal) {
        rejectModal.addEventListener('click', function (e) {
            if (e.target === rejectModal) closeRejectModal();
        });
    }

    // ─── Close modal on Escape ───
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeRejectModal();
    });

    // ─── Initialize ───
    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        loadPendingEvents();

        // Re-init lucide icons
        if (window.lucide) window.lucide.createIcons();
    });

})();
