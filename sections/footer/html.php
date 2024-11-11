<?php
require_once __DIR__ . '/query.php';

$footerQuery = new FooterQuery();
$data = $footerQuery->getFooterData();
?>

<footer class="site-footer">
    <div class="footer-content">
        <!-- Logo and Description -->
        <div class="footer-branding">
            <?php if (!empty($data['config']['logo'])): ?>
                <img src="<?php echo htmlspecialchars($data['config']['logo']); ?>" 
                     alt="DBT Unity" 
                     class="footer-logo">
            <?php endif; ?>
            
            <?php if (!empty($data['config']['description'])): ?>
                <p class="footer-description">
                    <?php echo htmlspecialchars($data['config']['description']); ?>
                </p>
            <?php endif; ?>

            <!-- Social Links -->
            <?php if (!empty($data['social'])): ?>
                <div class="social-links">
                    <?php foreach ($data['social'] as $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           aria-label="Follow us on <?php echo htmlspecialchars($social['platform']); ?>">
                            <?php echo $social['icon_svg']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer Links -->
        <?php if (!empty($data['links'])): ?>
            <div class="footer-links">
                <?php foreach ($data['links'] as $columnNum => $links): ?>
                    <div class="footer-column">
                        <?php foreach ($links as $link): ?>
                            <a href="<?php echo htmlspecialchars($link['url']); ?>" 
                               class="footer-link">
                                <?php echo htmlspecialchars($link['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
        <div class="copyright">
            <?php echo htmlspecialchars($data['config']['copyright'] ?? '© ' . date('Y') . ' DBT Unity. Все права защищены.'); ?>
        </div>
    </div>
</footer>
