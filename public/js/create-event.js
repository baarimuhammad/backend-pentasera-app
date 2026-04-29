/**
 * Create Event — Tab Switching, Modal & Ticket Management
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
    window.openTicketModal = function (type) {
        currentTicketType = type;
        modal.classList.remove('hidden');

        // Show/hide price field
        if (type === 'free') {
            priceField.classList.add('hidden');
        } else {
            priceField.classList.remove('hidden');
        }

        // Update create button text
        if (btnCreateTicket) {
            btnCreateTicket.textContent = type === 'free' ? 'Buat Tiket Gratis' : 'Buat Tiket Berbayar';
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

        // Reset inputs
        modal.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') {
                input.value = '0';
            } else if (input.type === 'date') {
                input.value = '';
            } else {
                input.value = '';
            }
        });
        modal.querySelectorAll('textarea').forEach(ta => ta.value = '');

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
            const startDate = dateInputs[0] ? dateInputs[0].value : '';
            const endDate = dateInputs[1] ? dateInputs[1].value : '';

            if (name && qty > 0 && startDate && endDate) {
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
    // Create Ticket
    // ============================================
    if (btnCreateTicket) {
        btnCreateTicket.addEventListener('click', function () {
            const detailInputs = modal.querySelectorAll('#modal-content-detail input');
            const name = detailInputs[0] ? detailInputs[0].value.trim() : '';
            const qty = detailInputs[1] ? parseInt(detailInputs[1].value) : 0;
            const price = currentTicketType === 'free' ? 0 : (detailInputs[2] ? detailInputs[2].value.trim() : '0');

            if (!name) {
                alert('Nama tiket wajib diisi!');
                return;
            }

            if (qty <= 0) {
                alert('Jumlah tiket harus lebih dari 0!');
                return;
            }

            const ticket = {
                id: Date.now(),
                name: name,
                qty: qty,
                price: currentTicketType === 'free' ? 'Gratis' : price,
                type: currentTicketType
            };

            tickets.push(ticket);
            renderTickets();
            closeTicketModal();
        });
    }

    // ============================================
    // Render Tickets List
    // ============================================
    function renderTickets() {
        if (tickets.length === 0) {
            addedTicketsContainer.classList.add('hidden');
            return;
        }

        addedTicketsContainer.classList.remove('hidden');
        addedTicketsList.innerHTML = tickets.map(t => `
            <div class="flex items-center justify-between p-4 bg-cream/30 rounded-xl border border-gold/10">
                <div>
                    <div class="font-bold text-ink text-sm">${t.name}</div>
                    <div class="text-xs text-gray-500">${t.qty} tiket • ${t.price}</div>
                </div>
                <button onclick="removeTicket(${t.id})" class="w-8 h-8 rounded-full hover:bg-red-50 flex items-center justify-center text-red-400 hover:text-red-600 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        `).join('');

        // Re-initialize lucide icons for new elements
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    window.removeTicket = function (id) {
        tickets = tickets.filter(t => t.id !== id);
        renderTickets();
    };
})();
