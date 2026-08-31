console.log('Agents dashboard JS loaded');

// Toggle Modal Function
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.toggle('hidden');
    }
}

// Toggle Action Menu Function
function toggleActionMenu(menuId) {
    const menu = document.getElementById(menuId);
    // Close all other menus first
    document.querySelectorAll('[id^="menu-"]').forEach(m => {
        if (m.id !== menuId) {
            m.classList.add('hidden');
        }
    });
    // Toggle target menu
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Close menus when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('[onclick^="toggleActionMenu"]')) {
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    }
    if (e.target.id === 'addAgentModal') {
        toggleModal('addAgentModal');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    console.log('Agents dashboard ready');
});

