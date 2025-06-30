// Admin-specific JavaScript can be added here. 

document.addEventListener('DOMContentLoaded', function() {
    // Logout functionality
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        });
        logoutBtn.addEventListener('touchstart', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        });
    }

    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarClose = document.getElementById('sidebar-close');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('active');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'true');
    }
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'false');
    }
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', openSidebar);
        sidebarToggle.addEventListener('touchstart', openSidebar);
    }
    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
        sidebarClose.addEventListener('touchstart', closeSidebar);
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('touchstart', closeSidebar);
    }
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // Form validation and enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Add mobile-friendly form enhancements
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // Prevent zoom on iOS for inputs
            if (input.type !== 'file') {
                input.style.fontSize = '16px';
            }
            // Add focus styles
            input.addEventListener('focus', function() {
                if (this.parentElement) this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                if (this.parentElement) this.parentElement.classList.remove('focused');
            });
        });
        // Form submission handling
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
            }
        });
    });

    // Mobile-friendly table enhancements
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        // Add data-label attributes for mobile responsive tables
        const headers = table.querySelectorAll('th');
        const rows = table.querySelectorAll('tbody tr');
        
        headers.forEach((header, index) => {
            const label = header.textContent.trim();
            rows.forEach(row => {
                const cell = row.querySelectorAll('td')[index];
                if (cell) {
                    cell.setAttribute('data-label', label);
                }
            });
        });

        // Make tables scrollable on mobile
        if (window.innerWidth <= 768) {
            const tableContainer = document.createElement('div');
            tableContainer.className = 'table-container';
            tableContainer.style.overflowX = 'auto';
            tableContainer.style.webkitOverflowScrolling = 'touch';
            table.parentNode.insertBefore(tableContainer, table);
            tableContainer.appendChild(table);
        }
    });

    // Add loading states for buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('loading') && this.type !== 'submit') {
                this.classList.add('loading');
                this.disabled = true;
                
                // Remove loading state after a delay (adjust as needed)
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.disabled = false;
                }, 2000);
            }
        });
        button.addEventListener('touchstart', function() {
            if (!this.classList.contains('loading') && this.type !== 'submit') {
                this.classList.add('loading');
                this.disabled = true;
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.disabled = false;
                }, 2000);
            }
        });
    });

    // Mobile-friendly delete confirmations
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
        button.addEventListener('touchstart', function(e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Responsive navigation handling
    function handleResize() {
        const nav = document.querySelector('nav ul');
        if (window.innerWidth <= 768) {
            // Add mobile navigation enhancements
            if (nav) {
                nav.style.flexDirection = 'column';
                nav.style.width = '100%';
            }
        } else {
            // Restore desktop navigation
            if (nav) {
                nav.style.flexDirection = '';
                nav.style.width = '';
            }
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call

    // Touch-friendly improvements
    if ('ontouchstart' in window) {
        // Add touch-specific enhancements
        document.body.classList.add('touch-device');
        
        // Increase touch targets
        const touchTargets = document.querySelectorAll('a, button, input[type="submit"]');
        touchTargets.forEach(target => {
            if (target.offsetHeight < 44 || target.offsetWidth < 44) {
                target.style.minHeight = '44px';
                target.style.minWidth = '44px';
                target.style.padding = '12px';
            }
        });
    }

    // Performance optimization: Debounce scroll events
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(function() {
            // Handle scroll-based functionality here
        }, 100);
    });

    // Auto-save form data
    function autoSaveForm() {
        const formData = new FormData(document.getElementById('match-form'));
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        localStorage.setItem('matchFormData', JSON.stringify(data));
    }

    // Keyboard shortcuts for admin panel
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save forms
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const activeForm = document.querySelector('form:focus-within');
            if (activeForm) {
                const submitBtn = activeForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        }

        // Escape key to close modals or cancel operations
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
            });
        }
    });

    // Enhanced accessibility
    const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]');
    focusableElements.forEach(element => {
        element.addEventListener('focus', function() {
            this.style.outline = '2px solid #007bff';
            this.style.outlineOffset = '2px';
        });
        
        element.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
    });

    // Mobile-specific optimizations
    if (window.innerWidth <= 768) {
        // Reduce animations on mobile for better performance
        document.body.style.setProperty('--transition-duration', '0.1s');
        
        // Optimize images for mobile
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            if (img.naturalWidth > 300) {
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
            }
        });
    }

    // Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    // Service Worker registered successfully
                })
                .catch(function(registrationError) {
                    // Service Worker registration failed
                });
        });
    }
}); 