// profile.js — Client-side logic for profile page

function saveProfile() {
    localStorage.setItem('profile_complete', 'true');
    alert('Profil berhasil diperbarui!');
    window.location.href = '/dashboard';
}

function toggleRoleAndRedirect() {
    // Toggle role in localStorage and redirect
    const currentRole = localStorage.getItem('pentasara_role') || 'creator';
    const newRole = currentRole === 'creator' ? 'user' : 'creator';
    localStorage.setItem('pentasara_role', newRole);
    window.location.href = '/';
}
