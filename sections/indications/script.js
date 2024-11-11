document.addEventListener('DOMContentLoaded', function() {
    const indicationsSection = document.querySelector('.indications-section');
    const cards = indicationsSection.querySelectorAll('.indication-card');
    
    // Add animation when cards come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add staggered animation to cards
                entry.target.style.animationDelay = `${entry.target.dataset.index * 0.1}s`;
                entry.target.classList.add('visible', 'animate');
                
                // Add ARIA live region announcement for screen readers
                entry.target.setAttribute('aria-live', 'polite');
                
                // Stop observing after animation
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Set animation delay index and observe each card
    cards.forEach((card, index) => {
        card.dataset.index = index;
        observer.observe(card);
        
        // Add keyboard interaction
        card.setAttribute('tabindex', '0');
        
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });

        // Add hover effect announcement for screen readers
        card.addEventListener('mouseenter', function() {
            this.setAttribute('aria-expanded', 'true');
        });
        
        card.addEventListener('mouseleave', function() {
            this.setAttribute('aria-expanded', 'false');
        });
    });

    // Handle CTA button interactions
    const ctaButton = document.querySelector('.indications-cta .cta-button');
    if (ctaButton) {
        ctaButton.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = this.getAttribute('href').slice(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Set focus to the target element for accessibility
                    targetElement.setAttribute('tabindex', '-1');
                    targetElement.focus();
                }
            }
        });
    }

    // Add resize observer to maintain grid layout integrity
    const resizeObserver = new ResizeObserver(entries => {
        for (let entry of entries) {
            const grid = entry.target;
            const cards = grid.children;
            const gridWidth = grid.offsetWidth;
            const cardWidth = 300; // minimum card width from CSS
            
            // Calculate optimal number of columns
            const columns = Math.floor(gridWidth / cardWidth);
            grid.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
        }
    });

    const grid = indicationsSection.querySelector('.indications-grid');
    resizeObserver.observe(grid);
});
