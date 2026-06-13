/**
 * Admin Analytics — JavaScript
 * Fetches analytics data and renders charts using Chart.js.
 * public/js/admin-analytics.js
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

    // ─── Escape HTML helper ───
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ─── Chart.js Global Defaults ───
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#7A7A7A';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;
    }

    // ─── Color Palette ───
    const COLORS = {
        rust: '#B84C2B',
        rustLight: 'rgba(184, 76, 43, 0.15)',
        gold: '#C8922A',
        goldLight: 'rgba(200, 146, 42, 0.15)',
        blue: '#2563eb',
        blueLight: 'rgba(37, 99, 235, 0.15)',
        green: '#16a34a',
        greenLight: 'rgba(22, 163, 74, 0.15)',
        amber: '#d97706',
        amberLight: 'rgba(217, 119, 6, 0.15)',
        red: '#dc2626',
        redLight: 'rgba(220, 38, 38, 0.15)',
        slate: '#64748b',
        slateLight: 'rgba(100, 116, 139, 0.15)',
    };

    const CHART_COLORS = [
        COLORS.rust, COLORS.gold, COLORS.blue, COLORS.green,
        COLORS.amber, COLORS.red, COLORS.slate, '#8b5cf6', '#ec4899',
    ];

    const CHART_BG_COLORS = [
        COLORS.rustLight, COLORS.goldLight, COLORS.blueLight, COLORS.greenLight,
        COLORS.amberLight, COLORS.redLight, COLORS.slateLight, 'rgba(139,92,246,0.15)', 'rgba(236,72,153,0.15)',
    ];

    // ─── Animate Counter ───
    function animateCounter(elementId, target, prefix = '', suffix = '') {
        const el = document.getElementById(elementId);
        if (!el) return;

        const duration = 800;
        const start = performance.now();
        const startVal = 0;

        function update(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const current = Math.round(startVal + (target - startVal) * eased);
            el.textContent = prefix + current.toLocaleString('id-ID') + suffix;

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        requestAnimationFrame(update);
    }

    // ─── Format Month Label ───
    function formatMonth(monthStr) {
        if (!monthStr) return '';
        const [year, month] = monthStr.split('-');
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return months[parseInt(month) - 1] + ' ' + year;
    }

    // ─── Status Label ───
    function statusLabel(status) {
        const map = {
            'published': 'Published',
            'draft': 'Draft',
            'pending_approval': 'Pending',
            'ended': 'Ended',
            'cancelled': 'Cancelled',
        };
        return map[status] || status;
    }

    // ─── Status Color ───
    function statusColor(status) {
        const map = {
            'published': COLORS.green,
            'draft': COLORS.slate,
            'pending_approval': COLORS.amber,
            'ended': COLORS.blue,
            'cancelled': COLORS.red,
        };
        return map[status] || COLORS.slate;
    }

    // ─── Load Analytics ───
    async function loadAnalytics() {
        try {
            const res = await apiGet('/admin/analytics');
            if (!res || !res._ok) {
                console.error('Failed to load analytics:', res?.message);
                return;
            }

            const d = res.data;

            // ── Overview Stats ──
            animateCounter('analytics-total-users', d.overview.total_users);
            animateCounter('analytics-total-events', d.overview.total_events);
            animateCounter('analytics-total-transactions', d.overview.total_transactions);

            const revenueEl = document.getElementById('analytics-total-revenue');
            if (revenueEl) revenueEl.textContent = d.overview.revenue_formatted || 'Rp 0';

            // ── Revenue Trend Chart ──
            renderRevenueTrend(d.revenue_trend || []);

            // ── User Growth Chart ──
            renderUserGrowth(d.user_growth || []);

            // ── Events by Category ──
            renderEventsCategory(d.events_by_category || []);

            // ── Events by Status ──
            renderEventsStatus(d.events_by_status || {});

            // ── Top Events ──
            renderTopEvents(d.top_events || []);

            // ── Recent Transactions ──
            renderRecentTransactions(d.recent_transactions || []);

        } catch (err) {
            console.error('Error loading analytics:', err);
        }
    }

    // ─── Revenue Trend (Line Chart) ───
    function renderRevenueTrend(data) {
        const ctx = document.getElementById('chart-revenue-trend');
        if (!ctx || typeof Chart === 'undefined') return;

        const labels = data.map(d => formatMonth(d.month));
        const revenues = data.map(d => d.revenue || 0);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: revenues,
                    borderColor: COLORS.rust,
                    backgroundColor: COLORS.rustLight,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: COLORS.rust,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2C1A0E',
                        titleColor: '#fff',
                        bodyColor: '#E5B96A',
                        borderColor: COLORS.gold,
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID'),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        border: { display: false },
                        ticks: {
                            callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt',
                        },
                    },
                },
            },
        });
    }

    // ─── User Growth (Bar Chart) ───
    function renderUserGrowth(data) {
        const ctx = document.getElementById('chart-user-growth');
        if (!ctx || typeof Chart === 'undefined') return;

        const labels = data.map(d => formatMonth(d.month));
        const counts = data.map(d => d.count || 0);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Pengguna Baru',
                    data: counts,
                    backgroundColor: COLORS.blueLight,
                    borderColor: COLORS.blue,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2C1A0E',
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        border: { display: false },
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                    },
                },
            },
        });
    }

    // ─── Events by Category (Doughnut) ───
    function renderEventsCategory(data) {
        const ctx = document.getElementById('chart-events-category');
        if (!ctx || typeof Chart === 'undefined') return;

        const labels = data.map(d => d.kategori_event || 'Lainnya');
        const counts = data.map(d => d.count || 0);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: counts,
                    backgroundColor: CHART_COLORS.slice(0, labels.length),
                    borderWidth: 0,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, font: { size: 11 } },
                    },
                    tooltip: {
                        backgroundColor: '#2C1A0E',
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
            },
        });
    }

    // ─── Events by Status (Horizontal Bar) ───
    function renderEventsStatus(data) {
        const ctx = document.getElementById('chart-events-status');
        if (!ctx || typeof Chart === 'undefined') return;

        const statuses = Object.keys(data);
        const counts = Object.values(data);
        const colors = statuses.map(s => statusColor(s));
        const bgColors = statuses.map(s => statusColor(s) + '25');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: statuses.map(statusLabel),
                datasets: [{
                    data: counts,
                    backgroundColor: bgColors,
                    borderColor: colors,
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2C1A0E',
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        border: { display: false },
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                    },
                },
            },
        });
    }

    // ─── Top Events ───
    function renderTopEvents(events) {
        const container = document.getElementById('top-events-list');
        const loading = document.getElementById('top-events-loading');

        if (loading) loading.style.display = 'none';
        if (!container) return;

        if (events.length === 0) {
            container.innerHTML = `
                <div style="text-align:center; padding:32px; color:var(--admin-text-dim); font-size:13px;">
                    Belum ada data event.
                </div>
            `;
            return;
        }

        const rankClasses = ['gold', 'silver', 'bronze', '', ''];

        container.innerHTML = events.map((ev, i) => `
            <div class="top-event-row">
                <div class="top-event-rank ${rankClasses[i] || ''}">${i + 1}</div>
                <div class="top-event-details">
                    <div class="top-event-title">${escapeHtml(ev.nama_event)}</div>
                    <div class="top-event-meta">${escapeHtml(ev.organizer)} · ${ev.tickets_sold} tiket terjual</div>
                </div>
                <div class="top-event-revenue">${escapeHtml(ev.revenue_formatted)}</div>
            </div>
        `).join('');
    }

    // ─── Recent Transactions ───
    function renderRecentTransactions(transactions) {
        const loading = document.getElementById('transactions-loading');
        const table = document.getElementById('transactions-table');
        const body = document.getElementById('transactions-body');
        const empty = document.getElementById('transactions-empty');

        if (loading) loading.style.display = 'none';

        if (transactions.length === 0) {
            if (empty) empty.style.display = 'block';
            return;
        }

        if (table) table.style.display = 'table';

        if (body) {
            body.innerHTML = transactions.map(t => {
                const date = t.date
                    ? new Date(t.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                    : '-';

                return `
                    <tr>
                        <td><span style="font-weight:600; font-family:monospace; font-size:12px; color:var(--admin-accent);">${escapeHtml(t.order_code)}</span></td>
                        <td>
                            <div class="user-name" style="font-size:12px;">${escapeHtml(t.buyer_name)}</div>
                            <div class="user-email">${escapeHtml(t.buyer_email)}</div>
                        </td>
                        <td><span class="event-meta">${escapeHtml(t.event)}</span></td>
                        <td><span style="font-weight:700; color:var(--admin-text);">${escapeHtml(t.total_formatted)}</span></td>
                        <td><span class="event-meta">${date}</span></td>
                    </tr>
                `;
            }).join('');
        }
    }

    // ─── Initialize ───
    document.addEventListener('DOMContentLoaded', function () {
        loadAnalytics();
        if (window.lucide) window.lucide.createIcons();
    });

})();
