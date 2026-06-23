/**
 * Pentasara - Shared Scripts
 */

// --- Helper Functions ---

function scrollCarousel(btn, dir) {
    const wrapper = btn.closest('.carousel-wrapper');
    const grid = wrapper.querySelector('.events-grid');
    if (grid) {
        const scrollAmount = 260; // Card width + gap
        const currentScroll = grid.scrollLeft;
        const maxScroll = grid.scrollWidth - grid.clientWidth;
        const halfScroll = grid.scrollWidth / 2;

        // Infinite Logic: If at the right end, jump to middle
        if (dir === 1 && currentScroll >= maxScroll - 10) {
            grid.scrollTo({ left: halfScroll - grid.clientWidth, behavior: 'instant' });
            setTimeout(() => {
                grid.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }, 10);
            return;
        }

        // Infinite Logic: If at the left end, jump to middle
        if (dir === -1 && currentScroll <= 10) {
            grid.scrollTo({ left: halfScroll, behavior: 'instant' });
            setTimeout(() => {
                grid.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            }, 10);
            return;
        }

        grid.scrollBy({ left: dir * scrollAmount, behavior: 'smooth' });
    }
}

// --- Auth & Role Logic ---
// NOTE: Primary auth state is managed by api-helper.js (getUser, getToken, isLoggedIn, logout).
// The functions below are kept for backward compatibility with existing onclick handlers.

function checkAuthState() {
    // Delegate to the layout's syncNavbar (loaded via api-helper + app.blade.php).
    // This is now a no-op fallback; the layout script handles everything.
    const loggedIn = typeof isLoggedIn === 'function' ? isLoggedIn() : false;
    const user = typeof getUser === 'function' ? getUser() : null;

    document.body.classList.toggle('is-logged-in', loggedIn);

    if (loggedIn && user) {
        const isCreator = user.role === 'creator';
        const isAdmin = user.role === 'admin';
        document.body.classList.toggle('is-creator', isCreator);
        document.body.classList.toggle('is-admin', isAdmin);

        // Admin auto-redirect: if on a non-admin page, redirect to admin dashboard
        if (isAdmin && !window.location.pathname.startsWith('/admin/') && !window.location.pathname.startsWith('/profile') && !window.location.pathname.startsWith('/settings') && !window.location.pathname.startsWith('/pusat-bantuan')) {
            window.location.href = '/admin/dashboard';
            return;
        }

        const roleLabel = document.getElementById('dropdown-role-label');
        if (roleLabel) {
            if (isAdmin) {
                roleLabel.innerText = 'Admin';
            } else {
                roleLabel.innerText = isCreator ? 'Pembeli' : 'Penyelenggara';
            }
        }
    } else {
        document.body.classList.remove('is-creator', 'is-admin');
    }

    // Update sidebar switch button text dynamically — hide for admin
    const switchBtns = document.querySelectorAll('.switch-mode-btn');
    if (switchBtns.length && user) {
        const isCreator = user && user.role === 'creator';
        const isAdmin = user && user.role === 'admin';
        switchBtns.forEach(btn => {
            if (isAdmin) {
                btn.style.display = 'none';
            } else {
                btn.style.display = '';
                const span = btn.querySelector('span');
                if (span) span.innerText = isCreator ? 'Beralih ke Pembeli' : 'Beralih ke Penyelenggara';
            }
        });
    }

    // Hide role-switch dropdown header for admin
    if (user && user.role === 'admin') {
        const dropdownHeader = document.querySelector('.dropdown-header');
        if (dropdownHeader) dropdownHeader.style.display = 'none';
    }

    if (window.lucide) window.lucide.createIcons();
}

async function toggleRole() {
    const user = typeof getUser === 'function' ? getUser() : null;
    if (!user) return;
    // Admin cannot switch roles
    if (user.role === 'admin') {
        alert('Akun admin tidak dapat beralih mode.');
        return;
    }
    const targetRole = user.role === 'creator' ? 'buyer' : 'creator';
    try {
        const res = await apiPatch('/profile', { role: targetRole });
        if (res && res._ok && res.data) {
            localStorage.setItem('user', JSON.stringify(res.data));
            checkAuthState();
            window.location.href = '/';
        } else {
            alert('Gagal beralih akun: ' + (res?.message || 'Error tidak diketahui'));
        }
    } catch (err) {
        console.error(err);
        alert('Gagal menghubungi server untuk beralih akun.');
    }
}

async function toggleRoleAndRedirect() {
    const user = typeof getUser === 'function' ? getUser() : null;
    if (!user) return;
    // Admin cannot switch roles
    if (user.role === 'admin') {
        alert('Akun admin tidak dapat beralih mode.');
        return;
    }
    const targetRole = user.role === 'creator' ? 'buyer' : 'creator';
    try {
        const res = await apiPatch('/profile', { role: targetRole });
        if (res && res._ok && res.data) {
            localStorage.setItem('user', JSON.stringify(res.data));
            checkAuthState();
            window.location.href = '/';
        } else {
            alert('Gagal beralih akun: ' + (res?.message || 'Error tidak diketahui'));
        }
    } catch (err) {
        console.error(err);
        alert('Gagal menghubungi server untuk beralih akun.');
    }
}

// logout() is provided by api-helper.js (calls POST /api/logout, clears storage, redirects)

window.addEventListener('DOMContentLoaded', () => {
    checkAuthState();

    // ── Live Search Dropdown ────────────────────────────────
    (function initLiveSearch() {
        const input = document.getElementById('navbar-search-input');
        const dropdown = document.getElementById('live-search-dropdown');
        const form = document.getElementById('navbar-search-form');
        const container = document.getElementById('search-container');

        if (!input || !dropdown) return;

        let debounceTimer = null;
        let abortController = null;
        let activeIndex = -1; // for keyboard nav
        let currentQuery = '';

        // Debounced fetch
        input.addEventListener('input', function () {
            const q = this.value.trim();
            currentQuery = q;
            clearTimeout(debounceTimer);

            if (q.length < 2) {
                hideDropdown();
                return;
            }

            showLoading();

            debounceTimer = setTimeout(() => {
                fetchResults(q);
            }, 300);
        });

        // Show on focus if there's already text
        input.addEventListener('focus', function () {
            const q = this.value.trim();
            if (q.length >= 2 && dropdown.innerHTML && !dropdown.classList.contains('active')) {
                showDropdown();
            }
        });

        // Keyboard navigation
        input.addEventListener('keydown', function (e) {
            if (!dropdown.classList.contains('active')) return;

            const items = dropdown.querySelectorAll('.lsd-item, .lsd-item-simple');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, -1);
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                if (activeIndex >= 0 && activeIndex < items.length) {
                    e.preventDefault();
                    items[activeIndex].click();
                }
                // If no item selected, let the form submit normally
            } else if (e.key === 'Escape') {
                hideDropdown();
                input.blur();
            }
        });

        // Click outside → close
        document.addEventListener('click', function (e) {
            if (container && !container.contains(e.target)) {
                hideDropdown();
            }
        });

        // Prevent form submit from also triggering when item is clicked
        if (form) {
            form.addEventListener('submit', function () {
                hideDropdown();
            });
        }

        async function fetchResults(q) {
            // Abort previous request
            if (abortController) abortController.abort();
            abortController = new AbortController();

            try {
                const res = await fetch('/api/events/search?q=' + encodeURIComponent(q), {
                    signal: abortController.signal,
                    headers: { 'Accept': 'application/json' },
                });
                const json = await res.json();

                if (!json || !json.data) {
                    renderEmpty(q);
                    return;
                }

                const { events, locations, categories } = json.data;
                const hasResults = (events && events.length) ||
                                   (locations && locations.length) ||
                                   (categories && categories.length);

                if (!hasResults) {
                    renderEmpty(q);
                } else {
                    renderResults(events || [], locations || [], categories || [], q);
                }
            } catch (err) {
                if (err.name === 'AbortError') return; // Ignored
                console.error('Live search error:', err);
                renderEmpty(q);
            }
        }

        function renderResults(events, locations, categories, q) {
            let html = '';

            // Events section
            if (events.length) {
                html += `<div class="lsd-section">
                    <div class="lsd-section-header">
                        <i data-lucide="calendar" class="lsd-section-icon"></i>
                        Events
                    </div>`;
                events.forEach(ev => {
                    html += `<a href="/order/${ev.id}" class="lsd-item lsd-item-event">
                        <img src="${ev.image}" alt="" class="lsd-thumb" loading="lazy">
                        <div class="lsd-item-info">
                            <div class="lsd-item-title">${highlightText(ev.nama, q)}</div>
                            <div class="lsd-item-meta">
                                <span><i data-lucide="calendar" class="w-3 h-3"></i> ${ev.tanggal}</span>
                                <span><i data-lucide="map-pin" class="w-3 h-3"></i> ${ev.lokasi}</span>
                            </div>
                        </div>
                        <div class="lsd-item-price">${ev.harga}</div>
                    </a>`;
                });
                html += '</div>';
            }

            // Locations section
            if (locations.length) {
                html += `<div class="lsd-section">
                    <div class="lsd-section-header">
                        <i data-lucide="map-pin" class="lsd-section-icon"></i>
                        Lokasi
                    </div>`;
                locations.forEach(loc => {
                    html += `<a href="/events?lokasi=${encodeURIComponent(loc.lokasi)}" class="lsd-item-simple">
                        <div class="lsd-simple-icon loc-icon">
                            <i data-lucide="map-pin"></i>
                        </div>
                        <div class="lsd-simple-text">
                            <div class="lsd-simple-name">${highlightText(loc.lokasi, q)}</div>
                            <div class="lsd-simple-count">${loc.count} event tersedia</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            // Categories section
            if (categories.length) {
                html += `<div class="lsd-section">
                    <div class="lsd-section-header">
                        <i data-lucide="tag" class="lsd-section-icon"></i>
                        Kategori
                    </div>`;
                categories.forEach(cat => {
                    html += `<a href="/events?kategori=${encodeURIComponent(cat.kategori)}" class="lsd-item-simple">
                        <div class="lsd-simple-icon cat-icon">
                            <i data-lucide="tag"></i>
                        </div>
                        <div class="lsd-simple-text">
                            <div class="lsd-simple-name">${highlightText(cat.kategori, q)}</div>
                            <div class="lsd-simple-count">${cat.count} event tersedia</div>
                        </div>
                    </a>`;
                });
                html += '</div>';
            }

            // Footer: "See all results" link
            html += `<div class="lsd-footer">
                <a href="/events?q=${encodeURIComponent(q)}">Lihat semua hasil untuk "${escapeHtml(q)}" →</a>
            </div>`;

            dropdown.innerHTML = html;
            activeIndex = -1;
            showDropdown();

            // Re-initialize Lucide icons inside dropdown
            if (window.lucide) window.lucide.createIcons({ nodes: [dropdown] });
        }

        function renderEmpty(q) {
            dropdown.innerHTML = `
                <div class="lsd-empty">
                    <div class="lsd-empty-icon">
                        <i data-lucide="search-x"></i>
                    </div>
                    <div class="lsd-empty-title">Tidak Ditemukan</div>
                    <div class="lsd-empty-desc">Tidak ada event, lokasi, atau kategori yang cocok dengan "${escapeHtml(q)}"</div>
                </div>`;
            activeIndex = -1;
            showDropdown();
            if (window.lucide) window.lucide.createIcons({ nodes: [dropdown] });
        }

        function showLoading() {
            dropdown.innerHTML = `
                <div class="lsd-loading">
                    <div class="lsd-spinner"></div>
                    Mencari...
                </div>`;
            showDropdown();
        }

        function showDropdown() {
            dropdown.classList.add('active');
        }

        function hideDropdown() {
            dropdown.classList.remove('active');
            activeIndex = -1;
        }

        function updateActiveItem(items) {
            items.forEach(item => item.classList.remove('lsd-active'));
            if (activeIndex >= 0 && activeIndex < items.length) {
                items[activeIndex].classList.add('lsd-active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        function highlightText(text, query) {
            if (!query) return escapeHtml(text);
            const escaped = escapeHtml(text);
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            return escaped.replace(regex, '<span class="lsd-highlight">$1</span>');
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function escapeRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    })();

    // Limit ticket qty
    const qtyInput = document.querySelector('input[type="number"][max="5"]');
    if (qtyInput) {
        qtyInput.addEventListener('input', (e) => {
            if (e.target.value > 5) e.target.value = 5;
            if (e.target.value < 1) e.target.value = 1;
        });
    }
});

// --- Create Event Logic ---

let ticketsData = [];
let editingTicketId = null;

function switchTab(tab) {
    const ticketsTab = document.getElementById('tab-tickets');
    const descTab = document.getElementById('tab-description');
    const ticketsContent = document.getElementById('content-tickets');
    const descContent = document.getElementById('content-description');

    if (!ticketsTab || !descTab || !ticketsContent || !descContent) return;

    if (tab === 'tickets') {
        ticketsTab.classList.add('border-rust', 'text-rust');
        ticketsTab.classList.remove('border-transparent', 'text-gray-400');
        descTab.classList.remove('border-rust', 'text-rust');
        descTab.classList.add('border-transparent', 'text-gray-400');
        ticketsContent.classList.remove('hidden');
        descContent.classList.add('hidden');
    } else {
        descTab.classList.add('border-rust', 'text-rust');
        descTab.classList.remove('border-transparent', 'text-gray-400');
        ticketsTab.classList.remove('border-rust', 'text-rust');
        ticketsTab.classList.add('border-transparent', 'text-gray-400');
        descContent.classList.remove('hidden');
        ticketsContent.classList.add('hidden');
    }
}

function openTicketModal(type, eventOrTicket) {
    const modal = document.getElementById('ticket-modal');
    const modalContent = modal ? modal.querySelector('.animate-fade-in-up') : null;
    const priceField = document.getElementById('price-field');
    const btnCreate = document.getElementById('btn-create-ticket');
    
    if (!modal || !modalContent) return;

    // Determine if we're editing an existing ticket (object with id, name, etc.)
    const isEditing = eventOrTicket && typeof eventOrTicket === 'object' && eventOrTicket.id && eventOrTicket.name;
    editingTicketId = isEditing ? eventOrTicket.id : null;
    
    // Reset modal content styles to let CSS handle centering
    modalContent.style.position = '';
    modalContent.style.top = '';
    modalContent.style.left = '';
    modalContent.style.transform = '';
    modalContent.style.margin = '';
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
    
    if (type === 'free') {
        if (priceField) priceField.classList.add('hidden');
        if (btnCreate) btnCreate.textContent = isEditing ? 'Simpan Perubahan' : 'Buat Tiket Gratis';
    } else {
        if (priceField) priceField.classList.remove('hidden');
        if (btnCreate) btnCreate.textContent = isEditing ? 'Simpan Perubahan' : 'Buat Tiket Berbayar';
    }

    // Reset inputs first
    modal.querySelectorAll('input').forEach(input => {
        if (input.type === 'number') input.value = '0';
        else if (input.type === 'date' || input.type === 'time') input.value = '';
        else input.value = '';
    });
    modal.querySelectorAll('textarea').forEach(ta => ta.value = '');

    // If editing, pre-fill fields
    if (isEditing) {
        const ticketData = eventOrTicket;
        const nameInput = modal.querySelector('input[placeholder*="Nama"], input[placeholder*="Maksimal"], input[placeholder*="Early Bird"], input[placeholder*="Contoh"]');
        const qtyInput = modal.querySelector('input[type="number"]');
        const priceInput = priceField ? priceField.querySelector('input') : null;

        if (nameInput) nameInput.value = ticketData.name || '';
        if (qtyInput) qtyInput.value = ticketData.quantity || 0;
        if (type === 'paid' && priceInput) {
            priceInput.value = 'Rp ' + (ticketData.price || 0).toLocaleString('id-ID');
        }

        // Pre-fill sale dates
        const dateInputs = modal.querySelectorAll('#modal-content-sales input[type="date"]');
        const timeInputs = modal.querySelectorAll('#modal-content-sales input[type="time"]');
        if (ticketData.saleStartDate && dateInputs[0]) dateInputs[0].value = ticketData.saleStartDate;
        if (ticketData.saleStartTime && timeInputs[0]) timeInputs[0].value = ticketData.saleStartTime;
        if (ticketData.saleEndDate && dateInputs[1]) dateInputs[1].value = ticketData.saleEndDate;
        if (ticketData.saleEndTime && timeInputs[1]) timeInputs[1].value = ticketData.saleEndTime;
    }
    
    switchModalTab('detail');
    validateTicketForm();
}

function closeTicketModal() {
    const modal = document.getElementById('ticket-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scroll
        
        // Reset inputs
        const inputs = modal.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.type === 'number') input.value = 0;
            else if (input.type !== 'date') input.value = '';
            
            if (input.placeholder && input.placeholder.includes('Rp')) input.value = 'Rp';
        });
    }
}

function createTicket() {
    const modal = document.getElementById('ticket-modal');
    if (!modal) return;

    const nameInput = modal.querySelector('input[placeholder*="Nama"], input[placeholder*="Maksimal"], input[placeholder*="Early Bird"], input[placeholder*="Contoh"]');
    const qtyInput = modal.querySelector('input[type="number"]');
    const priceField = document.getElementById('price-field');
    const priceInput = priceField ? priceField.querySelector('input') : null;
    const isFree = priceField && priceField.classList.contains('hidden');
    
    const name = nameInput ? nameInput.value.trim() : '';
    const quantity = qtyInput ? parseInt(qtyInput.value) : 0;
    const price = isFree ? 0 : (priceInput ? parseInt(priceInput.value.replace(/[^0-9]/g, '')) : 0);

    // Capture sale date/time
    const dateInputs = modal.querySelectorAll('#modal-content-sales input[type="date"]');
    const timeInputs = modal.querySelectorAll('#modal-content-sales input[type="time"]');
    const saleStartDate = dateInputs[0] ? dateInputs[0].value : '';
    const saleStartTime = timeInputs[0] ? timeInputs[0].value : '';
    const saleEndDate = dateInputs[1] ? dateInputs[1].value : '';
    const saleEndTime = timeInputs[1] ? timeInputs[1].value : '';

    const ticket = {
        id: editingTicketId || Date.now(),
        type: isFree ? 'free' : 'paid',
        name,
        quantity,
        price,
        saleStartDate,
        saleStartTime,
        saleEndDate,
        saleEndTime
    };

    if (editingTicketId) {
        const idx = ticketsData.findIndex(t => t.id === editingTicketId);
        if (idx !== -1) ticketsData[idx] = ticket;
        editingTicketId = null;
    } else {
        ticketsData.push(ticket);
    }

    renderTickets();
    closeTicketModal();
}

function formatTicketDate(dateStr, timeStr) {
    if (!dateStr) return '-';
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const parts = dateStr.split('-');
    const day = parseInt(parts[2]);
    const month = months[parseInt(parts[1]) - 1];
    const year = parts[0];
    let result = `${day} ${month} ${year}`;
    if (timeStr) result += `, ${timeStr}`;
    return result;
}

function renderTickets() {
    const list = document.getElementById('added-tickets-list');
    const container = document.getElementById('added-tickets-container');
    if (!list || !container) return;

    if (ticketsData.length > 0) {
        container.classList.remove('hidden');
        list.innerHTML = ticketsData.map(ticket => {
            const saleStart = formatTicketDate(ticket.saleStartDate, ticket.saleStartTime);
            const saleEnd = formatTicketDate(ticket.saleEndDate, ticket.saleEndTime);
            const typeBadge = ticket.type === 'free'
                ? '<span class="inline-block px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Gratis</span>'
                : '<span class="inline-block px-2 py-0.5 bg-rust/10 text-rust text-[10px] font-bold rounded-full uppercase tracking-wider">Berbayar</span>';

            return `
            <div class="group p-5 bg-white border border-gray-100 rounded-2xl hover:border-rust/30 hover:shadow-md transition-all duration-300 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        <div class="w-11 h-11 ${ticket.type === 'free' ? 'bg-green-50 text-green-500' : 'bg-rust/10 text-rust'} rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${ticket.type === 'free' ? '<polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>' : '<rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20"/><path d="M6 14h.01"/><path d="M10 14h.01"/>'}</svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <span class="font-bold text-ink text-base truncate">${ticket.name}</span>
                                ${typeBadge}
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mb-2">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                                    ${ticket.quantity} tiket
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-ink">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    ${ticket.type === 'free' ? 'Gratis' : 'Rp ' + ticket.price.toLocaleString('id-ID')}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 text-[11px] text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <span class="font-medium text-gray-500">Mulai:</span> ${saleStart}
                                </span>
                                <span class="hidden sm:inline text-gray-300">→</span>
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <span class="font-medium text-gray-500">Berakhir:</span> ${saleEnd}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button onclick="editTicket(${ticket.id})" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-rust/10 hover:border-rust/30 flex items-center justify-center text-gray-400 hover:text-rust transition-all shadow-sm" title="Edit tiket">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </button>
                        <button onclick="removeTicket(${ticket.id})" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-red-50 hover:border-red-200 flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm" title="Hapus tiket">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    } else {
        container.classList.add('hidden');
    }
}

function removeTicket(id) {
    ticketsData = ticketsData.filter(t => t.id !== id);
    renderTickets();
}

function editTicket(id) {
    const ticket = ticketsData.find(t => t.id === id);
    if (!ticket) return;
    openTicketModal(ticket.type || 'paid', ticket);
}

function validateTicketForm() {
    const modal = document.getElementById('ticket-modal');
    if (!modal) return;

    const nameInput = modal.querySelector('input[placeholder*="Nama"], input[placeholder*="Maksimal"], input[placeholder*="Early Bird"], input[placeholder*="Contoh"]');
    const qtyInput = modal.querySelector('input[type="number"]');
    const priceField = document.getElementById('price-field');
    const priceInput = priceField ? priceField.querySelector('input') : null;
    const btnNext = modal.querySelector('button[onclick*="switchModalTab(\'sales\')"]');
    const btnCreate = document.getElementById('btn-create-ticket');

    const isFree = priceField && priceField.classList.contains('hidden');
    
    const isNameValid = nameInput && nameInput.value.trim().length > 0;
    const isQtyValid = qtyInput && parseInt(qtyInput.value) > 0;
    const isPriceValid = isFree || (priceInput && priceInput.value.replace(/[^0-9]/g, '').length > 0);

    const isValid = isNameValid && isQtyValid && isPriceValid;

    if (btnNext) {
        if (isValid) {
            btnNext.classList.remove('opacity-50', 'cursor-not-allowed');
            btnNext.disabled = false;
        } else {
            btnNext.classList.add('opacity-50', 'cursor-not-allowed');
            btnNext.disabled = true;
        }
    }

    if (btnCreate) {
        if (isValid) {
            btnCreate.classList.remove('bg-rust/20', 'cursor-not-allowed');
            btnCreate.classList.add('bg-rust');
            btnCreate.disabled = false;
        } else {
            btnCreate.classList.add('bg-rust/20', 'cursor-not-allowed');
            btnCreate.classList.remove('bg-rust');
            btnCreate.disabled = true;
        }
    }
}

// Add event listeners for validation
window.addEventListener('load', () => {
    const modal = document.getElementById('ticket-modal');
    if (modal) {
        const inputs = modal.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', validateTicketForm);
        });

        const btnCreate = document.getElementById('btn-create-ticket');
        if (btnCreate) {
            btnCreate.addEventListener('click', createTicket);
        }
    }
});

function switchModalTab(tab) {
    if (tab === 'sales') {
        // Double check validation before switching
        const priceField = document.getElementById('price-field');
        const modal = document.getElementById('ticket-modal');
        const nameInput = modal.querySelector('input[placeholder*="Nama"], input[placeholder*="Maksimal"]');
        const qtyInput = modal.querySelector('input[type="number"]');
        const priceInput = priceField ? priceField.querySelector('input') : null;
        const isFree = priceField && priceField.classList.contains('hidden');
        
        const isValid = (nameInput && nameInput.value.trim().length > 0) && 
                        (qtyInput && parseInt(qtyInput.value) > 0) && 
                        (isFree || (priceInput && priceInput.value.replace(/[^0-9]/g, '').length > 0));
        
        if (!isValid) return;
    }

    const detailTab = document.getElementById('modal-tab-detail');
    const salesTab = document.getElementById('modal-tab-sales');
    const detailContent = document.getElementById('modal-content-detail');
    const salesContent = document.getElementById('modal-content-sales');

    if (!detailTab || !salesTab || !detailContent || !salesContent) return;

    if (tab === 'detail') {
        detailTab.classList.add('border-rust', 'text-rust');
        detailTab.classList.remove('border-transparent', 'text-gray-400');
        salesTab.classList.remove('border-rust', 'text-rust');
        salesTab.classList.add('border-transparent', 'text-gray-400');
        detailContent.classList.remove('hidden');
        salesContent.classList.add('hidden');
    } else {
        salesTab.classList.add('border-rust', 'text-rust');
        salesTab.classList.remove('border-transparent', 'text-gray-400');
        detailTab.classList.remove('border-rust', 'text-rust');
        detailTab.classList.add('border-transparent', 'text-gray-400');
        salesContent.classList.remove('hidden');
        detailContent.classList.add('hidden');
    }
}

// Global functions
window.scrollCarousel = scrollCarousel;
window.toggleRole = toggleRole;
window.toggleRoleAndRedirect = toggleRoleAndRedirect;
// window.logout is provided by api-helper.js
window.checkAuthState = checkAuthState;
window.switchTab = switchTab;
window.openTicketModal = openTicketModal;
window.closeTicketModal = closeTicketModal;
window.switchModalTab = switchModalTab;
window.removeTicket = removeTicket;
window.createTicket = createTicket;
window.editTicket = editTicket;

// --- Support Pages Logic ---

// Fungsi untuk Akordion FAQ di halaman Bantuan
function initAccordions() {
    document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', () => {
            const answer = item.nextElementSibling;
            const icon = item.querySelector('span');

            if (answer.style.maxHeight) {
                answer.style.maxHeight = null;
                answer.style.paddingBottom = "0";
                icon.innerText = "⌄";
            } else {
                answer.style.maxHeight = answer.scrollHeight + "px";
                answer.style.paddingBottom = "20px";
                icon.innerText = "⌃";
            }
        });
    });
}

// Simulasi Kirim Pesan di Hubungi Kami
function initContactForm() {
    const form = document.querySelector('.contact-form');
    if(form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Terima kasih! Pesan Anda telah kami terima.');
            form.reset();
        });
    }
}

// Initialize on load
window.addEventListener('DOMContentLoaded', () => {
    initAccordions();
    initContactForm();
});

console.log("Pentasara Script Loaded Successfully");
