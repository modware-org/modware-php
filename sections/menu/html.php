<?php
require_once __DIR__ . '/query.php';

$menuQuery = new MenuQuery();
$data = $menuQuery->getMenuData();

function renderMenuItem($item) {
    $hasChildren = !empty($item['children']);
    $target = $item['target'] ?? '_self';
    $iconClass = $item['icon_class'] ? ' ' . $item['icon_class'] : '';
    
    $output = '<li class="menu-item' . ($hasChildren ? ' has-submenu' : '') . '">';
    
    if ($hasChildren) {
        $output .= '<button class="menu-link' . $iconClass . '" aria-expanded="false">';
        $output .= htmlspecialchars($item['title']);
        $output .= '<svg class="submenu-icon" viewBox="0 0 20 20" width="16" height="16">
                      <path d="M5 7l5 5 5-5" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round"/>
                   </svg>';
        $output .= '</button>';
        $output .= '<ul class="submenu">';
        foreach ($item['children'] as $child) {
            $output .= renderMenuItem($child);
        }
        $output .= '</ul>';
    } else {
        $output .= '<a href="/' . htmlspecialchars($item['url']) . '" 
                      class="menu-link' . $iconClass . '"
                      target="' . htmlspecialchars($target) . '">';
        $output .= htmlspecialchars($item['title']);
        $output .= '</a>';
    }
    
    $output .= '</li>';
    return $output;
}

function renderCategoryMenuItem($category) {
    $hasChildren = !empty($category['children']);
    
    $output = '<li class="menu-item' . ($hasChildren ? ' has-submenu' : '') . '">';
    
    if ($hasChildren) {
        $output .= '<button class="menu-link" aria-expanded="false">';
        $output .= htmlspecialchars($category['name']);
        $output .= '<svg class="submenu-icon" viewBox="0 0 20 20" width="16" height="16">
                      <path d="M5 7l5 5 5-5" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round"/>
                   </svg>';
        $output .= '</button>';
        $output .= '<ul class="submenu">';
        foreach ($category['children'] as $child) {
            $output .= renderCategoryMenuItem($child);
        }
        $output .= '</ul>';
    } else {
        $output .= '<a href="/category/' . htmlspecialchars($category['slug']) . '" class="menu-link">';
        $output .= htmlspecialchars($category['name']);
        $output .= '</a>';
    }
    
    $output .= '</li>';
    return $output;
}

// Get current language
$currentLang = $_GET['lang'] ?? 'en';
?>

<header class="site-header" data-sticky="<?php echo $data['config']['sticky'] ?? 'true'; ?>">
    <div class="header-container">
        <!-- Logo -->
        <a href="/" class="site-logo">
            <img src="<?php echo htmlspecialchars($data['config']['logo'] ?? '/img/unitydbt-logo.png'); ?>"
                 alt="<?php echo htmlspecialchars($data['config']['logo_alt'] ?? 'Unity DBT'); ?>"
                 width="150" height="50">
        </a>

        <!-- Mobile Menu Button -->
        <button class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
            <span class="hamburger"></span>
        </button>

        <!-- Navigation -->
        <nav class="site-nav">
            <ul class="main-menu">
                <?php
                // Render menu items
                foreach ($data['items'] as $item) {
                    echo renderMenuItem($item);
                }
                
                // Render categories that should appear in menu
                foreach ($data['categories'] as $category) {
                    echo renderCategoryMenuItem($category);
                }
                ?>
            </ul>

            <!-- Language Selector -->
            <div class="language-selector">
                <button class="lang-toggle" aria-expanded="false">
                    <span class="current-lang"><?php echo strtoupper($currentLang); ?></span>
                    <svg class="submenu-icon" viewBox="0 0 20 20" width="16" height="16">
                        <path d="M5 7l5 5 5-5" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                <ul class="lang-submenu">
                    <li>
                        <a href="?lang=en" class="lang-option <?php echo $currentLang === 'en' ? 'active' : ''; ?>">
                            English
                        </a>
                    </li>
                    <li>
                        <a href="?lang=pl" class="lang-option <?php echo $currentLang === 'pl' ? 'active' : ''; ?>">
                            Polski
                        </a>
                    </li>
                </ul>
            </div>

            <?php if ($data['config']['show_search'] ?? false): ?>
                <!-- Search -->
                <div class="search-container">
                    <button class="search-toggle" aria-label="Toggle search">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
                                  stroke="currentColor" 
                                  stroke-width="2" 
                                  stroke-linecap="round" 
                                  stroke-linejoin="round"
                                  fill="none"/>
                        </svg>
                    </button>
                    <form class="search-form" action="/search" method="get">
                        <input type="search" 
                               name="q" 
                               placeholder="<?php echo $currentLang === 'pl' ? 'Szukaj...' : 'Search...'; ?>" 
                               aria-label="Search"
                               required>
                        <button type="submit" aria-label="Submit search">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
                                      stroke="currentColor" 
                                      stroke-width="2" 
                                      stroke-linecap="round" 
                                      stroke-linejoin="round"
                                      fill="none"/>
                            </svg>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (!empty($data['config']['cta_text'])): ?>
                <!-- CTA Button -->
                <a href="<?php echo htmlspecialchars($data['config']['cta_url']); ?>" 
                   class="header-cta">
                    <?php echo htmlspecialchars($data['config']['cta_text']); ?>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <?php if ($data['config']['show_search'] ?? false): ?>
        <!-- Mobile Search -->
        <div class="mobile-search">
            <form class="search-form" action="/search" method="get">
                <input type="search" 
                       name="q" 
                       placeholder="<?php echo $currentLang === 'pl' ? 'Szukaj...' : 'Search...'; ?>" 
                       aria-label="Search"
                       required>
                <button type="submit" aria-label="Submit search">
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
                              stroke="currentColor" 
                              stroke-width="2" 
                              stroke-linecap="round" 
                              stroke-linejoin="round"
                              fill="none"/>
                    </svg>
                </button>
            </form>
        </div>
    <?php endif; ?>
</header>
