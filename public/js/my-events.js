/**
 * My Events — Fetch events from API and render into tabs.
 * public/js/my-events.js
 */
(function () {
    'use strict';

    // Store all events for search filtering
    let allEvents = [];

    document.addEventListener('DOMContentLoaded', async () => {
        if (!requireAuth()) return;
        if (!requireRole('creator')) return;

        await loadMyEvents();
    });

    /**
     * Fetch events from /api/my-events and categorize into tabs.
     */
    async function loadMyEvents() {
        try {
            const res = await apiGet('/my-events');

            if (!res || !res._ok) {
                console.error('Failed to load my events:', res);
                showError('Gagal memuat data event. Silakan coba lagi.');
                return;
            }

            allEvents = res.data || [];
            renderAllTabs(allEvents);

        } catch (e) {
            console.error('Error loading my events:', e);
            showError('Terjadi kesalahan saat memuat event.');
        }
    }

    /**
     * Categorize events and render each tab.
     */
    function renderAllTabs(events) {
        const now = new Date();

        const activeEvents = events.filter(ev =>
            ev.event_status === 'published' && new Date(ev.event_datetime) >= now
        );

        const pendingEvents = events.filter(ev =>
            ev.event_status === 'pending_approval'
        );

        const draftEvents = events.filter(ev =>
            ev.event_status === 'draft'
        );

        const pastEvents = events.filter(ev =>
            ev.event_status === 'published' && new Date(ev.event_datetime) < now
        );

        // Update counts
        updateCount('count-aktif', activeEvents.length);
        updateCount('count-pending', pendingEvents.length);
        updateCount('count-draft', draftEvents.length);
        updateCount('count-lalu', pastEvents.length);

        // Update sidebar badge
        const badge = document.getElementById('sidebar-event-count');
        if (badge) badge.textContent = activeEvents.length + pendingEvents.length + draftEvents.length;

        // Render grids
        renderActiveGrid(activeEvents);
        renderPendingGrid(pendingEvents);
        renderDraftGrid(draftEvents);
        renderPastGrid(pastEvents);

        // Re-init Lucide icons for dynamically added content
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function updateCount(id, count) {
        const el = document.getElementById(id);
        if (el) el.textContent = count > 0 ? `(${count})` : '';
    }

    /**
     * Render Active Events tab.
     */
    function renderActiveGrid(events) {
        const grid = document.getElementById('grid-aktif');
        if (!grid) return;

        let html = '';

        if (events.length === 0) {
            html = `
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Belum ada event aktif</p>
                    <p class="text-sm mt-1">Buat event baru untuk memulai</p>
                </div>
            `;
        } else {
            html = events.map(ev => {
                const sold = ev.tiket_terjual || 0;
                const capacity = (ev.sisa_kuota_total || 0) + sold;
                const dateStr = formatDate(ev.event_datetime);

                return `
                    <div class="event-dashboard-card" data-name="${escHtml(ev.nama_event)}">
                        <div class="event-card-img">
                            <img src="${ev.image_src}" alt="${escHtml(ev.nama_event)}" onerror="this.src='/assets/hero-banner.jpg'">
                            <span class="event-status-badge">Aktif</span>
                        </div>
                        <div class="event-card-body">
                            <h3 class="event-card-title">${escHtml(ev.nama_event)}</h3>
                            <div class="event-card-meta">
                                <div class="meta-item">
                                    <label>Tiket</label>
                                    <span>${sold} / ${capacity}</span>
                                </div>
                                <div class="meta-item">
                                    <label>Tanggal Event</label>
                                    <span>${dateStr}</span>
                                </div>
                            </div>
                            <div class="event-card-footer">
                                <div class="status-indicator">
                                    <div class="status-dot"></div>
                                    <span>AKTIF</span>
                                </div>
                                <a href="/manage-event/${ev.id}" class="kelola-link">
                                    Kelola
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Always add "Create Event" placeholder
        html += `
            <div onclick="location.href='/create-event'" class="create-event-placeholder">
                <div class="placeholder-icon">
                    <i data-lucide="plus" class="w-8 h-8"></i>
                </div>
                <h4 class="font-bold text-ink mb-2">Buat Event Baru</h4>
                <p class="text-gray-400 text-xs leading-relaxed">Mulai daftarkan pementasan seni budaya Anda hari ini</p>
            </div>
        `;

        grid.innerHTML = html;
    }

    /**
     * Render Pending Approval Events tab.
     */
    function renderPendingGrid(events) {
        const grid = document.getElementById('grid-pending');
        if (!grid) return;

        if (events.length === 0) {
            grid.innerHTML = `
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="clock" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Tidak ada event menunggu approval</p>
                    <p class="text-sm mt-1">Event yang dikirim untuk review akan muncul di sini</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = events.map(ev => {
            const dateStr = formatDate(ev.event_datetime);

            return `
                <div class="event-dashboard-card" data-name="${escHtml(ev.nama_event)}">
                    <div class="event-card-img" style="filter: saturate(0.7);">
                        <img src="${ev.image_src}" alt="${escHtml(ev.nama_event)}" onerror="this.src='/assets/hero-banner.jpg'">
                        <span class="event-status-badge" style="background: #d97706;">Menunggu Approval</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-card-title">${escHtml(ev.nama_event)}</h3>
                        <div class="event-card-meta">
                            <div class="meta-item">
                                <label>Tanggal Event</label>
                                <span>${dateStr}</span>
                            </div>
                            <div class="meta-item">
                                <label>Status</label>
                                <span>Menunggu Review Admin</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <div class="status-indicator" style="color: #d97706;">
                                <div class="status-dot" style="background: #d97706;"></div>
                                <span>PENDING</span>
                            </div>
                            <a href="/manage-event/${ev.id}" class="kelola-link">
                                Lihat Detail
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Render Draft Events tab.
     */
    function renderDraftGrid(events) {
        const grid = document.getElementById('grid-draft');
        if (!grid) return;

        if (events.length === 0) {
            grid.innerHTML = `
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Belum ada event draft</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = events.map(ev => {
            const capacity = (ev.sisa_kuota_total || 0) + (ev.tiket_terjual || 0);

            return `
                <div class="event-dashboard-card opacity-90 cursor-pointer" onclick="location.href='/manage-event/${ev.id}'" data-name="${escHtml(ev.nama_event)}">
                    <div class="event-card-img" style="filter: grayscale(0.5);">
                        <img src="${ev.image_src}" alt="${escHtml(ev.nama_event)}" onerror="this.src='/assets/hero-banner.jpg'">
                        <span class="event-status-badge" style="background: #9ca3af;">Draft</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-card-title">${escHtml(ev.nama_event)}</h3>
                        <div class="event-card-meta">
                            <div class="meta-item">
                                <label>Kapasitas</label>
                                <span>${capacity}</span>
                            </div>
                            <div class="meta-item">
                                <label>Status</label>
                                <span>Draft</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <div class="status-indicator text-gray-400">
                                <div class="status-dot bg-gray-400"></div>
                                <span>DRAFT</span>
                            </div>
                            <a href="/manage-event/${ev.id}" class="kelola-link">
                                Edit Draft
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Render Past Events tab.
     */
    function renderPastGrid(events) {
        const grid = document.getElementById('grid-lalu');
        if (!grid) return;

        if (events.length === 0) {
            grid.innerHTML = `
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="archive" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Belum ada event yang selesai</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = events.map(ev => {
            const sold = ev.tiket_terjual || 0;
            const dateStr = formatDateShort(ev.event_datetime);

            return `
                <div class="event-dashboard-card opacity-75 cursor-pointer" onclick="location.href='/event-report/${ev.id}'" data-name="${escHtml(ev.nama_event)}">
                    <div class="event-card-img" style="filter: grayscale(1);">
                        <img src="${ev.image_src}" alt="${escHtml(ev.nama_event)}" onerror="this.src='/assets/hero-banner.jpg'">
                        <span class="event-status-badge" style="background: #2C1A0E;">Selesai</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-card-title">${escHtml(ev.nama_event)}</h3>
                        <div class="event-card-meta">
                            <div class="meta-item">
                                <label>Total Terjual</label>
                                <span>${sold}</span>
                            </div>
                            <div class="meta-item">
                                <label>Tanggal</label>
                                <span>${dateStr}</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <div class="status-indicator text-ink">
                                <div class="status-dot bg-ink"></div>
                                <span>SELESAI</span>
                            </div>
                            <a href="/event-report/${ev.id}" class="kelola-link">
                                Laporan
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Show error in all grids.
     */
    function showError(message) {
        ['grid-aktif', 'grid-pending', 'grid-draft', 'grid-lalu'].forEach(id => {
            const grid = document.getElementById(id);
            if (grid) {
                grid.innerHTML = `
                    <div class="text-center py-10 text-red-400 col-span-full">
                        <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-3 text-red-300"></i>
                        <p class="font-bold">${message}</p>
                        <button onclick="location.reload()" class="mt-3 text-sm text-rust font-bold hover:underline">Coba Lagi</button>
                    </div>
                `;
            }
        });
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // ──────────────────────────────
    // Search / Filter
    // ──────────────────────────────

    window.filterEvents = function () {
        const query = (document.getElementById('search-events')?.value || '').toLowerCase().trim();

        document.querySelectorAll('.event-dashboard-card').forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            if (!query || name.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    };

    // ──────────────────────────────
    // Tab switching
    // ──────────────────────────────

    window.switchMyEventTab = function (tabId) {
        // Update tab buttons
        const tabs = document.querySelectorAll('.event-tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
            const text = tab.innerText.toLowerCase();
            if ((tabId === 'aktif' && text.includes('aktif')) ||
                (tabId === 'pending' && text.includes('approval')) ||
                (tabId === 'draft' && text.includes('draft')) ||
                (tabId === 'lalu'  && text.includes('lalu'))) {
                tab.classList.add('active');
            }
        });

        // Update content visibility
        const contents = document.querySelectorAll('.event-tab-content');
        contents.forEach(content => {
            content.classList.remove('active');
        });
        const target = document.getElementById('tab-' + tabId);
        if (target) target.classList.add('active');
    };

    // ──────────────────────────────
    // Utilities
    // ──────────────────────────────

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        try {
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
        } catch { return dateStr; }
    }

    function formatDateShort(dateStr) {
        if (!dateStr) return '-';
        try {
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'short', year: 'numeric'
            });
        } catch { return dateStr; }
    }

    function escHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

})();
