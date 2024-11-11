<?php
// header.php
session_start();

// Navigation items array - can be managed from backend
$navigation = [
    'index' => 'О НАС',
    'training' => 'ТРЕНИНГ НАВЫКОВ',
    'specialists' => 'СПЕЦИАЛИСТЫ',
    'about-dbt' => 'ЧТО ТАКОЕ ДБТ',
    'contact' => 'КОНТАКТЫ'
];
?>

<header class="header" role="banner">
    <nav class="nav-container" role="navigation" aria-label="Главное меню">
        <div class="logo">
            <a href="/" aria-label="DBT Unity - Вернуться на главную">DBT Unity</a>
        </div>
        
        <button class="mobile-menu-toggle" 
                aria-label="Открыть меню" 
                aria-expanded="false"
                aria-controls="main-menu">
            <span class="hamburger"></span>
            <span class="sr-only">Меню</span>
        </button>
        
        <ul class="nav-menu" id="main-menu" role="menubar">
            <?php foreach ($navigation as $key => $item): ?>
                <li role="none">
                    <a href="<?php echo $key; ?>.php" 
                       role="menuitem"
                       <?php echo $key === 'index' && !isset($_GET['page']) ? 'aria-current="page"' : ''; ?>>
                        <?php echo htmlspecialchars($item); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

<!-- Add styles for screen reader only class -->
<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const header = document.querySelector('.header');

    mobileMenuToggle.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        navMenu.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
        header.classList.toggle('menu-open');
        
        // Update button label for screen readers
        this.setAttribute('aria-label', isExpanded ? 'Открыть меню' : 'Закрыть меню');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!header.contains(event.target) && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
            header.classList.remove('menu-open');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            mobileMenuToggle.setAttribute('aria-label', 'Открыть меню');
        }
    });

    // Handle keyboard navigation
    navMenu.addEventListener('keydown', function(e) {
        const menuItems = navMenu.querySelectorAll('[role="menuitem"]');
        const currentItem = document.activeElement;
        const currentIndex = Array.from(menuItems).indexOf(currentItem);

        switch (e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                e.preventDefault();
                if (currentIndex < menuItems.length - 1) {
                    menuItems[currentIndex + 1].focus();
                } else {
                    menuItems[0].focus();
                }
                break;
            case 'ArrowLeft':
            case 'ArrowUp':
                e.preventDefault();
                if (currentIndex > 0) {
                    menuItems[currentIndex - 1].focus();
                } else {
                    menuItems[menuItems.length - 1].focus();
                }
                break;
            case 'Escape':
                if (navMenu.classList.contains('active')) {
                    mobileMenuToggle.click();
                    mobileMenuToggle.focus();
                }
                break;
        }
    });
});
</script>
