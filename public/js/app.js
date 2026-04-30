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

function checkAuthState() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const isCreator = localStorage.getItem('isCreator') === 'true';

    document.body.classList.toggle('is-logged-in', isLoggedIn);

    const roleLabel = document.getElementById('dropdown-role-label');
    if (isCreator) {
        document.body.classList.add('is-creator');
        if (roleLabel) roleLabel.innerText = 'Pembeli';
    } else {
        document.body.classList.remove('is-creator');
        if (roleLabel) roleLabel.innerText = 'Penyelenggara';
    }

    // Update sidebar switch button text dynamically
    const switchBtns = document.querySelectorAll('.switch-mode-btn span');
    switchBtns.forEach(span => {
        span.innerText = isCreator ? 'Beralih ke Pembeli' : 'Beralih ke Penyelenggara';
    });
    
    if (window.lucide) window.lucide.createIcons();
}

function toggleRole() {
    localStorage.setItem('isCreator', JSON.parse(localStorage.getItem('isCreator') === 'false'));
    checkAuthState();
}

function toggleRoleAndRedirect() {
    const isCreator = localStorage.getItem('isCreator') === 'true';
    localStorage.setItem('isCreator', (!isCreator).toString());
    // Redirect buyer to my-tickets, creator to dashboard
    if (isCreator) {
        // Was creator, now switching to buyer
        window.location.href = '/my-tickets';
    } else {
        // Was buyer, now switching to creator
        window.location.href = '/dashboard';
    }
}

function logout() {
    localStorage.clear();
    window.location.href = '/';
}

window.addEventListener('DOMContentLoaded', () => {
    checkAuthState();
    
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

function openTicketModal(type, event) {
    const modal = document.getElementById('ticket-modal');
    const modalContent = modal ? modal.querySelector('.animate-fade-in-up') : null;
    const priceField = document.getElementById('price-field');
    const btnCreate = document.getElementById('btn-create-ticket');
    
    if (!modal || !modalContent) return;
    
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
        if (btnCreate) btnCreate.textContent = 'Buat Tiket Gratis';
    } else {
        if (priceField) priceField.classList.remove('hidden');
        if (btnCreate) btnCreate.textContent = 'Buat Tiket Berbayar';
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

    const nameInput = modal.querySelector('input[placeholder*="Nama"], input[placeholder*="Maksimal"]');
    const qtyInput = modal.querySelector('input[type="number"]');
    const priceField = document.getElementById('price-field');
    const priceInput = priceField ? priceField.querySelector('input') : null;
    const isFree = priceField && priceField.classList.contains('hidden');
    
    const name = nameInput ? nameInput.value.trim() : '';
    const quantity = qtyInput ? parseInt(qtyInput.value) : 0;
    const price = isFree ? 0 : (priceInput ? parseInt(priceInput.value.replace(/[^0-9]/g, '')) : 0);

    const ticket = {
        id: Date.now(),
        type: isFree ? 'free' : 'paid',
        name,
        quantity,
        price
    };

    ticketsData.push(ticket);
    renderTickets();
    closeTicketModal();
}

function renderTickets() {
    const list = document.getElementById('added-tickets-list');
    const container = document.getElementById('added-tickets-container');
    if (!list || !container) return;

    if (ticketsData.length > 0) {
        container.classList.remove('hidden');
        list.innerHTML = ticketsData.map(ticket => `
            <div class="flex items-center justify-between p-5 bg-white border border-gray-100 rounded-2xl group hover:border-rust transition-all shadow-sm">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 ${ticket.type === 'free' ? 'bg-green-50 text-green-500' : 'bg-rust/10 text-rust'} rounded-xl flex items-center justify-center">
                        <i data-lucide="${ticket.type === 'free' ? 'gift' : 'banknote'}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h5 class="font-bold text-ink">${ticket.name}</h5>
                        <p class="text-xs text-gray-400">${ticket.quantity} Tiket • ${ticket.type === 'free' ? 'Gratis' : 'Rp ' + ticket.price.toLocaleString('id-ID')}</p>
                    </div>
                </div>
                <button onclick="removeTicket(${ticket.id})" class="text-gray-300 hover:text-red-500 transition-colors p-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        `).join('');
        lucide.createIcons();
    } else {
        container.classList.add('hidden');
    }
}

function removeTicket(id) {
    ticketsData = ticketsData.filter(t => t.id !== id);
    renderTickets();
}

function validateTicketForm() {
    const modal = document.getElementById('ticket-modal');
    if (!modal) return;

    const nameInput = modal.querySelector('input[placeholder*="Nama"], input[placeholder*="Maksimal"]');
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
window.logout = logout;
window.checkAuthState = checkAuthState;
window.switchTab = switchTab;
window.openTicketModal = openTicketModal;
window.closeTicketModal = closeTicketModal;
window.switchModalTab = switchModalTab;
window.removeTicket = removeTicket;
window.createTicket = createTicket;

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
