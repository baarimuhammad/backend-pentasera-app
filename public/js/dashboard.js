/**
 * Dashboard — Fetch stats & events from API and populate the page.
 * public/js/dashboard.js
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', async () => {
        if (!requireAuth()) return;
        if (!requireRole('creator')) return;

        // Fetch stats
        try {
            const statsRes = await apiGet('/dashboard/stats');
            if (statsRes && statsRes._ok) {
                const d = statsRes.data;
                const elEvents  = document.getElementById('dash-stat-events');
                const elTickets = document.getElementById('dash-stat-tickets');
                const elRevenue = document.getElementById('dash-stat-revenue');

                if (elEvents)  elEvents.textContent = d.total_events_active;
                if (elTickets) elTickets.textContent = Number(d.total_tickets_sold).toLocaleString('id-ID');
                if (elRevenue) elRevenue.textContent = d.revenue_formatted;
            }
        } catch (e) {
            console.error('Failed to load dashboard stats:', e);
        }

        // Fetch events
        try {
            const eventsRes = await apiGet('/my-events');
            const container = document.getElementById('dashboard-events-list');
            const emptyMsg  = document.getElementById('dash-events-empty');

            if (eventsRes && eventsRes._ok && eventsRes.data && eventsRes.data.length > 0) {
                if (emptyMsg) emptyMsg.remove();

                // Show at most 6 events on dashboard
                const events = eventsRes.data.slice(0, 6);

                container.innerHTML = events.map(ev => {
                    const statusColor = ev.event_status === 'published'
                        ? 'bg-green-100 text-green-600'
                        : ev.event_status === 'draft'
                        ? 'bg-yellow-100 text-yellow-600'
                        : 'bg-gray-100 text-gray-600';

                    const statusText = ev.event_status === 'published' ? 'Aktif'
                        : ev.event_status === 'draft' ? 'Draf'
                        : ev.event_status.charAt(0).toUpperCase() + ev.event_status.slice(1);

                    const dateStr = ev.event_datetime
                        ? new Date(ev.event_datetime).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                        : '-';

                    return `
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-32 bg-gray-100 overflow-hidden">
                                <img src="${ev.image_src}" alt="${ev.nama_event}" class="w-full h-full object-cover" onerror="this.src='/assets/hero-banner.jpg'">
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="${statusColor} text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">${statusText}</span>
                                    <span class="text-[10px] text-gray-400">${dateStr}</span>
                                </div>
                                <h3 class="font-bold text-ink text-sm mb-1 truncate">${ev.nama_event}</h3>
                                <p class="text-xs text-gray-400 mb-3 truncate">${ev.lokasi || '-'}</p>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">${ev.tiket_terjual} tiket terjual</span>
                                    <span class="font-bold text-rust">${ev.pendapatan_formatted}</span>
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <a href="/manage-event/${ev.id}" class="flex-1 text-center py-2 bg-rust/10 text-rust text-xs font-bold rounded-lg hover:bg-rust/20 transition-colors">Kelola</a>
                                    <a href="/event-report/${ev.id}" class="flex-1 text-center py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition-colors">Laporan</a>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

            } else {
                if (emptyMsg) emptyMsg.textContent = 'Belum ada event. Buat event pertamamu!';
            }
        } catch (e) {
            console.error('Failed to load events:', e);
        }
    });
})();
