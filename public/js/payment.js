/**
 * Payment Page Logic — Pentasara
 * Extracted from original payment.html inline script
 * Pattern follows checkout.js convention
 */

const BANK_DATA = {
    bni: {
        name: 'BNI',
        logo: 'https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1200px-BNI_logo.svg.png',
        number: '8277 0812 3456 7890',
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
        number: '1234 5678 9012 3456',
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
        number: '9000 0123 4567 8901',
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
        name: 'Permata',
        logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Logo_BRI.svg/1200px-Logo_BRI.svg.png',
        number: '4567 8901 2345 6789',
        mobile: [
            "Buka aplikasi PermataMobile X.",
            "Pilih menu Bayar Tagihan > Virtual Account.",
            "Masukkan nomor Virtual Account.",
            "Konfirmasi dan masukkan PIN."
        ],
        atm: [
            "Masukkan kartu ATM dan PIN Permata.",
            "Pilih menu Pembayaran > Virtual Account.",
            "Masukkan nomor Virtual Account.",
            "Konfirmasi pembayaran."
        ],
        internet: [
            "Login ke PermataNet.",
            "Pilih menu Pembayaran > Virtual Account.",
            "Masukkan nomor Virtual Account.",
            "Lakukan otorisasi transaksi."
        ]
    }
};

let currentBank = null;

function initPayment() {
    const params = new URLSearchParams(window.location.search);
    const method = params.get('method');
    const total = params.get('total');

    const vaLayout = document.getElementById('va-layout');
    const qrisLayout = document.getElementById('qris-layout');
    const vaProgress = document.getElementById('va-progress');
    const qrisBanner = document.getElementById('qris-banner');

    if (method === 'qris' || method === 'shopeepay') {
        vaLayout.classList.add('hidden');
        vaProgress.classList.add('hidden');
        qrisLayout.classList.remove('hidden');
        qrisBanner.classList.remove('hidden');
        if (total) {
            document.getElementById('qris-total').innerText = total;
            document.getElementById('qris-total-small').innerText = total;
        }
        startTimer(15 * 60, true);
    } else {
        currentBank = BANK_DATA[method] || BANK_DATA.bni;
        document.getElementById('va-logo').src = currentBank.logo;
        document.getElementById('va-number').innerText = currentBank.number;
        document.getElementById('va-name-label').innerText = `${currentBank.name} VIRTUAL ACCOUNT`;
        if (total) {
            document.getElementById('va-total').innerText = total;
        }

        switchTab('mobile');
        startTimer(24 * 60 * 60);
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

window.switchTab = (type) => {
    if (!currentBank) return;

    const tabs = ['mobile', 'atm', 'internet'];
    tabs.forEach(t => {
        const tab = document.getElementById(`tab-${t}`);
        if (t === type) {
            tab.classList.add('text-[#B84C2B]', 'border-[#B84C2B]');
            tab.classList.remove('text-gray-400', 'border-transparent');
        } else {
            tab.classList.remove('text-[#B84C2B]', 'border-[#B84C2B]');
            tab.classList.add('text-gray-400', 'border-transparent');
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

function startTimer(duration, isQris = false) {
    let timer = duration, hours, minutes, seconds;
    const hBox = document.getElementById(isQris ? 'q-h' : 'h-box');
    const mBox = document.getElementById(isQris ? 'q-m' : 'm-box');
    const sBox = document.getElementById(isQris ? 'q-s' : 's-box');
    const qrisBannerTimer = document.getElementById('qris-timer-banner');

    setInterval(() => {
        hours = parseInt(timer / 3600, 10);
        minutes = parseInt((timer % 3600) / 60, 10);
        seconds = parseInt(timer % 60, 10);

        if (hBox) hBox.innerText = hours < 10 ? "0" + hours : hours;
        if (mBox) mBox.innerText = minutes < 10 ? "0" + minutes : minutes;
        if (sBox) sBox.innerText = seconds < 10 ? "0" + seconds : seconds;

        if (isQris && qrisBannerTimer) {
            qrisBannerTimer.innerText = `${hours < 10 ? "0" + hours : hours}:${minutes < 10 ? "0" + minutes : minutes}:${seconds < 10 ? "0" + seconds : seconds}`;
        }

        if (--timer < 0) timer = duration;
    }, 1000);
}

window.copyVA = () => {
    const num = document.getElementById('va-number').innerText.replace(/\s/g, '');
    navigator.clipboard.writeText(num).then(() => {
        alert('Nomor VA berhasil disalin!');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = num;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Nomor VA berhasil disalin!');
    });
};

window.confirmPayment = () => {
    document.getElementById('success-modal').classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
};

document.addEventListener('DOMContentLoaded', initPayment);
