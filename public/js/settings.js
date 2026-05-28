// settings.js — Client-side logic for settings page

function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = 'auto';
}

function handleCloseAccount() {
    const input = document.getElementById('confirm-delete-input').value;
    if (input !== 'KONFIRMASI') {
        alert('Silakan ketik KONFIRMASI untuk melanjutkan.');
        return;
    }

    if (confirm('Apakah Anda benar-benar yakin ingin menghapus akun Anda? Semua data akan hilang selamanya.')) {
        alert('Akun Anda sedang diproses untuk penutupan. Anda akan dialihkan ke halaman utama.');
        window.location.href = '/';
    }
}

function handlePasswordChange(e) {
    e.preventDefault();
    const newPass = document.getElementById('new-password').value;
    const confirmPass = document.getElementById('confirm-password').value;

    if (newPass !== confirmPass) {
        alert('Konfirmasi kata sandi tidak cocok!');
        return;
    }

    const btn = e.target.querySelector('button[type="submit"]');
    btn.innerHTML = 'Menyimpan...';
    btn.disabled = true;

    setTimeout(() => {
        alert('Kata sandi berhasil diperbarui!');
        closeModal('password-modal');
        btn.innerHTML = 'Simpan Perubahan';
        btn.disabled = false;
        e.target.reset();
    }, 1000);
}

