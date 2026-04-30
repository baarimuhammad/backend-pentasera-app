// my-events.js — Client-side logic for my-events page

/**
 * Switch between Aktif / Draft / Lalu tabs.
 * We intentionally use a unique name to avoid collisions with the
 * global `switchTab()` defined in app.js (used by create-event).
 */
function switchMyEventTab(tabId) {
    // Update tab buttons
    const tabs = document.querySelectorAll('.event-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
        const text = tab.innerText.toLowerCase();
        if ((tabId === 'aktif' && text.includes('aktif')) ||
            (tabId === 'draft' && text.includes('draft')) ||
            (tabId === 'lalu'  && text.includes('lalu'))) {
            tab.classList.add('active');
        }
    });

    // Update content visibility
    const contents = document.querySelectorAll('.event-tab-content');
    contents.forEach(content => {
        content.classList.remove('active');
    });
    const target = document.getElementById('tab-' + tabId);
    if (target) target.classList.add('active');
}

// Expose globally so onclick attributes work
window.switchMyEventTab = switchMyEventTab;
