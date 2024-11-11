document.addEventListener('DOMContentLoaded', function() {
    const programSection = document.querySelector('.program-section');
    const cards = programSection.querySelectorAll('.component-card');
    
    // Add animation when cards come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add staggered animation to cards
                entry.target.style.animationDelay = `${entry.target.dataset.index * 0.2}s`;
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
            
            // Scale up icon on hover
            const icon = this.querySelector('.icon-wrapper');
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.setAttribute('aria-expanded', 'false');
            
            // Reset icon scale
            const icon = this.querySelector('.icon-wrapper');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // Handle CTA button interactions
    const ctaButton = programSection.querySelector('.cta-button');
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

        // Add ripple effect on button click
        ctaButton.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            setTimeout(() => ripple.remove(), 600);
        });
    }

    // Add smooth parallax effect on scroll
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const cards = document.querySelectorAll('.component-card.visible');
                cards.forEach(card => {
                    const rect = card.getBoundingClientRect();
                    const scrollPercent = rect.top / window.innerHeight;
                    if (scrollPercent > 0 && scrollPercent < 1) {
                        card.style.transform = `translateY(${scrollPercent * -20}px)`;
                    }
                });
                ticking = false;
            });
            ticking = true;
        }
    });
});
