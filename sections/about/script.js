document.addEventListener('DOMContentLoaded', function() {
    const aboutSection = document.querySelector('.about-section');
    const teamMembers = aboutSection.querySelectorAll('.team-member');
    const certificationCard = aboutSection.querySelector('.certification-card');

    // Add animation when elements come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                
                // If it's the team members container, animate each member with delay
                if (entry.target.classList.contains('team-list')) {
                    teamMembers.forEach((member, index) => {
                        setTimeout(() => {
                            member.classList.add('visible');
                        }, index * 100);
                    });
                }
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px'
    });

    // Observe team list and certification card
    observer.observe(aboutSection.querySelector('.team-list'));
    observer.observe(certificationCard);

    // Add keyboard navigation for team members
    teamMembers.forEach(member => {
        member.setAttribute('tabindex', '0');
        member.setAttribute('role', 'button');
        
        member.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Add hover effect announcement for screen readers
    teamMembers.forEach(member => {
        member.addEventListener('mouseenter', function() {
            this.setAttribute('aria-expanded', 'true');
        });
        
        member.addEventListener('mouseleave', function() {
            this.setAttribute('aria-expanded', 'false');
        });
    });

    // Add smooth zoom effect for certification image
    const certImage = certificationCard.querySelector('img');
    if (certImage) {
        certImage.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        certImage.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
});
