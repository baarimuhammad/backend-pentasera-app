/**
 * manage-event.js
 * All client-side logic for the Manage Event page.
 * Uses API calls via api-helper.js for CRUD operations.
 */

// ── State ──
let currentCategory = 'paid';
let currentAction = 'add';
let editingRow = null;

// ── Get Event ID ──
function getEventId() {
    return window.__eventId || document.getElementById('manage-event-root')?.dataset?.eventId;
}

// ── Dashboard Stats ──
function updateDashboardStats() {
    const ticketRows = document.querySelectorAll('#ticket-table-body tr');
    let totalSold = 0;
    let totalCapacity = 0;
    let totalRevenue = 0;

    ticketRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 5) {
            const priceText = cells[1].textContent.replace(/[^0-9]/g, '');
            const capacityText = cells[2].textContent.replace(/[^0-9]/g, '');
            const soldText = cells[3].textContent.replace(/[^0-9]/g, '');
            const price = parseInt(priceText) || 0;
            const capacity = parseInt(capacityText) || 0;
            const sold = parseInt(soldText) || 0;
            totalSold += sold;
            totalCapacity += capacity;
            totalRevenue += (sold * price);
        }
    });

    // Count transactions from the transaction table
    const transactionRows = document.querySelectorAll('#dash-transaction-table tbody tr');
    const totalTransactions = transactionRows.length;

    // ── 1. Quick Stats Cards (top of page) ──
    const elSold = document.getElementById('stat-tickets-sold');
    const elPercent = document.getElementById('stat-tickets-percent');
    const elSales = document.getElementById('stat-total-sales');
    const elTx = document.getElementById('stat-total-transactions');

    if (elSold) elSold.textContent = `${totalSold.toLocaleString('id-ID')} / ${totalCapacity.toLocaleString('id-ID')}`;
    const percent = totalCapacity > 0 ? Math.round((totalSold / totalCapacity) * 100) : 0;
    if (elPercent) elPercent.textContent = `${percent}% Terisi`;
    if (elSales) elSales.textContent = `Rp ${totalRevenue.toLocaleString('id-ID')}`;
    if (elTx) elTx.textContent = totalTransactions.toLocaleString('id-ID');

    // ── 2. Laporan Penjualan Summary Cards ──
    const reportRevenue = document.getElementById('report-total-revenue');
    const reportTickets = document.getElementById('report-total-tickets');
    const reportDaily = document.getElementById('report-daily-avg');

    if (reportRevenue) reportRevenue.textContent = `Rp ${totalRevenue.toLocaleString('id-ID')}`;
    if (reportTickets) reportTickets.textContent = totalSold.toLocaleString('id-ID');
    const dailyAvg = totalRevenue > 0 ? Math.round(totalRevenue / 30) : 0;
    if (reportDaily) reportDaily.textContent = `Rp ${dailyAvg.toLocaleString('id-ID')}`;
}

// ── D3 Sales Chart ──
function renderSalesChart() {
    const container = document.getElementById('chart-container');
    if (!container) return;
    container.innerHTML = '';

    const width = container.clientWidth;
    const height = container.clientHeight;
    const padding = 20;

    const svg = d3.select('#chart-container')
        .append('svg')
        .attr('width', '100%')
        .attr('height', '100%')
        .attr('viewBox', `0 0 ${width} ${height}`)
        .attr('preserveAspectRatio', 'none');

    // Use real sales data from server, fallback to zeros if not available
    const chartData = window.__chartData || [];
    const data = chartData.map(d => d.revenue || 0);

    // If all values are zero, show a flat line at the bottom
    const maxVal = d3.max(data) || 1;

    const xScale = d3.scaleLinear().domain([0, data.length - 1]).range([padding, width - padding]);
    const yScale = d3.scaleLinear().domain([0, maxVal]).range([height - padding, padding]);

    const line = d3.line().x((d, i) => xScale(i)).y(d => yScale(d)).curve(d3.curveMonotoneX);
    const area = d3.area().x((d, i) => xScale(i)).y0(height).y1(d => yScale(d)).curve(d3.curveMonotoneX);

    const gradient = svg.append('defs').append('linearGradient')
        .attr('id', 'chart-gradient').attr('x1', '0%').attr('y1', '0%').attr('x2', '0%').attr('y2', '100%');
    gradient.append('stop').attr('offset', '0%').attr('stop-color', '#BD3B2E').attr('stop-opacity', 0.1);
    gradient.append('stop').attr('offset', '100%').attr('stop-color', '#BD3B2E').attr('stop-opacity', 0);

    svg.append('path').datum(data).attr('fill', 'url(#chart-gradient)').attr('d', area);
    svg.append('path').datum(data).attr('fill', 'none').attr('stroke', '#BD3B2E').attr('stroke-width', 3).attr('d', line);

    // Show dots at weekly intervals + last point
    const pointsToShow = [0, 7, 14, 21, Math.min(28, data.length - 1), data.length - 1]
        .filter((v, i, a) => a.indexOf(v) === i); // deduplicate
    svg.selectAll('.dot')
        .data(pointsToShow.map(i => ({ value: data[i], index: i })))
        .enter().append('circle').attr('class', 'dot')
        .attr('cx', d => xScale(d.index))
        .attr('cy', d => yScale(d.value))
        .attr('r', 4).attr('fill', 'white').attr('stroke', '#BD3B2E').attr('stroke-width', 2);

    // Populate x-axis date labels
    const labelsContainer = document.getElementById('chart-x-labels');
    if (labelsContainer && chartData.length > 0) {
        labelsContainer.innerHTML = '';
        const labelIndices = [0, 7, 14, 21, chartData.length - 1]
            .filter((v, i, a) => a.indexOf(v) === i);
        labelIndices.forEach(idx => {
            const dateStr = chartData[idx]?.date || '';
            const d = new Date(dateStr);
            const label = isNaN(d.getTime()) ? dateStr : d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            const span = document.createElement('span');
            span.textContent = label;
            labelsContainer.appendChild(span);
        });
    }
}

// ── Tab Switching ──
function switchManageTab(tabId) {
    document.querySelectorAll('.event-tab').forEach(tab => {
        tab.classList.remove('active');
        const t = tab.innerText.toLowerCase();
        if ((tabId === 'info' && t.includes('informasi')) ||
            (tabId === 'tiket' && t.includes('tiket')) ||
            (tabId === 'penjualan' && t.includes('laporan'))) {
            tab.classList.add('active');
        }
    });
    document.querySelectorAll('.event-tab-content').forEach(c => c.classList.remove('active'));
    const el = document.getElementById('manage-' + tabId);
    if (el) el.classList.add('active');

    if (tabId === 'penjualan') {
        updateDashboardStats();
        setTimeout(() => {
            renderSalesChart();
            if (window.lucide) window.lucide.createIcons();
        }, 100);
    }
}

// ── Modals ──
function openModal(id, type) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    if (id === 'modal-tiket') {
        const priceField = document.getElementById('price-field');
        const btnCreate = document.getElementById('btn-create-ticket');

        // Reset all input fields inside the modal
        document.getElementById('ticket-name').value = '';
        document.getElementById('ticket-qty').value = '0';
        document.getElementById('ticket-price').value = 'Rp';
        document.getElementById('ticket-desc').value = '';
        document.getElementById('ticket-start-date').value = '';
        document.getElementById('ticket-end-date').value = '';
        document.getElementById('ticket-start-time').value = '09:00';
        document.getElementById('ticket-end-time').value = '18:00';

        if (type === 'free') {
            if (priceField) priceField.classList.add('hidden');
            if (btnCreate) btnCreate.textContent = 'Simpan Tiket Gratis';
        } else {
            if (priceField) priceField.classList.remove('hidden');
            if (btnCreate) btnCreate.textContent = 'Simpan Tiket Berbayar';
        }

        // If editing, pre-fill from the existing row and ticket data
        if (currentAction === 'edit' && editingRow) {
            const cells = editingRow.querySelectorAll('td');
            if (cells.length >= 5) {
                document.getElementById('ticket-name').value = cells[0].textContent.trim();
                document.getElementById('ticket-qty').value = cells[2].textContent.trim();
                if (type !== 'free') {
                    document.getElementById('ticket-price').value = cells[1].textContent.trim();
                }
            }

            // Pre-fill sale dates and times from ticket data in window.__eventData
            const ticketId = editingRow.dataset.ticketId;
            if (ticketId && window.__eventData && window.__eventData.tickets) {
                const ticketData = window.__eventData.tickets.find(t => String(t.id) === String(ticketId));
                if (ticketData) {
                    if (ticketData.sale_start) {
                        const startDt = new Date(ticketData.sale_start);
                        if (!isNaN(startDt.getTime())) {
                            document.getElementById('ticket-start-date').value = startDt.toISOString().slice(0, 10);
                            document.getElementById('ticket-start-time').value = startDt.toTimeString().slice(0, 5);
                        }
                    }
                    if (ticketData.sale_end) {
                        const endDt = new Date(ticketData.sale_end);
                        if (!isNaN(endDt.getTime())) {
                            document.getElementById('ticket-end-date').value = endDt.toISOString().slice(0, 10);
                            document.getElementById('ticket-end-time').value = endDt.toTimeString().slice(0, 5);
                        }
                    }
                }
            }

            if (btnCreate) btnCreate.textContent = type === 'free' ? 'Simpan Perubahan' : 'Simpan Perubahan';
        }

        // Reset to detail tab
        switchModalTab('detail');

        // Reset button states
        const btnNext = document.getElementById('btn-next-tab');
        if (btnNext) {
            btnNext.disabled = true;
            btnNext.classList.add('opacity-50', 'cursor-not-allowed');
        }
        if (btnCreate) {
            btnCreate.disabled = true;
            btnCreate.style.opacity = '0.3';
            btnCreate.style.cursor = 'not-allowed';
        }

        // Run initial validation
        validateTicketForm();
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function switchModalTab(tab) {
    if (tab === 'sales') {
        const nameInput = document.getElementById('ticket-name');
        const qtyInput = document.getElementById('ticket-qty');
        const priceField = document.getElementById('price-field');
        const priceInput = document.getElementById('ticket-price');
        const isFree = priceField && priceField.classList.contains('hidden');

        const isNameValid = nameInput && nameInput.value.trim().length > 0;
        const isQtyValid = qtyInput && parseInt(qtyInput.value) > 0;
        const isPriceValid = isFree || (priceInput && parseInt(priceInput.value.replace(/[^0-9]/g, '')) > 0);

        if (!(isNameValid && isQtyValid && isPriceValid)) return;
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

// ── Ticket Actions ──
function handleTicketAction(action, category, btnEl) {
    category = category || 'paid';
    currentAction = action;
    currentCategory = category;

    const section = document.getElementById('ticket-action-section');
    const title = document.getElementById('action-title');
    const btnText = document.getElementById('btn-action-text');
    if (!section) return;

    // For edit mode, find the row and store it
    if (action === 'edit' && btnEl) {
        editingRow = btnEl.closest('tr');
    } else {
        editingRow = null;
    }

    // For edit mode, directly open the modal to reduce confusion
    if (action === 'edit') {
        openModal('modal-tiket', category);
        return;
    }

    section.classList.remove('hidden');
    if (title) title.textContent = 'Tambah Kategori Tiket';
    if (btnText) btnText.textContent = 'Lanjut Atur Detail Tiket';
    setTicketCategory(category);
    setTimeout(() => { section.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
}

function setTicketCategory(category) {
    currentCategory = category;
    const paidCard = document.getElementById('card-category-paid');
    const freeCard = document.getElementById('card-category-free');
    const paidIcon = document.getElementById('status-icon-paid');
    const freeIcon = document.getElementById('status-icon-free');
    const paidLabel = document.getElementById('label-tag-paid');
    const freeLabel = document.getElementById('label-tag-free');
    if (!paidCard || !freeCard) return;

    if (category === 'paid') {
        paidCard.className = 'relative border-2 rounded-[2.5rem] min-h-[56px] px-5 py-8 flex items-center justify-between transition-all cursor-pointer group overflow-hidden border-rust bg-rust/5 shadow-2xl shadow-rust/10';
        freeCard.className = 'relative border-2 border-transparent bg-gray-50/50 rounded-[2.5rem] min-h-[56px] px-5 py-8 flex items-center justify-between transition-all cursor-pointer group overflow-hidden hover:bg-white hover:border-rust/20';
        paidIcon.innerHTML = '<i data-lucide="check" class="w-6 h-6"></i>';
        paidIcon.className = 'w-12 h-12 rounded-full bg-rust text-white flex items-center justify-center shadow-lg shadow-rust/40';
        freeIcon.innerHTML = '<i data-lucide="plus" class="w-6 h-6"></i>';
        freeIcon.className = 'w-12 h-12 rounded-full border-2 border-gray-100 flex items-center justify-center text-gray-300 group-hover:border-rust group-hover:text-rust transition-all';
        if (paidLabel) { paidLabel.textContent = currentAction === 'edit' ? 'Current' : 'Pilih'; paidLabel.className = 'text-[10px] font-bold text-rust uppercase tracking-[0.2em] mb-2'; }
        if (freeLabel) { freeLabel.textContent = 'Opsi Lain'; freeLabel.className = 'text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2'; }
        const pInk = paidCard.querySelector('.text-ink'); if (pInk) pInk.classList.remove('text-gray-300');
        const fInk = freeCard.querySelector('.text-ink'); if (fInk) fInk.classList.add('text-gray-300');
    } else {
        freeCard.className = 'relative border-2 rounded-[2.5rem] min-h-[56px] px-5 py-8 flex items-center justify-between transition-all cursor-pointer group overflow-hidden border-rust bg-rust/5 shadow-2xl shadow-rust/10';
        paidCard.className = 'relative border-2 border-transparent bg-gray-50/50 rounded-[2.5rem] min-h-[56px] px-5 py-8 flex items-center justify-between transition-all cursor-pointer group overflow-hidden hover:bg-white hover:border-rust/20';
        freeIcon.innerHTML = '<i data-lucide="check" class="w-6 h-6"></i>';
        freeIcon.className = 'w-12 h-12 rounded-full bg-rust text-white flex items-center justify-center shadow-lg shadow-rust/40';
        paidIcon.innerHTML = '<i data-lucide="plus" class="w-6 h-6"></i>';
        paidIcon.className = 'w-12 h-12 rounded-full border-2 border-gray-100 flex items-center justify-center text-gray-300 group-hover:border-rust group-hover:text-rust transition-all';
        if (freeLabel) { freeLabel.textContent = currentAction === 'edit' ? 'Current' : 'Pilih'; freeLabel.className = 'text-[10px] font-bold text-rust uppercase tracking-[0.2em] mb-2'; }
        if (paidLabel) { paidLabel.textContent = 'Opsi Lain'; paidLabel.className = 'text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2'; }
        const fInk = freeCard.querySelector('.text-ink'); if (fInk) fInk.classList.remove('text-gray-300');
        const pInk = paidCard.querySelector('.text-ink'); if (pInk) pInk.classList.add('text-gray-300');
    }
    if (window.lucide) lucide.createIcons();
}

// ── Ticket Form Validation ──
function validateTicketForm() {
    const nameInput = document.getElementById('ticket-name');
    const qtyInput = document.getElementById('ticket-qty');
    const priceField = document.getElementById('price-field');
    const priceInput = document.getElementById('ticket-price');
    const startDate = document.getElementById('ticket-start-date');
    const endDate = document.getElementById('ticket-end-date');
    const btnNext = document.getElementById('btn-next-tab');
    const btnCreate = document.getElementById('btn-create-ticket');

    const isFree = priceField && priceField.classList.contains('hidden');
    const isNameValid = nameInput && nameInput.value.trim().length > 0;
    const isQtyValid = qtyInput && parseInt(qtyInput.value) > 0;
    const isPriceValid = isFree || (priceInput && parseInt(priceInput.value.replace(/[^0-9]/g, '')) > 0);
    const isDetailValid = isNameValid && isQtyValid && isPriceValid;

    if (btnNext) {
        if (isDetailValid) {
            btnNext.disabled = false;
            btnNext.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            btnNext.disabled = true;
            btnNext.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    if (btnCreate) {
        const hasStart = startDate && startDate.value;
        const hasEnd = endDate && endDate.value;
        const startTime = document.getElementById('ticket-start-time');
        const endTime = document.getElementById('ticket-end-time');
        const hasStartTime = startTime && startTime.value;
        const hasEndTime = endTime && endTime.value;

        // Validate that start is in the future and end is after start
        let dateValid = true;
        let dateErrorMsg = '';
        const dateErrorEl = document.getElementById('manage-date-validation-error');
        const now = new Date();
        if (hasStart && hasStartTime) {
            const startDT = new Date(startDate.value + 'T' + startTime.value);
            if (startDT <= now) {
                dateValid = false;
                dateErrorMsg = 'Waktu mulai harus lebih dari waktu sekarang';
            }
        }
        if (dateValid && hasStart && hasEnd && hasStartTime && hasEndTime) {
            const startDateTime = startDate.value + 'T' + startTime.value;
            const endDateTime = endDate.value + 'T' + endTime.value;
            if (endDateTime <= startDateTime) {
                dateValid = false;
                dateErrorMsg = 'Waktu berakhir harus lebih lambat dari waktu mulai';
            }
        }
        if (dateValid && hasEnd && hasEndTime) {
            const endDT = new Date(endDate.value + 'T' + endTime.value);
            if (endDT <= now) {
                dateValid = false;
                dateErrorMsg = 'Waktu berakhir harus lebih dari waktu sekarang';
            }
        }

        if (!dateValid) {
            if (!dateErrorEl) {
                const errorDiv = document.createElement('div');
                errorDiv.id = 'manage-date-validation-error';
                errorDiv.className = 'flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl mt-4 text-red-600 text-xs font-bold';
                errorDiv.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> ' + dateErrorMsg;
                const salesContent = document.getElementById('modal-content-sales');
                const spaceDiv = salesContent ? salesContent.querySelector('.space-y-8') : null;
                if (spaceDiv) spaceDiv.appendChild(errorDiv);
            } else {
                dateErrorEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> ' + dateErrorMsg;
            }
        } else {
            if (dateErrorEl) dateErrorEl.remove();
        }

        const isAllValid = isDetailValid && hasStart && hasEnd && hasStartTime && hasEndTime && dateValid;

        if (isAllValid) {
            btnCreate.disabled = false;
            btnCreate.style.opacity = '1';
            btnCreate.style.cursor = 'pointer';
        } else {
            btnCreate.disabled = true;
            btnCreate.style.opacity = '0.3';
            btnCreate.style.cursor = 'not-allowed';
        }
    }
}

// ── Notification helper ──
function showNotification(message, type = 'success') {
    const existing = document.getElementById('manage-notification');
    if (existing) existing.remove();

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
    };

    const notif = document.createElement('div');
    notif.id = 'manage-notification';
    notif.className = `fixed top-6 right-6 z-[9999] ${colors[type] || colors.info} text-white px-6 py-4 rounded-xl shadow-2xl text-sm font-bold flex items-center gap-3 animate-fade-in-up`;
    notif.innerHTML = `<span>${message}</span>`;
    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.opacity = '0';
        notif.style.transform = 'translateY(-10px)';
        notif.style.transition = 'all 0.3s';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

// ── Save Event Changes (API) ──
async function saveEventChanges() {
    const eventId = getEventId();
    if (!eventId) return;

    const btn = document.getElementById('btn-save-event');
    const originalText = btn ? btn.textContent : '';
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
    }

    try {
        const formData = new FormData();

        const namaEvent = document.getElementById('manage-nama-event')?.value?.trim();
        const kategoriEvent = document.getElementById('manage-kategori-event')?.value;
        const eventDatetime = document.getElementById('manage-event-datetime')?.value;
        const lokasi = document.getElementById('manage-lokasi')?.value?.trim();
        const deskripsi = document.getElementById('manage-deskripsi')?.value?.trim();

        if (namaEvent) formData.append('nama_event', namaEvent);
        if (kategoriEvent) formData.append('kategori_event', kategoriEvent);
        if (eventDatetime) formData.append('event_datetime', eventDatetime);
        if (lokasi) formData.append('lokasi', lokasi);
        if (deskripsi !== undefined) formData.append('deskripsi', deskripsi || '');

        // Pengaturan Tambahan
        const maxTicketInput = document.getElementById('manage-max-ticket');
        const oneEmailInput = document.getElementById('manage-one-email');
        const singleIdentityInput = document.getElementById('manage-single-identity');

        if (maxTicketInput) {
            let maxVal = parseInt(maxTicketInput.value) || 5;
            maxVal = Math.max(1, Math.min(5, maxVal));
            formData.append('max_ticket_per_transaction', maxVal);
        }
        if (oneEmailInput) {
            formData.append('one_email_one_transaction', oneEmailInput.checked ? '1' : '0');
        }
        if (singleIdentityInput) {
            formData.append('single_identity_per_ticket', singleIdentityInput.checked ? '1' : '0');
        }

        // Include banner image if a new file was selected
        const bannerInput = document.getElementById('manage-banner-input');
        if (bannerInput && bannerInput.files && bannerInput.files[0]) {
            formData.append('image', bannerInput.files[0]);
        }

        const res = await apiUpload('/events/' + eventId + '/update', formData);

        if (res && res._ok) {
            showNotification('Perubahan event berhasil disimpan!', 'success');
            // Update the banner preview if a new image was returned
            if (res.data && res.data.image_src) {
                const preview = document.getElementById('manage-banner-preview');
                if (preview) preview.src = res.data.image_src;
            }
        } else {
            const msg = res?.message || 'Gagal menyimpan perubahan';
            showNotification(msg, 'error');
        }
    } catch (err) {
        showNotification('Terjadi kesalahan saat menyimpan', 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }
}

// ── Save / Update Ticket (API) ──
async function saveTicket() {
    const btnCreate = document.getElementById('btn-create-ticket');
    if (btnCreate && btnCreate.disabled) return;

    const name = document.getElementById('ticket-name').value.trim();
    const qty = parseInt(document.getElementById('ticket-qty').value) || 0;
    const priceField = document.getElementById('price-field');
    const isFree = priceField && priceField.classList.contains('hidden');
    const priceRaw = isFree ? 0 : parseInt(document.getElementById('ticket-price').value.replace(/[^0-9]/g, '')) || 0;
    const priceText = isFree ? 'Gratis' : `Rp ${priceRaw.toLocaleString('id-ID')}`;

    // Get sale dates + times and combine them
    const saleStartDate = document.getElementById('ticket-start-date')?.value || null;
    const saleEndDate = document.getElementById('ticket-end-date')?.value || null;
    const saleStartTime = document.getElementById('ticket-start-time')?.value || null;
    const saleEndTime = document.getElementById('ticket-end-time')?.value || null;

    if (!saleStartDate || !saleEndDate || !saleStartTime || !saleEndTime) {
        showNotification('Tanggal dan jam penjualan tiket wajib diisi!', 'error');
        return;
    }

    const saleStart = `${saleStartDate} ${saleStartTime}:00`;
    const saleEnd = `${saleEndDate} ${saleEndTime}:00`;

    // Validate datetimes
    const nowStr = new Date().toISOString().slice(0, 16).replace('T', ' ') + ':00';
    if (saleStart <= nowStr) {
        showNotification('Waktu mulai harus lebih dari waktu sekarang', 'error');
        return;
    }
    if (saleEnd <= saleStart) {
        showNotification('Waktu berakhir harus lebih lambat dari waktu mulai', 'error');
        return;
    }
    if (saleEnd <= nowStr) {
        showNotification('Waktu berakhir harus lebih dari waktu sekarang', 'error');
        return;
    }

    // Show loading
    if (btnCreate) {
        btnCreate.disabled = true;
        btnCreate.textContent = 'Menyimpan...';
    }

    try {
        if (currentAction === 'edit' && editingRow) {
            // Edit existing ticket via API
            const ticketId = editingRow.dataset.ticketId;
            if (ticketId) {
                const updateData = {
                    kategori: name,
                    harga: priceRaw,
                    kuota: qty,
                };
                if (saleStart) updateData.sale_start = saleStart;
                if (saleEnd) updateData.sale_end = saleEnd;

                const res = await apiPatch('/tickets/' + ticketId, updateData);

                if (res && res._ok) {
                    // Update DOM
                    const cells = editingRow.querySelectorAll('td');
                    cells[0].innerHTML = `<span class="font-bold text-ink text-sm">${name}</span>`;
                    cells[1].textContent = priceText;
                    cells[2].textContent = qty;
                    const sold = parseInt(cells[3].textContent) || 0;
                    cells[4].textContent = Math.max(0, qty - sold);
                    showNotification('Tiket berhasil diperbarui!', 'success');
                } else {
                    showNotification(res?.message || 'Gagal memperbarui tiket', 'error');
                    return;
                }
            }
            editingRow = null;
        } else {
            // Add new ticket via API
            const eventId = getEventId();
            const createData = {
                event_id: parseInt(eventId),
                kategori: name,
                harga: priceRaw,
                kuota: qty,
            };
            if (saleStart) createData.sale_start = saleStart;
            if (saleEnd) createData.sale_end = saleEnd;

            const res = await apiPost('/tickets', createData);

            if (res && res._ok) {
                const ticket = res.data;
                const tbody = document.getElementById('ticket-table-body');
                if (!tbody) return;
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-rust/[0.02] transition-colors';
                tr.dataset.ticketId = ticket.id;
                tr.innerHTML = `
                    <td class="px-10 py-7 font-bold text-ink text-sm">${name}</td>
                    <td class="px-10 py-7 text-sm font-medium">${priceText}</td>
                    <td class="px-10 py-7 text-sm text-gray-400">${qty}</td>
                    <td class="px-10 py-7 text-sm font-bold text-rust">0</td>
                    <td class="px-10 py-7 text-sm text-gray-400 font-medium">${qty}</td>
                    <td class="px-10 py-7"><span class="bg-green-50 text-green-600 text-[10px] font-bold px-4 py-1.5 rounded-full border border-green-100 uppercase tracking-wider">Tersedia</span></td>
                    <td class="px-10 py-7 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="handleTicketAction('edit', '${isFree ? 'free' : 'paid'}', this)" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-300 hover:bg-rust/10 hover:text-rust transition-all">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </button>
                            <button onclick="deleteTicket(${ticket.id}, this)" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-300 hover:bg-red-50 hover:text-red-500 transition-all" title="Hapus tiket">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </td>`;
                tbody.appendChild(tr);
                if (window.lucide) lucide.createIcons();
                showNotification('Tiket berhasil ditambahkan!', 'success');
            } else {
                showNotification(res?.message || 'Gagal menambahkan tiket', 'error');
                return;
            }
        }

        closeModal('modal-tiket');
        const section = document.getElementById('ticket-action-section');
        if (section) section.classList.add('hidden');
        updateDashboardStats();
    } catch (err) {
        showNotification('Terjadi kesalahan saat menyimpan tiket', 'error');
    } finally {
        if (btnCreate) {
            btnCreate.disabled = false;
            btnCreate.textContent = isFree ? 'Simpan Tiket Gratis' : 'Simpan Tiket Berbayar';
        }
    }
}

// ── Delete Ticket (API) ──
async function deleteTicket(ticketId, btnEl) {
    if (!confirm('Yakin ingin menghapus tiket ini? Tiket yang sudah memiliki pesanan tidak bisa dihapus.')) {
        return;
    }

    const row = btnEl ? btnEl.closest('tr') : null;

    try {
        const res = await apiDelete('/tickets/' + ticketId);

        if (res && res._ok) {
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                row.style.transition = 'all 0.3s';
                setTimeout(() => {
                    row.remove();
                    updateDashboardStats();
                }, 300);
            }
            showNotification('Tiket berhasil dihapus!', 'success');
        } else {
            showNotification(res?.message || 'Gagal menghapus tiket', 'error');
        }
    } catch (err) {
        showNotification('Terjadi kesalahan saat menghapus tiket', 'error');
    }
}

// ── Banner preview ──
function setupBannerPreview() {
    const input = document.getElementById('manage-banner-input');
    const preview = document.getElementById('manage-banner-preview');
    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            preview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
}

// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
    updateDashboardStats();
    setupBannerPreview();

    const modal = document.getElementById('modal-tiket');
    if (modal) {
        modal.addEventListener('input', function (e) {
            if (e.target.matches('input, textarea')) validateTicketForm();
        });
        modal.addEventListener('change', function (e) {
            if (e.target.matches('input, textarea, select')) validateTicketForm();
        });
    }

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.querySelectorAll('.modal-container, [class*="modal-container"]').forEach(container => {
            container.addEventListener('click', (e) => e.stopPropagation());
        });
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
});

window.addEventListener('resize', () => {
    if (document.getElementById('manage-penjualan') &&
        document.getElementById('manage-penjualan').classList.contains('active')) {
        renderSalesChart();
    }
});
