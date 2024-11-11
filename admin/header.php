<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit;
}

// Get current user info if logged in
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT * FROM app_admin.admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-header">
                <h1>Admin Panel</h1>
                <?php if ($currentUser): ?>
                    <span class="user-info">
                        <?php echo htmlspecialchars($currentUser['username']); ?> 
                        (<?php echo htmlspecialchars($currentUser['role']); ?>)
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="nav-section">
                <h3>Content Management</h3>
                <ul>
                    <li><a href="pages/dashboard.php">Dashboard</a></li>
                    <li><a href="pages/pages.php">Pages</a></li>
                </ul>
            </div>

            <div class="nav-section">
                <h3>Sections</h3>
                <ul>
                    <li><a href="pages/sections.php">Manage Sections</a></li>
                    <li><a href="pages/section-templates.php">Section Templates</a></li>
                </ul>
            </div>

            <div class="nav-section">
                <h3>Components</h3>
                <ul>
                    <li><a href="pages/components.php">Manage Components</a></li>
                    <li><a href="pages/upload.php">Upload Manager</a></li>
                    <li><a href="pages/gallery.php">Gallery Manager</a></li>
                    <li><a href="pages/files.php">File Manager</a></li>
                </ul>
            </div>

            <div class="nav-section">
                <h3>Modules</h3>
                <ul>
                    <li><a href="pages/modules.php">Manage Modules</a></li>
                    <li><a href="pages/blog.php">Blog Module</a></li>
                    <li><a href="pages/rss.php">RSS Module</a></li>
                    <li><a href="pages/sitemap.php">Sitemap Module</a></li>
                </ul>
            </div>

            <div class="nav-section">
                <h3>Integrations</h3>
                <ul>
                    <li><a href="pages/integrations.php">Manage Integrations</a></li>
                    <li><a href="pages/shortcodes.php">Shortcodes</a></li>
                    <li><a href="pages/webhooks.php">Webhooks</a></li>
                    <li><a href="pages/api-keys.php">API Keys</a></li>
                    <li><a href="pages/translations.php">Translations</a></li>
                </ul>
            </div>

            <div class="nav-section">
                <h3>System</h3>
                <ul>
                    <li><a href="pages/settings.php">Settings</a></li>
                    <li><a href="pages/users.php">Users</a></li>
                    <li><a href="pages/diagnostics.php">Diagnostics</a></li>
                    <li><a href="visualizer.php">Structure Visualizer</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>

        <main class="admin-content">
