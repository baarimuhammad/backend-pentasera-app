/**
 * Payment Page Logic — Pentasara
 * Dynamic API-integrated version
 */

const BANK_DATA = {
    bni: {
        name: 'BNI',
        logo: 'https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1200px-BNI_logo.svg.png',
        mobile: [
            "Buka aplikasi BNI Mobile Banking dan login.",
            "Pilih menu Transfer, lalu pilih Virtual Account Billing.",
            "Pilih Input Baru dan masukkan nomor Virtual Account.",
            "Konfirmasi detail dan masukkan PIN transaksi."
        ],
        atm: [
            "Masukkan kartu ATM dan PIN BNI Anda.",
            "Pilih menu Lainnya > Transfer > Virtual Account Billing.",
            "Masukkan nomor Virtual Account.",
            "Konfirmasi pembayaran dan simpan struk."
        ],
        internet: [
            "Login ke BNI Internet Banking.",
            "Pilih menu Transfer > Virtual Account Billing.",
            "Masukkan nomor Virtual Account.",
            "Ikuti instruksi selanjutnya untuk verifikasi."
        ]
    },
    bca: {
        name: 'BCA',
        logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png',
        mobile: [
            "Buka m-BCA dan pilih m-Transfer.",
            "Pilih BCA Virtual Account.",
            "Masukkan nomor Virtual Account.",
            "Masukkan PIN m-BCA untuk konfirmasi."
        ],
        atm: [
            "Masukkan kartu ATM dan PIN BCA.",
            "Pilih Transaksi Lainnya > Transfer > Ke Rek BCA Virtual Account.",
            "Masukkan nomor Virtual Account.",
            "Pilih Ya untuk konfirmasi pembayaran."
        ],
        internet: [
            "Login ke KlikBCA.",
            "Pilih Transfer Dana > Transfer ke BCA Virtual Account.",
            "Masukkan nomor Virtual Account.",
            "Ikuti proses otorisasi transaksi."
        ]
    },
    mandiri: {
        name: 'Mandiri',
        logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/1200px-Bank_Mandiri_logo_2016.svg.png',
        mobile: [
            "Buka aplikasi Livin' by Mandiri.",
            "Pilih menu Bayar > Pembayaran Baru > Multi Payment.",
            "Pilih penyedia jasa dan masukkan nomor Virtual Account.",
            "Konfirmasi dan masukkan PIN Livin'."
        ],
        atm: [
            "Masukkan kartu ATM dan PIN Mandiri.",
            "Pilih Bayar/Beli > Lainnya > Multi Payment.",
            "Masukkan kode perusahaan dan nomor Virtual Account.",
            "Konfirmasi pembayaran."
        ],
        internet: [
            "Login ke Mandiri Online.",
            "Pilih menu Bayar > Multi Payment.",
            "Pilih rekening sumber dan masukkan nomor Virtual Account.",
            "Lakukan konfirmasi transaksi."
        ]
    },
    bri: {
        name: 'BRI',
        logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Logo_BRI.svg/1200px-Logo_BRI.svg.png',
        mobile: [
            "Buka aplikasi BRImobile (BRIMO) Anda dan login.",
            "Pilih menu BRIVA.",
            "Pilih Pembayaran Baru dan masukkan nomor Virtual Account.",
            "Konfirmasi detail tagihan dan masukkan PIN BRIMO."
        ],
        atm: [
            "Masukkan kartu ATM dan PIN BRI Anda.",
            "Pilih Transaksi Lain > Pembayaran > Lainnya > BRIVA.",
            "Masukkan nomor Virtual Account.",
            "Konfirmasi pembayaran dan simpan struk."
        ],
        internet: [
            "Login ke Internet Banking BRI.",
            "Pilih menu Pembayaran > BRIVA.",
            "Masukkan nomor Virtual Account.",
            "Otorisasi transaksi menggunakan token Anda."
        ]
    }
};

let currentBank = null;
let orderData = null;
let countdownInterval = null;

function initPayment() {
    // 1. Get order result from sessionStorage
    const stored = sessionStorage.getItem('order_result');
    if (!stored) {
        alert("Tidak ada pesanan aktif. Silakan pilih tiket terlebih dahulu.");
        window.location.href = "/";
        return;
    }

    orderData = JSON.parse(stored);
    const method = orderData.metode_pembayaran;
    const total = parseFloat(orderData.total_harga);
    const vaNumber = orderData.virtual_account || (orderData.payment_info ? orderData.payment_info.virtual_account : null);

    const vaLayout = document.getElementById('va-layout');
    const qrisLayout = document.getElementById('qris-layout');
    const vaProgress = document.getElementById('va-progress');
    const qrisBanner = document.getElementById('qris-banner');

    const rupiahFormat = `Rp ${total.toLocaleString('id-ID')}`;

    if (method === 'qris' || method === 'shopeepay' || method === 'gopay' || method === 'ovo' || method === 'dana') {
        // Render QRIS Layout
        if (vaLayout) vaLayout.classList.add('hidden');
        if (vaProgress) vaProgress.classList.add('hidden');
        if (qrisLayout) qrisLayout.classList.remove('hidden');
        if (qrisBanner) qrisBanner.classList.remove('hidden');
        
        document.getElementById('qris-total').innerText = rupiahFormat;
        const smallTotal = document.getElementById('qris-total-small');
        if (smallTotal) smallTotal.innerText = rupiahFormat;
        
        setupCountdown(orderData.expired_at, true);
    } else {
        // Render VA Layout
        if (vaLayout) vaLayout.classList.remove('hidden');
        if (vaProgress) vaProgress.classList.remove('hidden');
        if (qrisLayout) qrisLayout.classList.add('hidden');
        if (qrisBanner) qrisBanner.classList.add('hidden');

        currentBank = BANK_DATA[method] || BANK_DATA.bni;
        document.getElementById('va-logo').src = currentBank.logo;
        document.getElementById('va-number').innerText = vaNumber || '8277 0812 3456 7890';
        document.getElementById('va-name-label').innerText = `${currentBank.name} VIRTUAL ACCOUNT`;
        document.getElementById('va-total').innerText = rupiahFormat;

        switchTab('mobile');
        setupCountdown(orderData.expired_at, false);
    }

    // Attach click events to the I Have Paid buttons
    const payBtn = document.getElementById('confirm-payment-btn');
    if (payBtn) {
        payBtn.addEventListener('click', doConfirmPayment);
    }
    const qrisPayBtn = document.getElementById('qris-confirm-btn');
    if (qrisPayBtn) {
        qrisPayBtn.addEventListener('click', doConfirmPayment);
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

window.switchTab = (type) => {
    if (!currentBank) return;

    const tabs = ['mobile', 'atm', 'internet'];
    tabs.forEach(t => {
        const tab = document.getElementById(`tab-${t}`);
        if (tab) {
            if (t === type) {
                tab.classList.add('text-[#B84C2B]', 'border-[#B84C2B]');
                tab.classList.remove('text-gray-400', 'border-transparent');
            } else {
                tab.classList.remove('text-[#B84C2B]', 'border-[#B84C2B]');
                tab.classList.add('text-gray-400', 'border-transparent');
            }
        }
    });

    const instructions = currentBank[type];
    document.getElementById('instructions').innerHTML = instructions.map((s, i) => `
        <div class="flex gap-5">
            <span class="w-6 h-6 rounded-full bg-[#FFF5F2] text-[#B84C2B] flex items-center justify-center text-[11px] font-bold shrink-0">${i + 1}</span>
            <p class="text-[13px] text-gray-500 leading-relaxed">${s}</p>
        </div>
    `).join('');
};

function setupCountdown(expiredAtStr, isQris = false) {
    if (countdownInterval) clearInterval(countdownInterval);

    const expiredTime = new Date(expiredAtStr).getTime();

    const hBox = document.getElementById(isQris ? 'q-h' : 'h-box');
    const mBox = document.getElementById(isQris ? 'q-m' : 'm-box');
    const sBox = document.getElementById(isQris ? 'q-s' : 's-box');
    const qrisBannerTimer = document.getElementById('qris-timer-banner');

    countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const diff = expiredTime - now;

        if (diff <= 0) {
            clearInterval(countdownInterval);
            if (hBox) hBox.innerText = "00";
            if (mBox) mBox.innerText = "00";
            if (sBox) sBox.innerText = "00";
            if (qrisBannerTimer) qrisBannerTimer.innerText = "00:00:00";
            
            // Disable payment confirmation
            const payBtn = document.getElementById('confirm-payment-btn');
            if (payBtn) payBtn.disabled = true;
            const qrisPayBtn = document.getElementById('qris-confirm-btn');
            if (qrisPayBtn) qrisPayBtn.disabled = true;
            
            alert("Batas waktu pembayaran telah habis. Pesanan dibatalkan.");
            return;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        if (hBox) hBox.innerText = hours < 10 ? "0" + hours : hours;
        if (mBox) mBox.innerText = minutes < 10 ? "0" + minutes : minutes;
        if (sBox) sBox.innerText = seconds < 10 ? "0" + seconds : seconds;

        if (isQris && qrisBannerTimer) {
            qrisBannerTimer.innerText = `${hours < 10 ? "0" + hours : hours}:${minutes < 10 ? "0" + minutes : minutes}:${seconds < 10 ? "0" + seconds : seconds}`;
        }
    }, 1000);
}

window.copyVA = () => {
    const num = document.getElementById('va-number').innerText.replace(/\s/g, '');
    navigator.clipboard.writeText(num).then(() => {
        alert('Nomor VA berhasil disalin!');
    }).catch(() => {
        const textArea = document.createElement('textarea');
        textArea.value = num;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Nomor VA berhasil disalin!');
    });
};

async function doConfirmPayment() {
    if (!orderData || !orderData.order_id) return;

    const payBtn = document.getElementById('confirm-payment-btn');
    const qrisPayBtn = document.getElementById('qris-confirm-btn');
    
    [payBtn, qrisPayBtn].forEach(btn => {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="animate-spin w-5 h-5 border-2 border-current border-t-transparent rounded-full inline-block"></i> Mengkonfirmasi...`;
        }
    });

    try {
        const response = await apiPost(`/orders/${orderData.order_id}/confirm-payment`);
        if (response && response.status === 'success') {
            // Show Success Modal
            document.getElementById('success-modal').classList.remove('hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Clear sessionStorage
            sessionStorage.removeItem('order_result');

            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = '/my-tickets';
            }, 3000);
        } else {
            alert("Gagal konfirmasi pembayaran: " + (response ? response.message : 'Terjadi kesalahan sistem'));
            
            // Restore button
            if (payBtn) {
                payBtn.disabled = false;
                payBtn.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5"></i> I Have Paid`;
            }
            if (qrisPayBtn) {
                qrisPayBtn.disabled = false;
                qrisPayBtn.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5"></i> Saya Sudah Bayar`;
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    } catch (err) {
        console.error(err);
        alert("Gagal menghubungi server untuk konfirmasi pembayaran.");
        
        if (payBtn) {
            payBtn.disabled = false;
            payBtn.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5"></i> I Have Paid`;
        }
        if (qrisPayBtn) {
            qrisPayBtn.disabled = false;
            qrisPayBtn.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5"></i> Saya Sudah Bayar`;
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', initPayment);
