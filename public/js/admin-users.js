/**
 * Admin Users Management — JavaScript
 * Handles CRUD operations for user management.
 * public/js/admin-users.js
 */
(function () {
    'use strict';

    // ─── Auth Guard ───
    if (typeof requireAuth === 'function') requireAuth();

    const user = typeof getUser === 'function' ? getUser() : null;
    if (!user || user.role !== 'admin') {
        window.location.href = '/';
        return;
    }

    // ─── Set admin user info in sidebar ───
    const avatarEl = document.getElementById('admin-avatar-letter');
    const nameEl = document.getElementById('admin-user-name');
    if (avatarEl && user.nama) avatarEl.textContent = user.nama.charAt(0).toUpperCase();
    if (nameEl && user.nama) nameEl.textContent = user.nama;

    // ─── State ───
    let currentPage = 1;
    let currentRole = '';
    let currentSearch = '';
    let searchDebounceTimer = null;

    // ─── Toast helper ───
    function showToast(message, type = 'success') {
        const container = document.getElementById('admin-toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `admin-toast ${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ─── Escape HTML helper ───
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ─── Load Users ───
    async function loadUsers(page = 1) {
        const loadingEl = document.getElementById('users-loading');
        const tableEl = document.getElementById('users-table');
        const bodyEl = document.getElementById('users-table-body');
        const emptyEl = document.getElementById('users-empty');
        const paginationEl = document.getElementById('users-pagination');

        if (loadingEl) loadingEl.style.display = 'flex';
        if (tableEl) tableEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'none';
        if (paginationEl) paginationEl.style.display = 'none';

        let url = `/admin/users?page=${page}&per_page=15`;
        if (currentRole) url += `&role=${currentRole}`;
        if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;

        try {
            const res = await apiGet(url);
            if (!res || !res._ok) {
                console.error('Failed to load users:', res?.message);
                if (loadingEl) loadingEl.style.display = 'none';
                return;
            }

            const data = res.data;
            const users = data.data || [];
            currentPage = data.current_page || 1;

            if (loadingEl) loadingEl.style.display = 'none';

            if (users.length === 0) {
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }

            // Show table
            if (tableEl) tableEl.style.display = 'table';

            // Render rows
            if (bodyEl) {
                bodyEl.innerHTML = users.map(u => {
                    const joinDate = u.created_at
                        ? new Date(u.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                        : '-';

                    const isSelf = u.id === user.id;
                    const statusChecked = u.status === 'aktif' ? 'checked' : '';
                    const disabledAttr = isSelf ? 'disabled' : '';

                    return `
                        <tr data-user-id="${u.id}">
                            <td>
                                <div class="user-info-cell">
                                    <div class="user-avatar ${u.role}">${escapeHtml((u.nama || 'U').charAt(0).toUpperCase())}</div>
                                    <div>
                                        <div class="user-name">${escapeHtml(u.nama)}${isSelf ? ' <span style="font-size:10px;color:var(--admin-accent);font-weight:800;">(Anda)</span>' : ''}</div>
                                        <div class="user-email">${escapeHtml(u.email)}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge ${u.role}">${escapeHtml(u.role)}</span>
                            </td>
                            <td>
                                <label class="toggle-switch" title="${u.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'}">
                                    <input type="checkbox" ${statusChecked} ${disabledAttr} onchange="toggleUserStatus(${u.id}, this.checked)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </td>
                            <td><span class="event-meta">${joinDate}</span></td>
                            <td><span class="event-meta">${u.orders_count ?? 0}</span></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="btn-admin-view" onclick="openDetailUserModal(${u.id})" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button class="btn-admin-secondary" onclick="openEditUserModal(${u.id}, '${escapeHtml(u.nama)}', '${u.role}')" ${isSelf ? 'disabled style="opacity:0.4;cursor:not-allowed;"' : ''} title="Ubah Role">
                                        <i data-lucide="pen-line" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button class="btn-admin-danger" onclick="openDeleteUserModal(${u.id}, '${escapeHtml(u.nama)}')" ${isSelf ? 'disabled style="opacity:0.4;cursor:not-allowed;"' : ''} title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                if (window.lucide) window.lucide.createIcons();
            }

            // Pagination
            if (paginationEl && data.last_page > 1) {
                paginationEl.style.display = 'flex';
                let paginationHtml = '';

                paginationHtml += `<button onclick="goToPage(${data.current_page - 1})" ${data.current_page <= 1 ? 'disabled' : ''}>&laquo; Prev</button>`;

                for (let i = 1; i <= data.last_page; i++) {
                    if (i === 1 || i === data.last_page || Math.abs(i - data.current_page) <= 2) {
                        paginationHtml += `<button class="${i === data.current_page ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                    } else if (Math.abs(i - data.current_page) === 3) {
                        paginationHtml += `<span class="page-info">...</span>`;
                    }
                }

                paginationHtml += `<button onclick="goToPage(${data.current_page + 1})" ${data.current_page >= data.last_page ? 'disabled' : ''}>Next &raquo;</button>`;

                paginationEl.innerHTML = paginationHtml;
            }

        } catch (err) {
            console.error('Error loading users:', err);
            if (loadingEl) loadingEl.style.display = 'none';
        }
    }

    // ─── Toggle User Status ───
    window.toggleUserStatus = async function (userId, isActive) {
        const newStatus = isActive ? 'aktif' : 'nonaktif';
        try {
            const res = await apiPatch(`/admin/users/${userId}`, { status: newStatus });
            if (res && res._ok) {
                showToast(`Pengguna berhasil ${isActive ? 'diaktifkan' : 'dinonaktifkan'}`, 'success');
            } else {
                showToast(res?.message || 'Gagal mengubah status', 'error');
                await loadUsers(currentPage); // Revert UI
            }
        } catch (err) {
            console.error('Toggle status error:', err);
            showToast('Gagal menghubungi server', 'error');
            await loadUsers(currentPage);
        }
    };

    // ─── Edit User Modal ───
    window.openEditUserModal = function (userId, userName, currentRole) {
        const modal = document.getElementById('edit-user-modal');
        const info = document.getElementById('edit-user-info');
        const idInput = document.getElementById('edit-user-id');
        const roleSelect = document.getElementById('edit-user-role');

        if (info) info.textContent = `Mengubah role untuk: ${userName}`;
        if (idInput) idInput.value = userId;
        if (roleSelect) roleSelect.value = currentRole;
        if (modal) modal.classList.add('active');
    };

    window.closeEditUserModal = function () {
        const modal = document.getElementById('edit-user-modal');
        if (modal) modal.classList.remove('active');
    };

    window.confirmEditUser = async function () {
        const userId = document.getElementById('edit-user-id')?.value;
        const newRole = document.getElementById('edit-user-role')?.value;
        const btn = document.getElementById('btn-confirm-edit');

        if (!userId || !newRole) return;

        if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan...'; }

        try {
            const res = await apiPatch(`/admin/users/${userId}`, { role: newRole });
            if (res && res._ok) {
                showToast('Role berhasil diubah', 'success');
                closeEditUserModal();
                await loadUsers(currentPage);
            } else {
                showToast(res?.message || 'Gagal mengubah role', 'error');
            }
        } catch (err) {
            console.error('Edit user error:', err);
            showToast('Gagal menghubungi server', 'error');
        } finally {
            if (btn) { btn.disabled = false; btn.textContent = 'Simpan'; }
        }
    };

    // ─── Delete User Modal ───
    window.openDeleteUserModal = function (userId, userName) {
        const modal = document.getElementById('delete-user-modal');
        const info = document.getElementById('delete-user-info');
        const idInput = document.getElementById('delete-user-id');

        if (info) info.textContent = `Apakah Anda yakin ingin menghapus "${userName}"? Tindakan ini tidak dapat dibatalkan.`;
        if (idInput) idInput.value = userId;
        if (modal) modal.classList.add('active');
    };

    window.closeDeleteUserModal = function () {
        const modal = document.getElementById('delete-user-modal');
        if (modal) modal.classList.remove('active');
    };

    window.confirmDeleteUser = async function () {
        const userId = document.getElementById('delete-user-id')?.value;
        const btn = document.getElementById('btn-confirm-delete');

        if (!userId) return;

        if (btn) { btn.disabled = true; btn.textContent = 'Menghapus...'; }

        try {
            const res = await apiDelete(`/admin/users/${userId}`);
            if (res && res._ok) {
                showToast('Pengguna berhasil dihapus', 'success');
                closeDeleteUserModal();
                await loadUsers(currentPage);
            } else {
                showToast(res?.message || 'Gagal menghapus pengguna', 'error');
            }
        } catch (err) {
            console.error('Delete user error:', err);
            showToast('Gagal menghubungi server', 'error');
        } finally {
            if (btn) { btn.disabled = false; btn.textContent = 'Hapus Pengguna'; }
        }
    };

    // ─── Detail User Modal ───
    window.openDetailUserModal = async function (userId) {
        const modal = document.getElementById('detail-user-modal');
        const loadingEl = document.getElementById('detail-loading');
        const contentEl = document.getElementById('detail-content');

        // Show modal with loading
        if (modal) modal.classList.add('active');
        if (loadingEl) loadingEl.style.display = 'flex';
        if (contentEl) contentEl.style.display = 'none';

        try {
            const res = await apiGet(`/admin/users/${userId}/transactions`);
            if (!res || !res._ok) {
                showToast(res?.message || 'Gagal memuat data pengguna', 'error');
                closeDetailUserModal();
                return;
            }

            const data = res.data;
            const u = data.user;
            const transactions = data.transactions || [];

            // ── Populate Profile ──
            const avatarEl = document.getElementById('detail-avatar');
            if (avatarEl) {
                avatarEl.textContent = (u.nama || 'U').charAt(0).toUpperCase();
                avatarEl.className = 'detail-avatar ' + (u.role || 'buyer');
            }

            const nameEl = document.getElementById('detail-name');
            if (nameEl) nameEl.textContent = u.nama || '-';

            const emailEl = document.getElementById('detail-email');
            if (emailEl) emailEl.textContent = u.email || '-';

            const roleBadge = document.getElementById('detail-role-badge');
            if (roleBadge) {
                roleBadge.textContent = u.role || '-';
                roleBadge.className = 'role-badge ' + (u.role || '');
            }

            const phoneEl = document.getElementById('detail-phone');
            if (phoneEl) phoneEl.textContent = u.no_hp || 'Tidak tersedia';

            const statusEl = document.getElementById('detail-status');
            if (statusEl) {
                const isActive = u.status === 'aktif';
                statusEl.innerHTML = `<span class="status-dot ${u.status}"></span>${isActive ? 'Aktif' : 'Nonaktif'}`;
            }

            const joinedEl = document.getElementById('detail-joined');
            if (joinedEl) {
                joinedEl.textContent = u.created_at
                    ? new Date(u.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                    : '-';
            }

            const spentEl = document.getElementById('detail-total-spent');
            if (spentEl) spentEl.textContent = data.total_spent_formatted || 'Rp 0';

            // ── Populate Transactions ──
            const countEl = document.getElementById('detail-tx-count');
            if (countEl) countEl.textContent = transactions.length;

            const tableEl = document.getElementById('detail-tx-table');
            const bodyEl = document.getElementById('detail-tx-body');
            const emptyEl = document.getElementById('detail-tx-empty');

            if (transactions.length === 0) {
                if (tableEl) tableEl.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'flex';
            } else {
                if (emptyEl) emptyEl.style.display = 'none';
                if (tableEl) tableEl.style.display = 'table';

                if (bodyEl) {
                    bodyEl.innerHTML = transactions.map(tx => {
                        const txDate = tx.tanggal_order
                            ? new Date(tx.tanggal_order).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                            : '-';

                        const statusClass = tx.status_order === 'paid' ? 'paid'
                            : tx.status_order === 'pending' ? 'pending'
                            : tx.status_order === 'expired' ? 'expired'
                            : 'other';

                        const statusLabel = tx.status_order === 'paid' ? 'Lunas'
                            : tx.status_order === 'pending' ? 'Pending'
                            : tx.status_order === 'expired' ? 'Expired'
                            : tx.status_order || '-';

                        return `
                            <tr>
                                <td><span class="tx-code">${escapeHtml(tx.order_code)}</span></td>
                                <td><span class="tx-event">${escapeHtml(tx.event_name)}</span></td>
                                <td><span class="event-meta">${txDate}</span></td>
                                <td><span class="event-meta">${tx.jumlah_tiket}</span></td>
                                <td><span class="tx-amount">${escapeHtml(tx.total_formatted)}</span></td>
                                <td><span class="tx-status ${statusClass}">${statusLabel}</span></td>
                            </tr>
                        `;
                    }).join('');
                }
            }

            // Show content
            if (loadingEl) loadingEl.style.display = 'none';
            if (contentEl) contentEl.style.display = 'block';

            // Re-init Lucide icons in modal
            if (window.lucide) window.lucide.createIcons();

        } catch (err) {
            console.error('Error loading user detail:', err);
            showToast('Gagal menghubungi server', 'error');
            closeDetailUserModal();
        }
    };

    window.closeDetailUserModal = function () {
        const modal = document.getElementById('detail-user-modal');
        if (modal) modal.classList.remove('active');
    };

    // ─── Pagination ───
    window.goToPage = function (page) {
        if (page < 1) return;
        loadUsers(page);
    };

    // ─── Search ───
    const searchInput = document.getElementById('user-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                currentSearch = this.value.trim();
                currentPage = 1;
                loadUsers(1);
            }, 400);
        });
    }

    // ─── Role Filter Tabs ───
    const filterTabs = document.querySelectorAll('#role-filter-tabs .admin-filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentRole = this.dataset.role || '';
            currentPage = 1;
            loadUsers(1);
        });
    });

    // ─── Close modals ───
    ['edit-user-modal', 'delete-user-modal', 'detail-user-modal'].forEach(id => {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                }
            });
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeEditUserModal();
            closeDeleteUserModal();
            closeDetailUserModal();
        }
    });

    // ─── Initialize ───
    document.addEventListener('DOMContentLoaded', function () {
        loadUsers(1);
        if (window.lucide) window.lucide.createIcons();
    });

})();
