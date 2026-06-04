@extends('layouts.app')
@section('title', 'Kelola Akses | Pentasera')

@section('custom-nav')
{{-- Dashboard pages use their own sidebar, no main nav --}}
@endsection

@section('custom-footer')
{{-- Dashboard pages have no main footer --}}
@endsection

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera" class="w-10 h-10 object-contain">
            <span class="logo-text text-sm">PENTASERA</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Main Menu</p>
                <a href="{{ url('/dashboard') }}" class="nav-item creator-only">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item creator-only">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                </a>
                <a href="{{ url('/manage-access') }}" class="nav-item active creator-only">
                    <i data-lucide="users" class="w-5 h-5"></i> Kelola Akses
                </a>
                <a href="{{ url('/my-tickets') }}" class="nav-item user-only">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tiket Saya
                </a>
            </div>
            <div class="nav-group">
                <p class="nav-label">Account</p>
                <a href="{{ url('/profile') }}" class="nav-item">
                    <i data-lucide="user-circle" class="w-5 h-5"></i> Informasi Dasar
                </a>
                <a href="{{ url('/settings') }}" class="nav-item">
                    <i data-lucide="settings" class="w-5 h-5"></i> Pengaturan
                </a>
                <a href="{{ url('/pusat-bantuan') }}" class="nav-item">
                    <i data-lucide="help-circle" class="w-5 h-5"></i> Pusat Bantuan
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <button onclick="toggleRoleAndRedirect()" class="switch-mode-btn cursor-pointer">
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                <span>Beralih ke Pembeli</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Pentasera</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span>Kelola Akses</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="font-display text-3xl text-ink mb-2">Kelola Akses</h1>
            <p class="text-gray-500 text-sm">Atur siapa saja yang dapat mengelola event Anda</p>
        </div>

        <!-- Users Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3 class="table-title">Pengguna</h3>
                <div class="table-actions">
                    <div class="search-input-wrapper">
                        <i data-lucide="search"></i>
                        <input type="text" class="search-input" id="searchUserInput" placeholder="Cari pengguna..." oninput="filterUsers()">
                    </div>
                    <button id="btnUndangPengguna" onclick="openInviteModal()" class="bg-rust text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-2 cursor-pointer hover:opacity-90 transition-all opacity-50" disabled title="🚧 Fitur dalam pengembangan">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        Undang
                    </button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500">
                                    <i data-lucide="construction" class="w-8 h-8"></i>
                                </div>
                                <p class="font-bold text-ink text-sm">🚧 Fitur dalam pengembangan</p>
                                <p class="text-gray-400 text-xs">Manajemen staff/pengguna akan segera hadir di versi berikutnya</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Event List Section -->
        <div class="data-table-container">
            <div class="table-header">
                <h3 class="table-title">Daftar Event Terkait</h3>
                <div class="table-actions">
                    <div class="search-input-wrapper">
                        <i data-lucide="search"></i>
                        <input type="text" class="search-input" id="searchEventInput" placeholder="Cari event..." oninput="filterEvents()">
                    </div>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="eventTableBody">
                    @forelse($events as $event)
                    @php
                        $statusLabel = match($event->event_status) {
                            'published' => 'AKTIF',
                            'draft' => 'DRAFT',
                            'cancelled' => 'DIBATALKAN',
                            default => strtoupper($event->event_status),
                        };
                        $statusClass = match($event->event_status) {
                            'published' => 'active',
                            'draft' => 'bg-gray-100 text-gray-400',
                            'cancelled' => 'bg-red-100 text-red-500',
                            default => 'bg-gray-100 text-gray-400',
                        };
                        $isPast = $event->event_status === 'published' && $event->event_datetime < now();
                    @endphp
                    <tr class="event-row">
                        <td class="font-bold">{{ $event->nama_event }}</td>
                        <td>{{ $event->kategori_event ?? '-' }}</td>
                        <td><span class="status-badge {{ $isPast ? 'bg-ink text-white' : $statusClass }}">{{ $isPast ? 'SELESAI' : $statusLabel }}</span></td>
                        <td>
                            @if($isPast)
                                <a href="{{ url('/event-report/' . $event->id) }}" class="text-rust font-bold text-xs">Lihat Laporan</a>
                            @else
                                <a href="{{ url('/manage-event/' . $event->id) }}" class="text-rust font-bold text-xs">Kelola Akses</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">Belum ada event</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal Undang Pengguna -->
<div id="inviteModal" class="modal-overlay" onclick="if(event.target===this) closeInviteModal()">
    <div class="modal-container" style="max-width:520px">
        <div class="modal-header">
            <h3 style="font-family:var(--f-display);font-size:20px;color:var(--ink)">Undang Pengguna</h3>
            <button onclick="closeInviteModal()" class="cursor-pointer" style="background:none;border:none;color:#A0A0A0;transition:color 0.2s" onmouseover="this.style.color='var(--ink)'" onmouseout="this.style.color='#A0A0A0'">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:#7A7A7A;margin-bottom:24px">Tambahkan pengguna baru untuk mengelola event Anda. Masukkan email dan pilih role yang sesuai.</p>
            <form id="inviteForm" onsubmit="submitInvite(event)">
                <div class="form-group" style="margin-bottom:20px">
                    <label for="inviteEmail" style="display:block;font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px">Email Pengguna <span style="color:#DC2626">*</span></label>
                    <input type="email" id="inviteEmail" class="form-input" placeholder="contoh@email.com" required>
                </div>
                <div class="form-group" style="margin-bottom:20px">
                    <label for="inviteName" style="display:block;font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px">Nama Lengkap <span style="color:#DC2626">*</span></label>
                    <input type="text" id="inviteName" class="form-input" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group" style="margin-bottom:20px">
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px">Role <span style="color:#DC2626">*</span></label>
                    <div style="display:flex;gap:12px">
                        <label class="role-option" style="flex:1;cursor:pointer">
                            <input type="radio" name="inviteRole" value="admin" style="display:none" required>
                            <div class="role-option-card">
                                <i data-lucide="shield" class="w-5 h-5" style="margin-bottom:8px;color:var(--rust)"></i>
                                <span style="font-weight:700;font-size:13px;color:var(--ink)">Admin</span>
                                <p style="font-size:11px;color:#A0A0A0;margin-top:4px">Akses penuh mengelola event</p>
                            </div>
                        </label>
                        <label class="role-option" style="flex:1;cursor:pointer">
                            <input type="radio" name="inviteRole" value="editor" style="display:none">
                            <div class="role-option-card">
                                <i data-lucide="edit-3" class="w-5 h-5" style="margin-bottom:8px;color:var(--rust)"></i>
                                <span style="font-weight:700;font-size:13px;color:var(--ink)">Editor</span>
                                <p style="font-size:11px;color:#A0A0A0;margin-top:4px">Dapat mengedit detail event</p>
                            </div>
                        </label>
                        <label class="role-option" style="flex:1;cursor:pointer">
                            <input type="radio" name="inviteRole" value="viewer" style="display:none">
                            <div class="role-option-card">
                                <i data-lucide="eye" class="w-5 h-5" style="margin-bottom:8px;color:var(--rust)"></i>
                                <span style="font-weight:700;font-size:13px;color:var(--ink)">Viewer</span>
                                <p style="font-size:11px;color:#A0A0A0;margin-top:4px">Hanya melihat data event</p>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:20px">
                    <label for="inviteEvent" style="display:block;font-size:12px;font-weight:700;color:var(--ink);margin-bottom:8px">Assign ke Event</label>
                    <select id="inviteEvent" class="form-input" style="cursor:pointer" disabled title="🚧 Fitur dalam pengembangan">
                        <option value="">— Pilih Event —</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button onclick="closeInviteModal()" class="cursor-pointer" style="padding:12px 28px;border-radius:14px;font-size:13px;font-weight:700;background:#F5F5F5;color:var(--ink);border:none;transition:all 0.2s" onmouseover="this.style.background='#E5E5E5'" onmouseout="this.style.background='#F5F5F5'">Batal</button>
            <button onclick="document.getElementById('inviteForm').requestSubmit()" class="cursor-pointer opacity-50" disabled title="🚧 Fitur dalam pengembangan" style="padding:12px 28px;border-radius:14px;font-size:13px;font-weight:700;background:var(--rust);color:white;border:none;transition:all 0.2s;display:flex;align-items:center;gap:8px" onmouseover="this.style.opacity='0.5'" onmouseout="this.style.opacity='0.5'">
                <i data-lucide="send" class="w-4 h-4"></i>
                Kirim Undangan
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toastNotification" style="position:fixed;bottom:32px;right:32px;background:var(--ink);color:white;padding:16px 24px;border-radius:16px;font-size:13px;font-weight:600;display:none;align-items:center;gap:12px;box-shadow:0 12px 40px rgba(0,0,0,0.15);z-index:1100;animation:toastSlideIn 0.4s ease-out">
    <i data-lucide="check-circle" class="w-5 h-5" style="color:#4ADE80"></i>
    <span id="toastMessage">Undangan berhasil dikirim!</span>
</div>

@endsection

@push('styles')
<style>
/* Action Dropdown */
.action-dropdown {
    position: absolute;
    right: 32px;
    top: 100%;
    background: white;
    border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    border: 1px solid rgba(232, 194, 133, 0.15);
    padding: 8px;
    z-index: 50;
    min-width: 160px;
    animation: dropdownFadeIn 0.2s ease-out;
}

.action-dropdown button {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    background: none;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.15s ease;
}

.action-dropdown button:hover {
    background: #F9F5F0;
}

.action-dropdown button.text-red-500 {
    color: #DC2626;
}

.action-dropdown button.text-red-500:hover {
    background: #FEF2F2;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Role Option Cards */
.role-option-card {
    border: 2px solid #E5E5E5;
    border-radius: 16px;
    padding: 16px 12px;
    text-align: center;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.role-option-card:hover {
    border-color: rgba(184, 76, 43, 0.3);
    background: rgba(184, 76, 43, 0.02);
}

.role-option input:checked + .role-option-card {
    border-color: var(--rust);
    background: rgba(184, 76, 43, 0.06);
    box-shadow: 0 0 0 4px rgba(184, 76, 43, 0.08);
}

/* Toast animation */
@keyframes toastSlideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes toastSlideOut {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(20px); opacity: 0; }
}
</style>
@endpush

@push('scripts')
<script>

/* ── Invite Modal ── */
function openInviteModal() {
    const modal = document.getElementById('inviteModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    // Re-render icons inside modal
    if (window.lucide) lucide.createIcons();
}

function closeInviteModal() {
    const modal = document.getElementById('inviteModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    // Reset form
    document.getElementById('inviteForm').reset();
}

function submitInvite(e) {
    e.preventDefault();
    const email = document.getElementById('inviteEmail').value.trim();
    const name = document.getElementById('inviteName').value.trim();
    const role = document.querySelector('input[name="inviteRole"]:checked');
    const event = document.getElementById('inviteEvent').value;

    if (!email || !name) return;
    if (!role) {
        alert('Silakan pilih role untuk pengguna.');
        return;
    }

    // Generate initials
    const parts = name.split(' ');
    const initials = parts.length >= 2
        ? (parts[0][0] + parts[1][0]).toUpperCase()
        : name.substring(0, 2).toUpperCase();

    // Add new row to table
    const tbody = document.getElementById('userTableBody');
    const eventText = event
        ? document.getElementById('inviteEvent').options[document.getElementById('inviteEvent').selectedIndex].text
        : '—';
    const newRow = document.createElement('tr');
    newRow.classList.add('user-row');
    newRow.style.animation = 'dropdownFadeIn 0.3s ease-out';
    newRow.innerHTML = `
        <td>
            <div class="user-cell">
                <div class="user-avatar">${initials}</div>
                <span class="font-bold">${name}</span>
            </div>
        </td>
        <td>${email}</td>
        <td class="font-bold">${eventText}</td>
        <td><span class="status-badge active">AKTIF</span></td>
        <td style="position:relative">
            <button class="text-gray-400 hover:text-ink cursor-pointer" onclick="toggleActionMenu(this)"><i data-lucide="more-vertical" class="w-4 h-4"></i></button>
            <div class="action-dropdown" style="display:none">
                <button onclick="editUserRole(this)"><i data-lucide="shield" class="w-3.5 h-3.5"></i> Ubah Role</button>
                <button onclick="removeUser(this)" class="text-red-500"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus</button>
            </div>
        </td>
    `;
    tbody.appendChild(newRow);
    if (window.lucide) lucide.createIcons();

    closeInviteModal();
    showToast('Undangan berhasil dikirim ke ' + email);
}

/* ── Action Dropdown ── */
function toggleActionMenu(btn) {
    // Close all other dropdowns first
    document.querySelectorAll('.action-dropdown').forEach(d => {
        if (d !== btn.nextElementSibling) d.style.display = 'none';
    });
    const dropdown = btn.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-dropdown') && !e.target.closest('[onclick*="toggleActionMenu"]')) {
        document.querySelectorAll('.action-dropdown').forEach(d => d.style.display = 'none');
    }
});

function editUserRole(btn) {
    const row = btn.closest('tr');
    const name = row.querySelector('.user-cell .font-bold').textContent;
    btn.closest('.action-dropdown').style.display = 'none';
    showToast('Role ' + name + ' telah diperbarui');
}

function removeUser(btn) {
    const row = btn.closest('tr');
    const name = row.querySelector('.user-cell .font-bold').textContent;
    if (confirm('Apakah Anda yakin ingin menghapus akses ' + name + '?')) {
        row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => row.remove(), 300);
        showToast(name + ' telah dihapus dari daftar akses');
    } else {
        btn.closest('.action-dropdown').style.display = 'none';
    }
}

/* ── Search/Filter ── */
function filterUsers() {
    const query = document.getElementById('searchUserInput').value.toLowerCase();
    document.querySelectorAll('#userTableBody .user-row').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeInviteModal();
});
</script>
@endpush
