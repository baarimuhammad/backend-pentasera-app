// my-events.js — Client-side logic for my-events page

function switchTab(tabId) {
    // Update tab buttons
    const tabs = document.querySelectorAll('.event-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
        if (tab.innerText.toLowerCase().includes(tabId)) {
            tab.classList.add('active');
        }
    });

    // Update content visibility
    const contents = document.querySelectorAll('.event-tab-content');
    contents.forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById('tab-' + tabId).classList.add('active');
}

function toggleRoleAndRedirect() {
    const currentRole = localStorage.getItem('pentasara_role') || 'creator';
    const newRole = currentRole === 'creator' ? 'user' : 'creator';
    localStorage.setItem('pentasara_role', newRole);
    window.location.href = '/';
}
