document.addEventListener('DOMContentLoaded', function() {
    // Logout functionality
    const logoutBtn = document.getElementById('logout-btn');
    const sidebarLogoutBtn = document.getElementById('sidebar-logout-btn');

    function handleLogout(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
    
    if (sidebarLogoutBtn) {
        sidebarLogoutBtn.addEventListener('click', handleLogout);
    }

    // Mobile sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    let sidebarOpen = false;
    let touchStartX = null;
    let touchStartY = null;

    function openSidebar() {
        if (sidebar && sidebarOverlay && sidebarToggle) {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('open');
            sidebarToggle.classList.add('active');
            sidebarToggle.setAttribute('aria-expanded', 'true');
            sidebarOpen = true;
            
            // Prevent body scroll when sidebar is open
            document.body.style.overflow = 'hidden';
            
            // Focus management for accessibility
            setTimeout(() => {
                const firstLink = sidebar.querySelector('a');
                if (firstLink) firstLink.focus();
            }, 100);
        }
    }

    function closeSidebar() {
        if (sidebar && sidebarOverlay && sidebarToggle) {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('open');
            sidebarToggle.classList.remove('active');
            sidebarToggle.setAttribute('aria-expanded', 'false');
            sidebarOpen = false;
            
            // Restore body scroll
            document.body.style.overflow = '';
            
            // Return focus to hamburger button
            sidebarToggle.focus();
        }
    }

    // Event listeners for sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebarOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
        
        // Keyboard support
        sidebarToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (sidebarOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar when clicking on a link (mobile)
    if (sidebar) {
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Don't close for external links or admin panel
                if (!this.href.includes('../admin/') && !this.href.startsWith('http')) {
                    closeSidebar();
                }
            });
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (sidebarOpen && (e.key === 'Escape' || e.key === 'Esc')) {
            closeSidebar();
        }
    });

    // Touch gesture support for mobile
    function handleTouchStart(e) {
        if (e.touches.length === 1) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }
    }

    function handleTouchMove(e) {
        if (touchStartX === null || touchStartY === null) return;
        
        const touchX = e.touches[0].clientX;
        const touchY = e.touches[0].clientY;
        const deltaX = touchX - touchStartX;
        const deltaY = touchY - touchStartY;
        
        // Only handle horizontal swipes
        if (Math.abs(deltaX) > Math.abs(deltaY)) {
            e.preventDefault();
        }
    }

    function handleTouchEnd(e) {
        if (touchStartX === null || touchStartY === null) return;
        
        const touchX = e.changedTouches[0].clientX;
        const touchY = e.changedTouches[0].clientY;
        const deltaX = touchX - touchStartX;
        const deltaY = touchY - touchStartY;
        
        // Minimum swipe distance
        const minSwipeDistance = 50;
        
        // Only handle horizontal swipes
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
            // Swipe right to open sidebar (from left edge)
            if (deltaX > 0 && touchStartX < 50 && !sidebarOpen) {
                openSidebar();
            }
            // Swipe left to close sidebar
            else if (deltaX < 0 && sidebarOpen) {
                closeSidebar();
            }
        }
        
        touchStartX = null;
        touchStartY = null;
    }

    // Add touch event listeners
    document.addEventListener('touchstart', handleTouchStart, { passive: false });
    document.addEventListener('touchmove', handleTouchMove, { passive: false });
    document.addEventListener('touchend', handleTouchEnd);

    // Resize handler to close sidebar on larger screens
    function handleResize() {
        if (window.innerWidth > 900 && sidebarOpen) {
            closeSidebar();
        }
    }

    window.addEventListener('resize', handleResize);

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
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
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
    });

    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

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

    // Add loading states for buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('loading')) {
                this.classList.add('loading');
                this.disabled = true;
                
                // Remove loading state after a delay (adjust as needed)
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.disabled = false;
                }, 2000);
            }
        });
    });

    // Sidebar close button functionality
    const sidebarCloseBtn = document.getElementById('sidebar-close');
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', closeSidebar);
        sidebarCloseBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                closeSidebar();
            }
        });
    }
}); 