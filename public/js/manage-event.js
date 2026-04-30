/**
 * manage-event.js
 * All client-side logic for the Manage Event page.
 * Extracted from the original static manage-event.html.
 */

// ── State ──
let currentCategory = 'paid';
let currentAction = 'add';

// ── Dashboard Stats ──
// Reads ticket data from the Kategori Tiket table and updates:
//  1. Quick Stats cards (top of manage-event page)
//  2. Laporan Penjualan summary cards
// This ensures all numbers stay in sync across the page.
function updateDashboardStats() {
    const ticketRows = document.querySelectorAll('#manage-tiket tbody tr');
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

    // Calculate transaction revenue from the transaction table
    let txRevenue = 0;
    transactionRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const amountText = cells[3].textContent.replace(/[^0-9]/g, '');
            txRevenue += parseInt(amountText) || 0;
        }
    });

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
    // Daily average (assume 30-day period for demo)
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

    const data = [15,28,25,35,30,45,40,55,50,48,60,65,75,70,85,80,78,90,95,110,105,120,115,130,125,145,140,155,150,148];

    const xScale = d3.scaleLinear().domain([0, data.length - 1]).range([padding, width - padding]);
    const yScale = d3.scaleLinear().domain([0, d3.max(data)]).range([height - padding, padding]);

    const line = d3.line().x((d, i) => xScale(i)).y(d => yScale(d)).curve(d3.curveMonotoneX);
    const area = d3.area().x((d, i) => xScale(i)).y0(height).y1(d => yScale(d)).curve(d3.curveMonotoneX);

    const gradient = svg.append('defs').append('linearGradient')
        .attr('id', 'chart-gradient').attr('x1','0%').attr('y1','0%').attr('x2','0%').attr('y2','100%');
    gradient.append('stop').attr('offset','0%').attr('stop-color','#BD3B2E').attr('stop-opacity',0.1);
    gradient.append('stop').attr('offset','100%').attr('stop-color','#BD3B2E').attr('stop-opacity',0);

    svg.append('path').datum(data).attr('fill','url(#chart-gradient)').attr('d', area);
    svg.append('path').datum(data).attr('fill','none').attr('stroke','#BD3B2E').attr('stroke-width',3).attr('d', line);

    const pointsToShow = [0,7,14,21,28,29];
    svg.selectAll('.dot')
        .data(data.filter((d, i) => pointsToShow.includes(i)))
        .enter().append('circle').attr('class','dot')
        .attr('cx', (d, i) => xScale(pointsToShow[i]))
        .attr('cy', d => yScale(d))
        .attr('r', 4).attr('fill','white').attr('stroke','#BD3B2E').attr('stroke-width',2);
}

// ── Tab Switching ──
function switchManageTab(tabId) {
    document.querySelectorAll('.event-tab').forEach(tab => {
        tab.classList.remove('active');
        const t = tab.innerText.toLowerCase();
        if ((tabId==='info' && t.includes('informasi')) ||
            (tabId==='tiket' && t.includes('tiket')) ||
            (tabId==='penjualan' && t.includes('laporan'))) {
            tab.classList.add('active');
        }
    });
    document.querySelectorAll('.event-tab-content').forEach(c => c.classList.remove('active'));
    const el = document.getElementById('manage-' + tabId);
    if (el) el.classList.add('active');

    if (tabId === 'penjualan') {
        // Recalculate stats when switching to sales report
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
        modal.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') {
                input.value = '0';
            } else if (input.type === 'date') {
                input.value = '';
            } else if (input.type === 'text') {
                // Check if it's the price field
                if (input.closest('#price-field')) {
                    input.value = 'Rp';
                } else {
                    input.value = '';
                }
            } else {
                input.value = '';
            }
        });
        modal.querySelectorAll('textarea').forEach(ta => ta.value = '');

        if (type === 'free') {
            if (priceField) priceField.classList.add('hidden');
            if (btnCreate) btnCreate.textContent = 'Simpan Tiket Gratis';
        } else {
            if (priceField) priceField.classList.remove('hidden');
            if (btnCreate) btnCreate.textContent = 'Simpan Tiket Berbayar';
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
        // Validate detail fields before allowing switch to sales tab
        const modal = document.getElementById('modal-tiket');
        if (!modal) return;

        const nameInput = modal.querySelector('#modal-content-detail input[type="text"]');
        const qtyInput = modal.querySelector('#modal-content-detail input[type="number"]');
        const priceField = document.getElementById('price-field');
        const priceInput = priceField ? priceField.querySelector('input') : null;
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
        detailTab.classList.add('border-rust','text-rust');
        detailTab.classList.remove('border-transparent','text-gray-400');
        salesTab.classList.remove('border-rust','text-rust');
        salesTab.classList.add('border-transparent','text-gray-400');
        detailContent.classList.remove('hidden');
        salesContent.classList.add('hidden');
    } else {
        salesTab.classList.add('border-rust','text-rust');
        salesTab.classList.remove('border-transparent','text-gray-400');
        detailTab.classList.remove('border-rust','text-rust');
        detailTab.classList.add('border-transparent','text-gray-400');
        salesContent.classList.remove('hidden');
        detailContent.classList.add('hidden');
    }
}

// ── Ticket Actions ──
function handleTicketAction(action, category) {
    category = category || 'paid';
    currentAction = action;
    currentCategory = category;

    const section = document.getElementById('ticket-action-section');
    const title = document.getElementById('action-title');
    const btnText = document.getElementById('btn-action-text');
    if (!section) return;

    section.classList.remove('hidden');
    if (action === 'edit') {
        if (title) title.textContent = 'Ubah Kategori Tiket';
        if (btnText) btnText.textContent = 'Lanjut Ubah Detail Tiket';
    } else {
        if (title) title.textContent = 'Tambah Kategori Tiket';
        if (btnText) btnText.textContent = 'Lanjut Atur Detail Tiket';
    }
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
    const modal = document.getElementById('modal-tiket');
    if (!modal) return;

    // Find inputs specifically within the detail content area
    const detailContent = document.getElementById('modal-content-detail');
    if (!detailContent) return;

    const nameInput = detailContent.querySelector('input[placeholder*="Contoh"]');
    const qtyInput = detailContent.querySelector('input[type="number"]');
    const priceField = document.getElementById('price-field');
    const priceInput = priceField ? priceField.querySelector('input') : null;

    // Use specific IDs instead of generic selectors
    const btnNext = document.getElementById('btn-next-tab');
    const btnCreate = document.getElementById('btn-create-ticket');

    const isFree = priceField && priceField.classList.contains('hidden');
    const isNameValid = nameInput && nameInput.value.trim().length > 0;
    const isQtyValid = qtyInput && parseInt(qtyInput.value) > 0;
    const isPriceValid = isFree || (priceInput && parseInt(priceInput.value.replace(/[^0-9]/g, '')) > 0);
    const isDetailValid = isNameValid && isQtyValid && isPriceValid;

    // "Selanjutnya" button — enable/disable based on detail fields
    if (btnNext) {
        if (isDetailValid) {
            btnNext.disabled = false;
            btnNext.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            btnNext.disabled = true;
            btnNext.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // "Simpan Tiket" button — enable/disable based on all fields including dates
    if (btnCreate) {
        const salesContent = document.getElementById('modal-content-sales');
        const dateInputs = salesContent ? salesContent.querySelectorAll('input[type="date"]') : [];
        const startDate = dateInputs[0] ? dateInputs[0].value : '';
        const endDate = dateInputs[1] ? dateInputs[1].value : '';
        const isAllValid = isDetailValid && startDate && endDate;

        if (isAllValid) {
            btnCreate.disabled = false;
            btnCreate.style.opacity = '1';
            btnCreate.style.cursor = 'pointer';
            btnCreate.classList.add('hover:bg-rust-deep');
        } else {
            btnCreate.disabled = true;
            btnCreate.style.opacity = '0.3';
            btnCreate.style.cursor = 'not-allowed';
            btnCreate.classList.remove('hover:bg-rust-deep');
        }
    }
}

function saveEventChanges() {
    alert('Perubahan event berhasil disimpan!');
}

// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
    updateDashboardStats();

    const modal = document.getElementById('modal-tiket');
    if (modal) {
        // Use event delegation for all input/textarea events inside the modal
        modal.addEventListener('input', function(e) {
            if (e.target.matches('input, textarea')) {
                validateTicketForm();
            }
        });
        modal.addEventListener('change', function(e) {
            if (e.target.matches('input, textarea, select')) {
                validateTicketForm();
            }
        });
    }

    // Close modals on overlay click, prevent propagation from container
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        // Prevent clicks inside modal container from closing the modal
        overlay.querySelectorAll('.modal-container, [class*="modal-container"]').forEach(container => {
            container.addEventListener('click', (e) => {
                e.stopPropagation();
            });
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
