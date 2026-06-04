const CHECKOUT_DATA = window.PENTASARA_CHECKOUT || {};
const EVENTS = CHECKOUT_DATA.event ? [CHECKOUT_DATA.event] : [];
const TICKETS = CHECKOUT_DATA.tickets || [];

function resolveImageUrl(image, baseUrl) {
    if (!image) return '';
    if (image.startsWith('http://') || image.startsWith('https://')) return image;
    return `${baseUrl}/${image.replace(/^\/+/, '')}`;
}

function initCheckout() {
    const params = new URLSearchParams(window.location.search);
    const eventId = params.get('id');
    const ticketData = params.get('tickets');
    
    if (typeof PENTASARA_CHECKOUT === 'undefined') {
        console.error('PENTASARA_CHECKOUT is undefined!');
        return;
    }

    const event = PENTASARA_CHECKOUT.event;
    const ticketsList = PENTASARA_CHECKOUT.tickets;

    if (!event) {
        console.error('No event data!');
        return;
    }

    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';

    // Render Event details — support both old (nama_event/image_url) and new (name/image) formats
    const eventImage = event.image || event.image_url;
    const eventName = event.name || event.nama_event;
    const eventDate = event.date || (event.event_datetime ? new Date(event.event_datetime).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-');
    const eventVenue = event.venue || event.lokasi;

    document.getElementById('event-thumb').src = eventImage ? resolveImageUrl(eventImage, baseUrl) : baseUrl + '/assets/kecak.png';
    document.getElementById('event-name').innerText = eventName;
    document.getElementById('event-date').innerText = eventDate;
    document.getElementById('event-venue').innerText = eventVenue;
    document.getElementById('modal-event-thumb').src = document.getElementById('event-thumb').src;
    document.getElementById('modal-event-name').innerText = eventName;
    document.getElementById('modal-event-date').innerText = eventDate;

    if (ticketData) {
        const ticketEntries = ticketData.split(',').map(s => s.split(':'));
        let subtotal = 0, ticketListHtml = '', modalTicketListHtml = '', totalQty = 0, ticketTypes = [];

        ticketEntries.forEach(([tid, q]) => {
            const t = ticketsList.find(tk => tk.id == tid);
            if (t) {
                const qty = parseInt(q);
                totalQty += qty;
                const price = parseFloat(t.harga || t.rawPrice);
                const ticketName = t.kategori || t.type;
                subtotal += (qty * price);
                ticketListHtml += `<div class="flex justify-between text-[13px]"><span class="text-gray-400">Harga Tiket ${ticketName} (${qty}x)</span><span class="font-bold text-[#2C1A0E]">Rp ${(qty * price).toLocaleString('id-ID')}</span></div>`;
                modalTicketListHtml += `<div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl"><div><p class="font-bold text-[#2C1A0E]">${ticketName}</p><p class="text-[10px] text-gray-400">${qty} Tiket x Rp ${price.toLocaleString('id-ID')}</p></div><span class="font-bold text-rust">Rp ${(qty * price).toLocaleString('id-ID')}</span></div>`;
                for (let i = 0; i < qty; i++) {
                    ticketTypes.push(`${eventName.toUpperCase()} – ${ticketName.toUpperCase()}`);
                }
            }
        });

        const serviceFee = subtotal > 0 ? Math.round(subtotal * 0.1) : 0;
        const total = subtotal + serviceFee;

        document.getElementById('ticket-list').innerHTML = ticketListHtml;
        document.getElementById('service-fee').innerText = `Rp ${serviceFee.toLocaleString('id-ID')}`;
        document.getElementById('total-payment').innerText = `Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('bottom-total').innerText = `Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('modal-ticket-list').innerHTML = modalTicketListHtml;
        document.getElementById('modal-subtotal').innerText = `Rp ${subtotal.toLocaleString('id-ID')}`;
        document.getElementById('modal-service-fee').innerText = `Rp ${serviceFee.toLocaleString('id-ID')}`;
        document.getElementById('modal-total').innerText = `Rp ${total.toLocaleString('id-ID')}`;

        generateVisitorForms(totalQty, ticketTypes);
    }

    // Pre-fill user data if logged in
    const user = getUser();
    if (user) {
        if (document.getElementById('buyer-name')) document.getElementById('buyer-name').value = user.nama || user.name || '';
        if (document.getElementById('buyer-email')) document.getElementById('buyer-email').value = user.email || '';
        if (document.getElementById('buyer-phone')) {
            let phone = user.no_hp || '';
            if (phone.startsWith('+62')) phone = phone.replace('+62', '');
            else if (phone.startsWith('62')) phone = phone.substring(2);
            else if (phone.startsWith('0')) phone = phone.substring(1);
            document.getElementById('buyer-phone').value = phone;
        }
        if (document.getElementById('buyer-id')) document.getElementById('buyer-id').value = user.no_ktp || '';
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
    startTimer(5 * 60 + 31);
}

function generateVisitorForms(count, types) {
    const container = document.getElementById('visitor-container');
    let html = '';
    for (let i = 1; i <= count; i++) {
        html += `
        <section class="bg-white rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-gray-100 p-10 animate-fade-in-up">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <i data-lucide="users" class="w-5 h-5 text-rust"></i>
                    <h2 class="font-bold text-lg text-[#2C1A0E]">Data Pengunjung ${i}</h2>
                </div>
                ${i === 1 ? `
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="custom-checkbox-ui w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center group-hover:border-rust transition-all bg-white shadow-sm" id="checkbox-ui">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-white hidden" id="checkbox-icon"></i>
                    </div>
                    <input type="checkbox" class="hidden" id="copy-buyer">
                    <span class="text-[12px] text-gray-600 font-bold group-hover:text-rust transition-colors">Sama seperti pemesan</span>
                </label>` : ''}
            </div>
            <div class="bg-[#FFF5F2] border border-[#FFD8CC] p-5 rounded-xl mb-8">
                <span class="text-[9px] font-bold text-[#B84C2B] uppercase tracking-widest block mb-1">JENIS TIKET</span>
                <p class="text-[13px] font-bold text-[#2C1A0E] uppercase tracking-wide">${types[i-1]}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Nama Lengkap *</label>
                    <input type="text" id="visitor-name-${i}" placeholder="Masukkan nama pengunjung" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm placeholder:text-gray-300">
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Identitas *</label>
                    <div class="relative">
                        <select class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm appearance-none bg-white">
                            <option>KTP</option><option>Paspor</option><option>SIM</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-[11px] font-bold text-[#2C1A0E]">Nomor Identitas *</label>
                    <input type="text" id="visitor-id-${i}" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)" placeholder="Masukkan 16 digit nomor identitas" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-rust outline-none transition-all text-sm placeholder:text-gray-300">
                </div>
            </div>
        </section>`;
    }
    container.innerHTML = html;

    const copyCheckbox = document.getElementById('copy-buyer');
    if (copyCheckbox) {
        copyCheckbox.addEventListener('change', (e) => {
            const icon = document.getElementById('checkbox-icon');
            const ui = document.getElementById('checkbox-ui');
            if (e.target.checked) {
                icon.classList.remove('hidden');
                ui.classList.add('checked');
                document.getElementById('visitor-name-1').value = document.getElementById('buyer-name').value;
                document.getElementById('visitor-id-1').value = document.getElementById('buyer-id').value;
            } else {
                icon.classList.add('hidden');
                ui.classList.remove('checked');
            }
        });
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

window.openDetailModal = () => {
    document.getElementById('detail-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
};
window.closeDetailModal = () => {
    document.getElementById('detail-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
};
window.openVoucherModal = () => {
    document.getElementById('voucher-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') lucide.createIcons();
};
window.closeVoucherModal = () => {
    document.getElementById('voucher-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
};

window.applyVoucher = () => {
    const code = document.getElementById('voucher-input').value;
    if (!code) { alert('Masukkan kode voucher terlebih dahulu'); return; }
    if (code.toUpperCase() === 'DISKON10') {
        alert('Voucher DISKON10 berhasil diterapkan!');
        document.getElementById('voucher-text').innerText = 'Voucher DISKON10 Terpasang';
        document.getElementById('voucher-trigger').classList.add('bg-green-50', 'border-green-200');
        closeVoucherModal();
    } else {
        alert('Maaf, voucher tidak valid atau sudah kadaluarsa');
    }
};

function startTimer(duration) {
    let timer = duration;
    const display = document.getElementById('timer');
    const interval = setInterval(() => {
        let minutes = parseInt(timer / 60, 10);
        let seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = minutes + ":" + seconds;
        if (--timer < 0) {
            clearInterval(interval);
            alert("Waktu memesan habis. Silakan ulangi pemesanan Anda.");
            window.location.href = "/";
        }
    }, 1000);
}

window.togglePaymentGroup = (id) => {
    const group = document.getElementById(`${id}-group`);
    const icon = document.getElementById(`${id}-icon`);
    const isHidden = group.classList.contains('hidden');
    if (isHidden) {
        group.classList.remove('hidden');
        icon.style.transform = 'rotate(0deg)';
    } else {
        group.classList.add('hidden');
        icon.style.transform = 'rotate(180deg)';
    }
};

window.processPayment = async () => {
    if (!isLoggedIn()) {
        alert("Silakan login terlebih dahulu untuk melakukan pembelian tiket.");
        window.location.href = "/auth";
        return;
    }

    const missing = [];
    const buyerName = document.getElementById('buyer-name');
    const buyerEmail = document.getElementById('buyer-email');
    const buyerId = document.getElementById('buyer-id');
    const buyerPhone = document.getElementById('buyer-phone');
    const gender = document.querySelector('input[name="gender"]:checked');
    const selectedPayment = document.querySelector('input[name="payment"]:checked');

    [buyerName, buyerEmail, buyerId, buyerPhone].forEach(el => el.classList.remove('border-red-500'));

    if (!buyerName.value) { missing.push("Nama Pemesan"); buyerName.classList.add('border-red-500'); }
    if (!buyerEmail.value) { missing.push("Email Pemesan"); buyerEmail.classList.add('border-red-500'); }
    if (!buyerId.value) { missing.push("Nomor Identitas Pemesan"); buyerId.classList.add('border-red-500'); }
    if (!buyerPhone.value) { missing.push("Nomor WhatsApp Pemesan"); buyerPhone.classList.add('border-red-500'); }
    if (!gender) missing.push("Jenis Kelamin");

    const visitorContainers = document.querySelectorAll('#visitor-container section');
    for (let i = 1; i <= visitorContainers.length; i++) {
        const vName = document.getElementById(`visitor-name-${i}`);
        const vId = document.getElementById(`visitor-id-${i}`);
        vName.classList.remove('border-red-500');
        vId.classList.remove('border-red-500');
        if (!vName.value) { missing.push(`Nama Pengunjung ${i}`); vName.classList.add('border-red-500'); }
        if (!vId.value) { missing.push(`Nomor Identitas Pengunjung ${i}`); vId.classList.add('border-red-500'); }
    }

    if (!selectedPayment) missing.push("Metode Pembayaran");

    if (missing.length > 0) {
        alert("Mohon lengkapi data berikut:\n- " + missing.join("\n- "));
        const firstError = document.querySelector('.border-red-500');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    if (buyerId.value.length !== 16) {
        alert('Nomor identitas pemesan harus berjumlah 16 digit');
        buyerId.classList.add('border-red-500'); buyerId.focus(); return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(buyerEmail.value)) {
        alert('Mohon masukkan format email yang valid');
        buyerEmail.classList.add('border-red-500'); buyerEmail.focus(); return;
    }

    for (let i = 1; i <= visitorContainers.length; i++) {
        const vId = document.getElementById(`visitor-id-${i}`);
        if (vId.value.length !== 16) {
            alert(`Nomor identitas Pengunjung ${i} harus berjumlah 16 digit`);
            vId.classList.add('border-red-500'); vId.focus(); return;
        }
    }

    // Call API transactions
    const params = new URLSearchParams(window.location.search);
    const ticketData = params.get('tickets');
    const ticketEntries = ticketData.split(',').map(s => s.split(':'));

    const items = ticketEntries.map(([tid, q]) => ({
        ticket_id: parseInt(tid),
        jumlah: parseInt(q)
    }));

    const body = {
        items: items,
        buyer_info: {
            nama: buyerName.value,
            email: buyerEmail.value,
            no_hp: '0' + buyerPhone.value,
            no_ktp: buyerId.value
        },
        metode_pembayaran: selectedPayment.value
    };

    // Show loading indicator
    const payBtn = document.querySelector('button[onclick="processPayment()"]');
    const originalBtnText = payBtn.innerHTML;
    payBtn.disabled = true;
    payBtn.innerHTML = `<i class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2 inline-block"></i> Memproses...`;

    try {
        const response = await apiPost('/transactions', body);
        if (response && response.success) {
            sessionStorage.setItem('order_result', JSON.stringify(response.data));
            window.location.href = '/payment';
        } else {
            alert("Gagal memproses transaksi: " + (response ? response.message : 'Terjadi kesalahan sistem'));
            payBtn.disabled = false;
            payBtn.innerHTML = originalBtnText;
        }
    } catch (err) {
        console.error(err);
        alert("Gagal menghubungi server. Pastikan koneksi internet aktif.");
        payBtn.disabled = false;
        payBtn.innerHTML = originalBtnText;
    }
};

document.addEventListener('DOMContentLoaded', initCheckout);
