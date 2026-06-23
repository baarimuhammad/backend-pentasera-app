/**
 * Create Event — Tab Switching, Modal & Ticket Management + API Submission
 * public/js/create-event.js
 */
(function () {
    'use strict';

    const modal = document.getElementById('ticket-modal');
    const priceField = document.getElementById('price-field');
    const btnCreateTicket = document.getElementById('btn-create-ticket');
    const addedTicketsContainer = document.getElementById('added-tickets-container');
    const addedTicketsList = document.getElementById('added-tickets-list');

    let currentTicketType = 'paid';
    let tickets = [];
    let editingTicketId = null;

    // ============================================
    // Auth Guard
    // ============================================
    if (typeof requireAuth === 'function') requireAuth();
    if (typeof requireRole === 'function') requireRole('creator');

    // ============================================
    // Banner Image Upload Preview
    // ============================================
    const bannerInput = document.getElementById('banner-input');
    const bannerPreview = document.getElementById('banner-preview');
    const bannerPreviewContainer = document.getElementById('banner-preview-container');
    const bannerPlaceholder = document.getElementById('banner-placeholder');

    if (bannerInput) {
        bannerInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB');
                    bannerInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (ev) {
                    if (bannerPreview) bannerPreview.src = ev.target.result;
                    if (bannerPreviewContainer) bannerPreviewContainer.classList.remove('hidden');
                    if (bannerPlaceholder) bannerPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================
    // Max Ticket Per Transaction (+/- buttons)
    // ============================================
    const maxTicketValue = document.getElementById('max-ticket-value');
    const maxTicketMinus = document.getElementById('max-ticket-minus');
    const maxTicketPlus = document.getElementById('max-ticket-plus');

    if (maxTicketMinus && maxTicketPlus && maxTicketValue) {
        maxTicketMinus.addEventListener('click', function (e) {
            e.preventDefault();
            let val = parseInt(maxTicketValue.textContent) || 5;
            if (val > 1) {
                maxTicketValue.textContent = val - 1;
            }
        });

        maxTicketPlus.addEventListener('click', function (e) {
            e.preventDefault();
            let val = parseInt(maxTicketValue.textContent) || 5;
            if (val < 5) {
                maxTicketValue.textContent = val + 1;
            }
        });
    }

    // ============================================
    // Tab Switching (Tickets & Description)
    // ============================================
    window.switchTab = function (tab) {
        const tabs = ['tickets', 'description'];
        tabs.forEach(t => {
            const tabBtn = document.getElementById('tab-' + t);
            const content = document.getElementById('content-' + t);
            if (t === tab) {
                tabBtn.classList.add('border-rust', 'text-rust');
                tabBtn.classList.remove('border-transparent', 'text-gray-400');
                content.classList.remove('hidden');
            } else {
                tabBtn.classList.remove('border-rust', 'text-rust');
                tabBtn.classList.add('border-transparent', 'text-gray-400');
                content.classList.add('hidden');
            }
        });
    };

    // ============================================
    // Modal Tab Switching (Detail & Sales Date)
    // ============================================
    window.switchModalTab = function (tab) {
        // Block switching to sales if detail fields are incomplete
        if (tab === 'sales') {
            const salesTab = document.getElementById('modal-tab-sales');
            if (salesTab && salesTab.disabled) {
                return;
            }
        }

        const tabs = ['detail', 'sales'];
        tabs.forEach(t => {
            const tabBtn = document.getElementById('modal-tab-' + t);
            const content = document.getElementById('modal-content-' + t);
            if (t === tab) {
                tabBtn.classList.add('border-rust', 'text-rust');
                tabBtn.classList.remove('border-transparent', 'text-gray-400');
                content.classList.remove('hidden');
            } else {
                tabBtn.classList.remove('border-rust', 'text-rust');
                tabBtn.classList.add('border-transparent', 'text-gray-400');
                content.classList.add('hidden');
            }
        });
    };

    // ============================================
    // Open / Close Ticket Modal
    // ============================================
    window.openTicketModal = function (type, editTicketData) {
        currentTicketType = type;
        // Only treat as edit if editTicketData is a plain object with an id (not a browser Event)
        const isEditing = editTicketData && !(editTicketData instanceof Event) && editTicketData.id;
        editingTicketId = isEditing ? editTicketData.id : null;
        modal.classList.remove('hidden');

        // Show/hide price field
        if (type === 'free') {
            priceField.classList.add('hidden');
        } else {
            priceField.classList.remove('hidden');
        }

        // Update create button text
        if (btnCreateTicket) {
            if (editingTicketId) {
                btnCreateTicket.textContent = 'Simpan Perubahan';
            } else {
                btnCreateTicket.textContent = type === 'free' ? 'Buat Tiket Gratis' : 'Buat Tiket Berbayar';
            }
        }

        // Reset sales tab to disabled
        const salesTabBtn = document.getElementById('modal-tab-sales');
        if (salesTabBtn) {
            salesTabBtn.disabled = true;
            salesTabBtn.classList.add('opacity-40', 'cursor-not-allowed');
            salesTabBtn.title = 'Isi detail tiket terlebih dahulu';
        }

        // Reset to detail tab
        switchModalTab('detail');

        // Reset inputs first
        modal.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') {
                input.value = '0';
            } else if (input.type === 'date' || input.type === 'time') {
                input.value = '';
            } else {
                input.value = '';
            }
        });
        modal.querySelectorAll('textarea').forEach(ta => ta.value = '');

        // If editing, pre-fill fields with existing ticket data
        if (isEditing && editTicketData) {
            const detailInputs = modal.querySelectorAll('#modal-content-detail input');
            if (detailInputs[0]) detailInputs[0].value = editTicketData.name || '';
            if (detailInputs[1]) detailInputs[1].value = editTicketData.qty || 0;
            if (type === 'paid' && detailInputs[2]) {
                detailInputs[2].value = 'Rp ' + (editTicketData.price || 0).toLocaleString('id-ID');
            }
            // Pre-fill sale dates if available
            const dateInputs = modal.querySelectorAll('#modal-content-sales input[type="date"]');
            const timeInputs = modal.querySelectorAll('#modal-content-sales input[type="time"]');
            if (editTicketData.saleStartDate && dateInputs[0]) dateInputs[0].value = editTicketData.saleStartDate;
            if (editTicketData.saleStartTime && timeInputs[0]) timeInputs[0].value = editTicketData.saleStartTime;
            if (editTicketData.saleEndDate && dateInputs[1]) dateInputs[1].value = editTicketData.saleEndDate;
            if (editTicketData.saleEndTime && timeInputs[1]) timeInputs[1].value = editTicketData.saleEndTime;
        }

        // Remove any leftover date error
        const dateErrorEl = document.getElementById('date-validation-error');
        if (dateErrorEl) dateErrorEl.remove();

        // Validate on input
        validateModalFields();
    };

    window.closeTicketModal = function () {
        modal.classList.add('hidden');
    };

    // Close modal on backdrop click
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeTicketModal();
            }
        });

        // Live validation — enable "Selanjutnya" button and create button
        modal.addEventListener('input', validateModalFields);
    }

    function validateModalFields() {
        if (!modal) return;
        const inputs = modal.querySelectorAll('#modal-content-detail input');
        const name = inputs[0] ? inputs[0].value.trim() : '';
        const qty = inputs[1] ? parseInt(inputs[1].value) : 0;

        // For paid tickets, price must also be filled
        let priceValid = true;
        if (currentTicketType === 'paid') {
            const priceInput = inputs[2] ? inputs[2].value.trim() : '';
            // Price must not be empty and must contain a numeric value > 0
            const priceNum = parseInt(priceInput.replace(/[^0-9]/g, ''));
            priceValid = priceInput !== '' && priceInput !== 'Rp' && priceNum > 0;
        }

        const detailComplete = name && qty > 0 && priceValid;

        // "Selanjutnya" button
        const nextBtn = modal.querySelector('#modal-content-detail button[onclick*="switchModalTab"]');
        if (nextBtn) {
            if (detailComplete) {
                nextBtn.disabled = false;
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                nextBtn.disabled = true;
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // "Tanggal Penjualan" tab button
        const salesTabBtn = document.getElementById('modal-tab-sales');
        if (salesTabBtn) {
            if (detailComplete) {
                salesTabBtn.disabled = false;
                salesTabBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                salesTabBtn.title = '';
            } else {
                salesTabBtn.disabled = true;
                salesTabBtn.classList.add('opacity-40', 'cursor-not-allowed');
                salesTabBtn.title = 'Isi detail tiket terlebih dahulu';
                // If user is currently on sales tab with incomplete detail, switch back
                const salesContent = document.getElementById('modal-content-sales');
                if (salesContent && !salesContent.classList.contains('hidden')) {
                    switchModalTab('detail');
                }
            }
        }

        // "Buat Tiket" button
        if (btnCreateTicket) {
            const dateInputs = modal.querySelectorAll('#modal-content-sales input[type="date"]');
            const timeInputs = modal.querySelectorAll('#modal-content-sales input[type="time"]');
            const startDate = dateInputs[0] ? dateInputs[0].value : '';
            const endDate = dateInputs[1] ? dateInputs[1].value : '';
            const startTime = timeInputs[0] ? timeInputs[0].value : '';
            const endTime = timeInputs[1] ? timeInputs[1].value : '';

            // Validate that start datetime is in the future and end is after start
            let dateValid = true;
            let dateErrorMsg = '';
            const dateErrorEl = document.getElementById('date-validation-error');
            const now = new Date();
            if (startDate && startTime) {
                const startDT = new Date(startDate + 'T' + startTime);
                if (startDT <= now) {
                    dateValid = false;
                    dateErrorMsg = 'Waktu mulai harus lebih dari waktu sekarang';
                }
            }
            if (dateValid && startDate && endDate && startTime && endTime) {
                const startDateTime = startDate + 'T' + startTime;
                const endDateTime = endDate + 'T' + endTime;
                if (endDateTime <= startDateTime) {
                    dateValid = false;
                    dateErrorMsg = 'Waktu berakhir harus lebih lambat dari waktu mulai';
                }
            }
            if (dateValid && endDate && endTime) {
                const endDT = new Date(endDate + 'T' + endTime);
                if (endDT <= now) {
                    dateValid = false;
                    dateErrorMsg = 'Waktu berakhir harus lebih dari waktu sekarang';
                }
            }

            if (!dateValid) {
                if (!dateErrorEl) {
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'date-validation-error';
                    errorDiv.className = 'flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl mt-4 text-red-600 text-xs font-bold';
                    errorDiv.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> ' + dateErrorMsg;
                    const salesContent = document.getElementById('modal-content-sales');
                    const spaceDiv = salesContent ? salesContent.querySelector('.space-y-8') : null;
                    if (spaceDiv) spaceDiv.appendChild(errorDiv);
                } else {
                    // Update existing error message
                    dateErrorEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> ' + dateErrorMsg;
                }
            } else {
                if (dateErrorEl) dateErrorEl.remove();
            }

            if (name && qty > 0 && startDate && endDate && startTime && endTime && dateValid) {
                btnCreateTicket.disabled = false;
                btnCreateTicket.classList.remove('bg-rust/20', 'cursor-not-allowed');
                btnCreateTicket.classList.add('bg-rust', 'hover:bg-rust-deep', 'shadow-lg', 'shadow-rust/20', 'cursor-pointer');
            } else {
                btnCreateTicket.disabled = true;
                btnCreateTicket.classList.add('bg-rust/20', 'cursor-not-allowed');
                btnCreateTicket.classList.remove('bg-rust', 'hover:bg-rust-deep', 'shadow-lg', 'shadow-rust/20', 'cursor-pointer');
            }
        }
    }

    // ============================================
    // Create / Update Ticket
    // ============================================
    if (btnCreateTicket) {
        btnCreateTicket.addEventListener('click', function () {
            const detailInputs = modal.querySelectorAll('#modal-content-detail input');
            const name = detailInputs[0] ? detailInputs[0].value.trim() : '';
            const qty = detailInputs[1] ? parseInt(detailInputs[1].value) : 0;
            const priceRaw = currentTicketType === 'free' ? 0 : parseInt((detailInputs[2] ? detailInputs[2].value : '0').replace(/[^0-9]/g, ''));

            if (!name) {
                alert('Nama tiket wajib diisi!');
                return;
            }

            if (qty <= 0) {
                alert('Jumlah tiket harus lebih dari 0!');
                return;
            }

            // Capture sale date/time fields
            const dateInputs = modal.querySelectorAll('#modal-content-sales input[type="date"]');
            const timeInputs = modal.querySelectorAll('#modal-content-sales input[type="time"]');
            const saleStartDate = dateInputs[0] ? dateInputs[0].value : '';
            const saleStartTime = timeInputs[0] ? timeInputs[0].value : '';
            const saleEndDate = dateInputs[1] ? dateInputs[1].value : '';
            const saleEndTime = timeInputs[1] ? timeInputs[1].value : '';

            if (!saleStartDate || !saleEndDate || !saleStartTime || !saleEndTime) {
                alert('Tanggal dan jam penjualan tiket wajib diisi!');
                return;
            }

            // Final datetime validation
            const startDT = saleStartDate + 'T' + saleStartTime;
            const endDT = saleEndDate + 'T' + saleEndTime;
            const nowISO = new Date().toISOString().slice(0, 16);
            if (startDT <= nowISO) {
                alert('Waktu mulai harus lebih dari waktu sekarang!');
                return;
            }
            if (endDT <= startDT) {
                alert('Waktu berakhir harus lebih lambat dari waktu mulai!');
                return;
            }
            if (endDT <= nowISO) {
                alert('Waktu berakhir harus lebih dari waktu sekarang!');
                return;
            }

            const ticketData = {
                name: name,
                qty: qty,
                price: priceRaw,
                priceDisplay: currentTicketType === 'free' ? 'Gratis' : 'Rp ' + priceRaw.toLocaleString('id-ID'),
                type: currentTicketType,
                saleStartDate: saleStartDate,
                saleStartTime: saleStartTime,
                saleEndDate: saleEndDate,
                saleEndTime: saleEndTime,
            };

            if (editingTicketId) {
                // Update existing ticket
                const idx = tickets.findIndex(t => t.id === editingTicketId);
                if (idx !== -1) {
                    ticketData.id = editingTicketId;
                    tickets[idx] = ticketData;
                }
                editingTicketId = null;
            } else {
                // Add new ticket
                ticketData.id = Date.now();
                tickets.push(ticketData);
            }

            renderTickets();
            closeTicketModal();
        });
    }

    // ============================================
    // Render Tickets List
    // ============================================
    function formatDateDisplay(dateStr, timeStr) {
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
        if (tickets.length === 0) {
            addedTicketsContainer.classList.add('hidden');
            return;
        }

        addedTicketsContainer.classList.remove('hidden');
        addedTicketsList.innerHTML = tickets.map(t => {
            const saleStart = formatDateDisplay(t.saleStartDate, t.saleStartTime);
            const saleEnd = formatDateDisplay(t.saleEndDate, t.saleEndTime);
            const typeBadge = t.type === 'free'
                ? '<span class="inline-block px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold rounded-full uppercase tracking-wider">Gratis</span>'
                : '<span class="inline-block px-2 py-0.5 bg-rust/10 text-rust text-[10px] font-bold rounded-full uppercase tracking-wider">Berbayar</span>';

            return `
            <div class="group p-5 bg-cream/30 rounded-2xl border border-gold/10 hover:border-rust/20 hover:shadow-md transition-all duration-300">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-bold text-ink text-base truncate">${t.name}</span>
                            ${typeBadge}
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                                ${t.qty} tiket
                            </span>
                            <span class="flex items-center gap-1 font-semibold text-ink">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                ${t.priceDisplay}
                            </span>
                        </div>
                        <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 text-[11px] text-gray-400">
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
                    <div class="flex items-center gap-1 shrink-0">
                        <button onclick="editTicket(${t.id})" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-rust/10 hover:border-rust/30 flex items-center justify-center text-gray-400 hover:text-rust transition-all shadow-sm" title="Edit tiket">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </button>
                        <button onclick="removeTicket(${t.id})" class="w-9 h-9 rounded-xl bg-white border border-gray-100 hover:bg-red-50 hover:border-red-200 flex items-center justify-center text-gray-400 hover:text-red-500 transition-all shadow-sm" title="Hapus tiket">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    window.editTicket = function (id) {
        const ticket = tickets.find(t => t.id === id);
        if (!ticket) return;
        openTicketModal(ticket.type || 'paid', ticket);
    };

    window.removeTicket = function (id) {
        tickets = tickets.filter(t => t.id !== id);
        renderTickets();
    };

    // ============================================
    // Submit Event (API)
    // ============================================
    async function submitEvent(status) {
        const namaEvent = document.getElementById('input-nama-event')?.value.trim();
        const kategoriEvent = document.getElementById('input-kategori-event')?.value;
        const penyelenggara = document.getElementById('input-penyelenggara')?.value.trim();
        const datetime = document.getElementById('input-datetime')?.value;
        const lokasi = document.getElementById('input-lokasi')?.value.trim();
        const deskripsi = document.getElementById('input-deskripsi')?.value.trim();
        const bannerFile = document.getElementById('banner-input')?.files[0];

        // Validate required fields
        if (!namaEvent) { alert('Nama event wajib diisi!'); return; }
        if (!datetime) { alert('Tanggal & waktu wajib diisi!'); return; }
        if (!lokasi) { alert('Lokasi wajib diisi!'); return; }

        // Show loading
        const btnDraft = document.getElementById('btn-save-draft');
        const btnPublish = document.getElementById('btn-publish-event');
        const activeBtn = status === 'draft' ? btnDraft : btnPublish;
        const originalText = activeBtn.textContent;
        activeBtn.textContent = 'Menyimpan...';
        activeBtn.disabled = true;

        try {
            // Step 1: Check/create organizer
            let organizerId = null;

            const meRes = await apiGet('/me');
            if (!meRes || !meRes._ok) {
                alert('Gagal mengambil data user');
                return;
            }

            // Try to get organizer from dashboard stats
            const statsRes = await apiGet('/dashboard/stats');

            // Check if user already has events (meaning they have an organizer)
            const eventsRes = await apiGet('/my-events');
            if (eventsRes && eventsRes._ok && eventsRes.data && eventsRes.data.length > 0) {
                // User already has an organizer — get the organizer_id from existing event
                const existingEventRes = await apiGet('/events/' + eventsRes.data[0].id);
                if (existingEventRes && existingEventRes._ok) {
                    organizerId = existingEventRes.data.organizer_id;
                }
            }

            // If no organizer found, create one
            if (!organizerId) {
                const orgName = penyelenggara || meRes.data.name || 'Organizer';
                const orgRes = await apiPost('/organizers', {
                    organizer_name: orgName,
                    contact_email: meRes.data.email || '',
                });

                if (!orgRes || !orgRes._ok) {
                    alert('Gagal membuat organizer: ' + (orgRes?.message || 'Unknown error'));
                    return;
                }
                organizerId = orgRes.data.id;
            }

            // Step 2: Create event via FormData (for file upload)
            const formData = new FormData();
            formData.append('organizer_id', organizerId);
            formData.append('nama_event', namaEvent);
            formData.append('kategori_event', kategoriEvent || '');
            formData.append('event_datetime', datetime);
            formData.append('lokasi', lokasi);
            formData.append('deskripsi', deskripsi || '');
            formData.append('event_status', status);

            if (bannerFile) {
                formData.append('image', bannerFile);
            }

            // Pengaturan Tambahan
            const maxTicketEl = document.getElementById('max-ticket-value');
            const oneEmailEl = document.getElementById('toggle-one-email');
            const singleIdentityEl = document.getElementById('toggle-single-identity');

            formData.append('max_ticket_per_transaction', maxTicketEl ? parseInt(maxTicketEl.textContent) || 5 : 5);
            formData.append('one_email_one_transaction', oneEmailEl && oneEmailEl.checked ? '1' : '0');
            formData.append('single_identity_per_ticket', singleIdentityEl && singleIdentityEl.checked ? '1' : '0');

            const eventRes = await apiUpload('/events', formData);
            if (!eventRes || !eventRes._ok) {
                alert('Gagal membuat event: ' + (eventRes?.message || 'Unknown error'));
                return;
            }

            const eventId = eventRes.data.id;

            // Step 3: Create tickets
            if (tickets.length > 0) {
                const ticketPromises = tickets.map(t =>
                    apiPost('/tickets', {
                        event_id: eventId,
                        kategori: t.name,
                        harga: t.price,
                        kuota: t.qty
                    })
                );

                const ticketResults = await Promise.allSettled(ticketPromises);
                const failed = ticketResults.filter(r => r.status === 'rejected' || (r.value && !r.value._ok));

                if (failed.length > 0 && failed.length < tickets.length) {
                    alert(`Event berhasil dibuat! Namun ${failed.length} tiket gagal dibuat. Silakan kelola di halaman manage event.`);
                    window.location.href = '/manage-event/' + eventId;
                    return;
                } else if (failed.length === tickets.length) {
                    alert('Event berhasil dibuat, namun semua tiket gagal dibuat. Silakan kelola tiket di halaman manage event.');
                    window.location.href = '/manage-event/' + eventId;
                    return;
                }
            }

            // Check if event went to pending_approval
            const createdStatus = eventRes.data.event_status;
            if (createdStatus === 'pending_approval') {
                alert('Event berhasil dibuat! Event Anda akan ditinjau oleh admin sebelum dipublikasikan.');
            } else {
                alert('Event berhasil dibuat!');
            }
            window.location.href = '/my-events';

        } catch (err) {
            console.error('Submit event error:', err);
            alert('Terjadi kesalahan saat membuat event.');
        } finally {
            activeBtn.textContent = originalText;
            activeBtn.disabled = false;
        }
    }

    // ============================================
    // Button Event Listeners
    // ============================================
    const btnDraft = document.getElementById('btn-save-draft');
    const btnPublish = document.getElementById('btn-publish-event');

    if (btnDraft) {
        btnDraft.addEventListener('click', () => submitEvent('draft'));
    }
    if (btnPublish) {
        btnPublish.addEventListener('click', () => submitEvent('published'));
    }
})();
