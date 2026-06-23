/**
 * Admin Event Review — JavaScript
 * Fetches event details and handles approve/reject on the review page.
 * public/js/admin-event-review.js
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

    const eventId = typeof REVIEW_EVENT_ID !== 'undefined' ? REVIEW_EVENT_ID : null;
    if (!eventId) {
        showError('Event ID tidak valid.');
        return;
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

    // ─── Show Error State ───
    function showError(msg) {
        const loadingEl = document.getElementById('review-loading');
        const errorEl = document.getElementById('review-error');
        const contentEl = document.getElementById('review-content');
        const errorMsg = document.getElementById('review-error-msg');

        if (loadingEl) loadingEl.style.display = 'none';
        if (contentEl) contentEl.style.display = 'none';
        if (errorEl) errorEl.style.display = 'flex';
        if (errorMsg && msg) errorMsg.textContent = msg;

        if (window.lucide) window.lucide.createIcons();
    }

    // ─── Escape HTML ───
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ─── Format Date ───
    function formatDate(dateStr, options) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', options || {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const date = d.toLocaleDateString('id-ID', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
        const time = d.toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit'
        });
        return `${date}, ${time} WIB`;
    }

    // ─── Load Event Detail ───
    async function loadEventDetail() {
        const loadingEl = document.getElementById('review-loading');
        const contentEl = document.getElementById('review-content');

        try {
            const res = await apiGet(`/admin/pending-events/${eventId}`);
            if (!res || !res._ok) {
                showError(res?.message || 'Event tidak ditemukan.');
                return;
            }

            const ev = res.data;

            // Hide loading, show content
            if (loadingEl) loadingEl.style.display = 'none';
            if (contentEl) contentEl.style.display = 'block';

            // Banner
            const bannerEl = document.getElementById('review-banner');
            if (bannerEl) {
                bannerEl.src = ev.image_src || '/assets/hero-banner.jpg';
                bannerEl.alt = ev.nama_event;
            }

            // Title
            const titleEl = document.getElementById('review-title');
            if (titleEl) titleEl.textContent = `Review: ${ev.nama_event}`;

            // Status badge
            const statusBadge = document.getElementById('review-status-badge');
            if (statusBadge) {
                if (ev.event_status === 'pending_approval') {
                    statusBadge.className = 'review-status-badge pending';
                    statusBadge.innerHTML = '<i data-lucide="clock" class="w-4 h-4"></i> Menunggu Persetujuan';
                } else {
                    statusBadge.className = 'review-status-badge';
                    statusBadge.textContent = ev.event_status;
                }
            }

            // Event Info
            setText('detail-nama', ev.nama_event);
            setText('detail-kategori', ev.kategori_event || 'Umum');
            setText('detail-datetime', formatDateTime(ev.event_datetime));
            setText('detail-lokasi', ev.lokasi || '-');
            setText('detail-submitted', formatDate(ev.created_at, {
                day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            }));

            // Description
            const deskripsiEl = document.getElementById('detail-deskripsi');
            if (deskripsiEl) {
                if (ev.deskripsi) {
                    // Convert newlines to paragraphs
                    const paragraphs = ev.deskripsi.split('\n').filter(p => p.trim());
                    deskripsiEl.innerHTML = paragraphs.map(p => `<p>${escapeHtml(p)}</p>`).join('');
                } else {
                    deskripsiEl.innerHTML = '<p class="text-muted">Belum ada deskripsi.</p>';
                }
            }

            // Creator Info
            setText('creator-name', ev.creator_name);
            setText('creator-email', ev.creator_email);
            setText('creator-organizer', ev.organizer_name);

            const creatorAvatar = document.getElementById('creator-avatar');
            if (creatorAvatar) {
                creatorAvatar.textContent = (ev.creator_name || 'C').charAt(0).toUpperCase();
            }

            // Rules
            setText('rule-max-ticket', ev.max_ticket_per_transaction || 'Tidak dibatasi');

            const oneEmailEl = document.getElementById('rule-one-email');
            if (oneEmailEl) {
                if (ev.one_email_one_transaction) {
                    oneEmailEl.innerHTML = '<span class="rule-yes"><i data-lucide="check" class="w-3 h-3"></i> Ya</span>';
                } else {
                    oneEmailEl.innerHTML = '<span class="rule-no"><i data-lucide="x" class="w-3 h-3"></i> Tidak</span>';
                }
            }

            const identityEl = document.getElementById('rule-identity');
            if (identityEl) {
                if (ev.single_identity_per_ticket) {
                    identityEl.innerHTML = '<span class="rule-yes"><i data-lucide="check" class="w-3 h-3"></i> Ya</span>';
                } else {
                    identityEl.innerHTML = '<span class="rule-no"><i data-lucide="x" class="w-3 h-3"></i> Tidak</span>';
                }
            }

            // Summary
            setText('summary-tickets', ev.total_tickets || 0);
            setText('summary-capacity', ev.total_capacity || 0);
            setText('ticket-count', `${ev.total_tickets || 0} tiket`);

            // Tickets
            renderTickets(ev.tickets || []);

            // If event is not pending, hide approve/reject buttons
            if (ev.event_status !== 'pending_approval') {
                const actionsCard = document.querySelector('.review-actions-card');
                if (actionsCard) actionsCard.style.display = 'none';

                if (statusBadge) {
                    statusBadge.className = 'review-status-badge approved';
                    statusBadge.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4"></i> ${ev.event_status}`;
                }
            }

            // Re-init lucide icons
            if (window.lucide) window.lucide.createIcons();

        } catch (err) {
            console.error('Error loading event detail:', err);
            showError('Gagal memuat detail event.');
        }
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '-';
    }

    // ─── Render Tickets ───
    function renderTickets(tickets) {
        const container = document.getElementById('tickets-container');
        const emptyEl = document.getElementById('tickets-empty');

        if (!container) return;

        if (!tickets.length) {
            if (emptyEl) emptyEl.style.display = 'block';
            return;
        }

        if (emptyEl) emptyEl.style.display = 'none';

        container.innerHTML = tickets.map(ticket => {
            const saleStart = ticket.sale_start
                ? new Date(ticket.sale_start).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                : '-';
            const saleEnd = ticket.sale_end
                ? new Date(ticket.sale_end).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                : '-';

            return `
                <div class="review-ticket-card">
                    <div class="review-ticket-header">
                        <div class="review-ticket-name">
                            <i data-lucide="ticket" class="w-4 h-4"></i>
                            ${escapeHtml(ticket.kategori)}
                        </div>
                        <div class="review-ticket-price ${ticket.harga === 0 ? 'free' : ''}">
                            ${ticket.harga_formatted}
                        </div>
                    </div>
                    <div class="review-ticket-details">
                        <div class="review-ticket-detail">
                            <span class="review-ticket-detail-label">Kuota</span>
                            <span class="review-ticket-detail-value">${ticket.kuota} tiket</span>
                        </div>
                        <div class="review-ticket-detail">
                            <span class="review-ticket-detail-label">Penjualan</span>
                            <span class="review-ticket-detail-value">${saleStart} — ${saleEnd}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // ─── Approve Event ───
    window.approveEvent = async function () {
        const btn = document.getElementById('btn-approve-event');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader" class="w-5 h-5 animate-spin"></i> Memproses...';
            if (window.lucide) window.lucide.createIcons();
        }

        try {
            const res = await apiPost(`/admin/events/${eventId}/approve`);
            if (res && res._ok) {
                showToast('Event berhasil di-approve dan dipublikasikan!', 'success');
                // Redirect back after short delay
                setTimeout(() => {
                    window.location.href = '/admin/dashboard';
                }, 1500);
            } else {
                showToast(res?.message || 'Gagal approve event', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i> Setujui Event';
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        } catch (err) {
            console.error('Approve error:', err);
            showToast('Gagal menghubungi server', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i> Setujui Event';
                if (window.lucide) window.lucide.createIcons();
            }
        }
    };

    // ─── Reject Modal ───
    window.openRejectModal = function () {
        const modal = document.getElementById('reject-modal');
        const reason = document.getElementById('reject-reason');
        if (reason) reason.value = '';
        if (modal) modal.classList.add('active');
    };

    window.closeRejectModal = function () {
        const modal = document.getElementById('reject-modal');
        if (modal) modal.classList.remove('active');
    };

    window.confirmReject = async function () {
        const reason = document.getElementById('reject-reason')?.value.trim();
        const btn = document.getElementById('btn-confirm-reject');

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
                setTimeout(() => {
                    window.location.href = '/admin/dashboard';
                }, 1500);
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
        loadEventDetail();
        if (window.lucide) window.lucide.createIcons();
    });

})();
