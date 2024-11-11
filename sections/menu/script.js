document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const siteNav = document.querySelector('.site-nav');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            
            // Close all submenus when closing the mobile menu
            if (!isExpanded) {
                document.querySelectorAll('.has-submenu > button').forEach(btn => {
                    btn.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }

    // Language selector toggle
    const langToggle = document.querySelector('.lang-toggle');
    const langSubmenu = document.querySelector('.lang-submenu');

    if (langToggle && langSubmenu) {
        langToggle.addEventListener('click', function(e) {
            // Only handle click for mobile view
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
                
                // Close other menus
                submenuToggles.forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
            }
        });

        // Handle language selection
        const langOptions = document.querySelectorAll('.lang-option');
        langOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                const currentLang = this.getAttribute('href').split('=')[1];
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('lang', currentLang);
                window.location.href = currentUrl.toString();
            });
        });
    }

    // Submenu toggles for mobile
    const submenuToggles = document.querySelectorAll('.has-submenu > button');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // Only handle click for mobile view
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                // Close other submenus
                submenuToggles.forEach(otherToggle => {
                    if (otherToggle !== this) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                    }
                });

                this.setAttribute('aria-expanded', !isExpanded);
            }
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const isClickInside = siteNav?.contains(e.target) || menuToggle?.contains(e.target);
            const isLangClick = langToggle?.contains(e.target) || langSubmenu?.contains(e.target);
            
            if (!isClickInside && !isLangClick && menuToggle?.getAttribute('aria-expanded') === 'true') {
                menuToggle.setAttribute('aria-expanded', 'false');
                submenuToggles.forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
                if (langToggle) {
                    langToggle.setAttribute('aria-expanded', 'false');
                }
            }
        }
    });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                // Reset mobile menu state when returning to desktop
                menuToggle?.setAttribute('aria-expanded', 'false');
                submenuToggles.forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });
                if (langToggle) {
                    langToggle.setAttribute('aria-expanded', 'false');
                }
            }
        }, 250);
    });

    // Sticky header behavior
    const header = document.querySelector('.site-header[data-sticky="true"]');
    if (header) {
        let lastScroll = 0;
        let ticking = false;

        window.addEventListener('scroll', function() {
            lastScroll = window.scrollY;

            if (!ticking) {
                window.requestAnimationFrame(function() {
                    if (lastScroll > 100) {
                        header.classList.add('is-sticky');
                    } else {
                        header.classList.remove('is-sticky');
                    }
                    ticking = false;
                });

                ticking = true;
            }
        });
    }

    // Search toggle functionality
    const searchToggle = document.querySelector('.search-toggle');
    const searchForm = document.querySelector('.search-form');

    if (searchToggle && searchForm) {
        searchToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            searchForm.classList.toggle('is-active');

            if (!isExpanded) {
                searchForm.querySelector('input').focus();
            }
        });

        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchToggle.contains(e.target) && !searchForm.contains(e.target)) {
                searchToggle.setAttribute('aria-expanded', 'false');
                searchForm.classList.remove('is-active');
            }
        });
    }
});
