/**
 * profile.js — Client-side logic for profile page
 * Requires: api-helper.js loaded first
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', init);

    async function init() {
        if (!requireAuth()) return;

        await loadProfile();
        bindAvatarUpload();
    }

    // ── Load user profile from API ────────────────────────
    async function loadProfile() {
        const res = await apiGet('/me');

        if (!res || !res._ok) {
            alert('Gagal memuat profil. Silakan login ulang.');
            return;
        }

        // API returns { data: { user: {...} } }
        const user = res.data?.user || res.data || res;

        // Fill user fields
        const inputNama = document.getElementById('inputNama');
        const inputEmail = document.getElementById('inputEmail');
        const inputNoHp = document.getElementById('inputNoHp');

        if (inputNama) inputNama.value = user.nama || '';
        if (inputEmail) inputEmail.value = user.email || '';
        if (inputNoHp) inputNoHp.value = user.no_hp || '';

        // Show avatar if exists
        if (user.avatar_url) {
            const avatarImage = document.getElementById('avatarImage');
            const avatarIcon = document.getElementById('avatarIcon');
            if (avatarImage) {
                // Use avatar_full_url if available, otherwise construct the path
                avatarImage.src = user.avatar_full_url || ('/storage/' + user.avatar_url);
                avatarImage.style.display = 'block';
            }
            if (avatarIcon) {
                avatarIcon.style.display = 'none';
            }
        }

        // Update localStorage with fresh user data
        localStorage.setItem('user', JSON.stringify(user));

        // If creator, load organizer data
        if (user.role === 'creator') {
            await loadOrganizer(user);
        }
    }

    // ── Load organizer data for creators ──────────────────
    async function loadOrganizer(currentUser) {
        const res = await apiGet('/organizers');

        if (!res || !res._ok) return;

        const organizers = res.data || [];
        const user = currentUser || getUser();

        // Find organizer belonging to this user
        const myOrg = organizers.find(o => o.user_id === user?.id);

        if (myOrg) {
            const inputOrganizerName = document.getElementById('inputOrganizerName');
            const inputAddress = document.getElementById('inputAddress');
            const inputDeskripsi = document.getElementById('inputDeskripsi');
            const inputContactPhone = document.getElementById('inputContactPhone');

            if (inputOrganizerName) inputOrganizerName.value = myOrg.organizer_name || '';
            if (inputAddress) inputAddress.value = myOrg.address || '';
            if (inputDeskripsi) inputDeskripsi.value = myOrg.deskripsi || '';
            if (inputContactPhone) inputContactPhone.value = myOrg.contact_phone || '';
        }
    }

    // ── Save profile ──────────────────────────────────────
    window.saveProfile = async function () {
        const btn = document.getElementById('btnSaveProfile');
        const originalText = btn?.innerHTML;
        if (btn) {
            btn.innerHTML = 'Menyimpan...';
            btn.disabled = true;
        }

        const payload = {
            nama: document.getElementById('inputNama')?.value || '',
            no_hp: document.getElementById('inputNoHp')?.value || '',
        };

        // Add creator fields if visible
        const user = getUser();
        if (user?.role === 'creator') {
            const orgName = document.getElementById('inputOrganizerName')?.value;
            const address = document.getElementById('inputAddress')?.value;
            const deskripsi = document.getElementById('inputDeskripsi')?.value;
            const contactPhone = document.getElementById('inputContactPhone')?.value;

            if (orgName) payload.organizer_name = orgName;
            if (address) payload.address = address;
            if (deskripsi) payload.deskripsi = deskripsi;
            if (contactPhone) payload.contact_phone = contactPhone;
        }

        const res = await apiPatch('/profile', payload);

        if (res && res._ok) {
            // Update localStorage with fresh user data
            if (res.data) {
                localStorage.setItem('user', JSON.stringify(res.data));
            }
            alert('Profil berhasil diperbarui!');
        } else {
            const msg = res?.message || 'Gagal menyimpan profil.';
            alert(msg);
        }

        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    };

    // ── Avatar upload ─────────────────────────────────────
    function bindAvatarUpload() {
        const avatarInput = document.getElementById('avatarInput');
        if (!avatarInput) return;

        avatarInput.addEventListener('change', async function () {
            const file = this.files[0];
            if (!file) return;

            // Validate size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                return;
            }

            // Preview immediately
            const reader = new FileReader();
            reader.onload = function (e) {
                const avatarImage = document.getElementById('avatarImage');
                const avatarIcon = document.getElementById('avatarIcon');
                if (avatarImage) {
                    avatarImage.src = e.target.result;
                    avatarImage.style.display = 'block';
                }
                if (avatarIcon) {
                    avatarIcon.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);

            // Upload to server
            const formData = new FormData();
            formData.append('avatar', file);

            const res = await apiUpload('/profile/avatar', formData);

            if (res && res._ok) {
                // Update localStorage
                const user = getUser();
                if (user && res.data?.avatar_url) {
                    user.avatar_url = res.data.avatar_url;
                    if (res.data.avatar_full_url) {
                        user.avatar_full_url = res.data.avatar_full_url;
                    }
                    localStorage.setItem('user', JSON.stringify(user));
                }
                alert('Avatar berhasil diupload!');
            } else {
                alert(res?.message || 'Gagal mengupload avatar.');
            }
        });
    }


})();
