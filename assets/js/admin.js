/**
 * Admin Panel JavaScript
 * @package PersonalBiography
 */

document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle Handler
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    // Sidebar Toggle Mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');

    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', () => {
            adminSidebar.classList.toggle('show');
        });
    }

    // Confirm Delete Actions
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const message = btn.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Auto-generate slug from title
    const titleInput = document.querySelector('[data-slug-target]');
    const slugInput = document.querySelector('#slug');

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', (e) => {
            if (!slugInput.dataset.userEdited) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });

        slugInput.addEventListener('input', () => {
            slugInput.dataset.userEdited = 'true';
        });
    }
});
