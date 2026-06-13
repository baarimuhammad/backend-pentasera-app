{{-- Admin Sidebar Partial — shared across all admin pages --}}
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera Logo">
        <span class="logo-text">PENTASERA</span>
    </div>

    <div class="admin-badge">
        <i data-lucide="shield-check" class="w-3 h-3"></i>
        Administrator
    </div>

    <nav>
        <div class="nav-group">
            <p class="nav-label">Menu Utama</p>
            <a href="{{ url('/admin/dashboard') }}" class="nav-item {{ ($activePage ?? '') === 'dashboard' ? 'active' : '' }}" id="nav-pending-link">
                <i data-lucide="clock" class="w-5 h-5"></i> Event Pending
                <span id="nav-pending-badge" class="pending-count" style="margin-left:auto; display:none;">0</span>
            </a>
        </div>
        <div class="nav-group">
            <p class="nav-label">Manajemen</p>
            <a href="{{ url('/admin/users') }}" class="nav-item {{ ($activePage ?? '') === 'users' ? 'active' : '' }}">
                <i data-lucide="users" class="w-5 h-5"></i> Kelola Pengguna
            </a>
            <a href="{{ url('/admin/analytics') }}" class="nav-item {{ ($activePage ?? '') === 'analytics' ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i> Laporan & Analitik
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-user-info">
            <div class="admin-avatar" id="admin-avatar-letter">A</div>
            <div>
                <div class="admin-user-name" id="admin-user-name">Admin</div>
                <div class="admin-user-role">Administrator</div>
            </div>
        </div>
        <button class="btn-admin-logout" onclick="logout()">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Keluar
        </button>
    </div>
</aside>
