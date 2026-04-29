import './bootstrap';
/**
 * Pentasara - Shared Scripts (Laravel Version)
 */

window.scrollCarousel = function(btn, dir) {
    const wrapper = btn.closest('.carousel-wrapper');
    const grid = wrapper.querySelector('.events-grid');
    if (grid) {
        const scrollAmount = 260;
        grid.scrollBy({ left: dir * scrollAmount, behavior: 'smooth' });
    }
}

// Logic Auth & Mode Penyelenggara
window.checkAuthState = function() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const isCreator = localStorage.getItem('isCreator') === 'true';
    document.body.classList.toggle('is-logged-in', isLoggedIn);
    document.body.classList.toggle('is-creator', isCreator);
}

document.addEventListener('DOMContentLoaded', () => {
    window.checkAuthState();
    if (window.lucide) window.lucide.createIcons();
});
