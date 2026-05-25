/**
 * my-tickets.js — Dynamic ticket rendering from API
 * Requires: api-helper.js loaded first
 */

(function () {
    'use strict';

    // ── State ─────────────────────────────────────────────
    let allTickets = [];
    let currentFilter = 'aktif';

    // ── Init ──────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', init);

    async function init() {
        if (!requireAuth()) return;

        await loadTickets();
        bindTabs();
        bindSearch();
    }

    // ── Fetch tickets from API ────────────────────────────
    async function loadTickets() {
        const container = document.getElementById('tickets-list');
        container.innerHTML = renderLoading();

        const res = await apiGet('/my-tickets');

        if (!res || !res._ok) {
            container.innerHTML = renderEmpty('Gagal memuat tiket. Silakan coba lagi.');
            return;
        }

        allTickets = res.data || [];
        renderTickets();
    }

    // ── Render tickets ────────────────────────────────────
    function renderTickets(searchQuery = '') {
        const container = document.getElementById('tickets-list');
        const query = searchQuery.toLowerCase();

        const filtered = allTickets.filter(ticket => {
            const status = mapStatus(ticket.status_validasi);
            const matchFilter = status === currentFilter;

            if (!matchFilter) return false;
            if (!query) return true;

            const eventName = ticket.detail_order?.ticket?.event?.nama_event || '';
            return eventName.toLowerCase().includes(query);
        });

        if (filtered.length === 0) {
            container.innerHTML = renderEmpty(getEmptyMessage(currentFilter));
            return;
        }

        container.innerHTML = filtered.map(ticket => renderTicketCard(ticket)).join('');

        // Re-init Lucide icons
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // ── Map status_validasi to tab filter ─────────────────
    function mapStatus(statusValidasi) {
        switch (statusValidasi) {
            case 'valid':
                return 'aktif';
            case 'used':
                return 'selesai';
            case 'expired':
            case 'cancelled':
                return 'batal';
            default:
                return 'aktif';
        }
    }

    // ── Render a single ticket card ───────────────────────
    function renderTicketCard(ticket) {
        const detail = ticket.detail_order || {};
        const ticketType = detail.ticket || {};
        const event = ticketType.event || {};
        const order = detail.order || {};

        const eventName = event.nama_event || 'Event Tidak Diketahui';
        const eventDate = event.event_datetime ? formatDate(event.event_datetime) : '-';
        const eventTime = event.event_datetime ? formatTime(event.event_datetime) : '-';
        const eventLocation = event.lokasi || '-';
        const ticketKategori = ticketType.kategori || '-';
        const jumlah = detail.jumlah || 1;
        const subtotal = detail.subtotal || 0;
        const kodeQr = ticket.kode_qr || '';
        const statusValidasi = ticket.status_validasi || 'valid';
        const imageSrc = event.image_src || event.image_url || '/assets/hero-banner.jpg';

        const statusInfo = getStatusInfo(statusValidasi, order.status_order);
        const isActive = statusValidasi === 'valid';
        const isUsed = statusValidasi === 'used';

        return `
        <div class="ticket-card" data-status="${mapStatus(statusValidasi)}">
            <div class="w-full md:w-56 h-48 md:h-auto overflow-hidden relative group" style="min-width:224px;">
                <img src="${imageSrc}" alt="${eventName}" 
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 ${isUsed ? 'grayscale opacity-60' : ''}"
                     onerror="this.src='/assets/hero-banner.jpg'">
                ${isUsed ? `
                <div class="absolute inset-0 bg-ink/60 flex items-center justify-center">
                    <span class="status-badge-ticket status-used scale-125">Check-in Berhasil</span>
                </div>` : `
                <div class="absolute top-3 left-3">
                    <span class="status-badge-ticket ${statusInfo.badgeClass} shadow-sm">${statusInfo.badgeText}</span>
                </div>`}
            </div>

            <div class="flex-grow p-6 flex flex-col justify-between ${isUsed ? 'bg-gray-50/50' : ''}">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <h2 class="font-display text-xl ${isUsed ? 'text-gray-400' : 'text-ink'} leading-tight">${eventName}</h2>
                        <span class="text-[10px] font-bold ${isUsed ? 'text-gray-300 bg-gray-200/50' : 'text-gray-400 bg-gray-50'} uppercase tracking-widest px-2 py-1 rounded">${kodeQr}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 mt-4">
                        <div class="flex items-center gap-2 text-gray-${isUsed ? '400' : '500'} text-sm">
                            <i data-lucide="calendar" class="w-4 h-4 ${isUsed ? '' : 'text-rust/70'}"></i>
                            <span>${eventDate}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-${isUsed ? '400' : '500'} text-sm">
                            <i data-lucide="clock" class="w-4 h-4 ${isUsed ? '' : 'text-rust/70'}"></i>
                            <span>${eventTime}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-${isUsed ? '400' : '500'} text-sm">
                            <i data-lucide="map-pin" class="w-4 h-4 ${isUsed ? '' : 'text-rust/70'}"></i>
                            <span>${eventLocation}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-${isUsed ? '400' : '500'} text-sm">
                            <i data-lucide="ticket" class="w-4 h-4 ${isUsed ? '' : 'text-rust/70'}"></i>
                            <span>${jumlah} Tiket (${ticketKategori})</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-between">
                    ${statusInfo.footerHtml}
                    <span class="text-lg font-bold ${isActive ? 'text-rust' : 'text-gray-400'}">Rp ${formatNumber(subtotal)}</span>
                </div>
            </div>

            <div class="ticket-divider hidden md:block"></div>

            <div class="p-6 flex flex-col items-center justify-center ${isUsed ? 'bg-gray-100/30' : 'bg-gray-50/30'} md:w-56 text-center border-t md:border-t-0 md:border-l border-gray-100" style="min-width:224px;">
                ${isActive ? `
                <div class="mb-6 w-full">
                    <div class="flex flex-col items-center gap-3">
                        <div id="qr-mini-${ticket.id}" class="w-24 h-24"></div>
                        <span class="text-[10px] font-bold text-gray-400 font-mono tracking-widest">${kodeQr}</span>
                    </div>
                </div>
                <button onclick="showTicketQR('${eventName}', '${kodeQr}')" class="w-full bg-rust text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-rust-deep transition-all shadow-md shadow-rust/10 cursor-pointer">Lihat Tiket</button>
                ` : isUsed ? `
                <div class="mb-6 w-full opacity-30 grayscale">
                    <div class="flex flex-col items-center gap-3">
                        <div id="qr-mini-${ticket.id}" class="w-24 h-24"></div>
                        <span class="text-[10px] font-bold font-mono tracking-widest">${kodeQr}</span>
                    </div>
                </div>
                <button class="w-full border border-gray-200 text-gray-400 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">Sudah Terpakai</button>
                ` : `
                <div class="mb-5 flex flex-col items-center text-gray-300">
                    <i data-lucide="x-circle" class="w-10 h-10 mb-2 opacity-20"></i>
                    <p class="text-[10px] leading-relaxed">Tiket ini sudah tidak berlaku</p>
                </div>
                <button class="w-full border border-gray-200 text-gray-400 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">Tidak Berlaku</button>
                `}
            </div>
        </div>`;
    }

    // ── Status helpers ────────────────────────────────────
    function getStatusInfo(statusValidasi, statusOrder) {
        switch (statusValidasi) {
            case 'valid':
                return {
                    badgeClass: 'status-success',
                    badgeText: 'E-Tiket Siap',
                    footerHtml: `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <span class="text-sm font-medium text-ink">Pembayaran Lunas</span>
                        </div>`
                };
            case 'used':
                return {
                    badgeClass: 'status-used',
                    badgeText: 'Sudah Digunakan',
                    footerHtml: `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                <i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <span class="text-sm font-medium text-blue-600">Check-in Berhasil</span>
                        </div>`
                };
            case 'expired':
            case 'cancelled':
            default:
                return {
                    badgeClass: 'status-pending',
                    badgeText: 'Kedaluwarsa',
                    footerHtml: `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                            </div>
                            <span class="text-sm font-medium text-red-600">Tidak Berlaku</span>
                        </div>`
                };
        }
    }

    function getEmptyMessage(filter) {
        switch (filter) {
            case 'aktif': return 'Belum ada tiket aktif. Yuk cari event menarik!';
            case 'selesai': return 'Belum ada tiket yang selesai digunakan.';
            case 'batal': return 'Tidak ada tiket yang dibatalkan.';
            default: return 'Belum ada tiket.';
        }
    }

    // ── Tab binding ───────────────────────────────────────
    function bindTabs() {
        const tabs = document.querySelectorAll('.ticket-tab-btn');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                currentFilter = tab.getAttribute('data-filter');
                const searchQuery = document.getElementById('ticketSearch')?.value || '';
                renderTickets(searchQuery);
            });
        });
    }

    // ── Search binding ────────────────────────────────────
    function bindSearch() {
        const searchInput = document.getElementById('ticketSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                renderTickets(this.value);
            });
        }
    }

    // ── QR Code Modal ─────────────────────────────────────
    window.showTicketQR = function (name, kodeQr) {
        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalId').innerText = kodeQr;

        const qrContainer = document.getElementById('modalQrCode');
        qrContainer.innerHTML = '';

        // Generate QR code using QRCode.js
        if (typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: kodeQr,
                width: 180,
                height: 180,
                colorDark: '#1A0F0A',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            qrContainer.innerHTML = `<p class="text-gray-400 text-sm">${kodeQr}</p>`;
        }

        document.getElementById('ticketModal').style.display = 'flex';
    };

    window.closeTicket = function () {
        document.getElementById('ticketModal').style.display = 'none';
    };

    // ── Formatting helpers ────────────────────────────────
    function formatDate(dateStr) {
        const d = new Date(dateStr);
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')} WIB`;
    }

    function formatNumber(n) {
        return Number(n).toLocaleString('id-ID');
    }

    // ── UI components ─────────────────────────────────────
    function renderLoading() {
        return `
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
            <div class="w-12 h-12 border-4 border-gray-200 border-t-rust rounded-full animate-spin mb-4"></div>
            <p class="text-sm">Memuat tiket...</p>
        </div>`;
    }

    function renderEmpty(message) {
        return `
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
            <i data-lucide="ticket" class="w-16 h-16 mb-4 opacity-20"></i>
            <p class="font-display text-lg text-gray-500 mb-2">Tidak Ada Tiket</p>
            <p class="text-sm text-gray-400 max-w-xs text-center">${message}</p>
            <a href="/events" class="mt-6 bg-rust text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-rust-deep transition-all">
                Cari Event
            </a>
        </div>`;
    }

})();
