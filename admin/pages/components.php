<?php
// Get all component directories
$componentDirs = glob(__DIR__ . '/../../components/*', GLOB_ONLYDIR);
$selectedComponent = $_GET['component'] ?? '';

// Function to get component name from path
function getComponentName($path) {
    return basename($path);
}
?>

<div class="components-container">
    <div class="components-sidebar">
        <h3>Components</h3>
        <ul class="components-list">
            <?php foreach ($componentDirs as $dir): ?>
                <?php 
                $componentName = getComponentName($dir);
                $isActive = $selectedComponent === $componentName ? 'active' : '';
                ?>
                <li>
                    <a href="?page=components&component=<?php echo $componentName; ?>" 
                       class="<?php echo $isActive; ?>">
                        <?php echo ucfirst($componentName); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="components-content">
        <?php
        if ($selectedComponent && in_array(__DIR__ . '/../../components/' . $selectedComponent, $componentDirs)) {
            $adminFile = __DIR__ . '/../../components/' . $selectedComponent . '/admin.php';
            if (file_exists($adminFile)) {
                include $adminFile;
            } else {
                echo '<div class="alert alert-warning">No admin interface available for this component.</div>';
            }
        } else {
            echo '<div class="alert alert-info">Select a component from the list to manage it.</div>';
        }
        ?>
    </div>
</div>

<style>
.components-container {
    display: flex;
    gap: 20px;
    height: 100%;
}

.components-sidebar {
    width: 200px;
    background: #f5f5f5;
    padding: 15px;
    border-radius: 5px;
}

.components-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.components-list li {
    margin-bottom: 8px;
}

.components-list a {
    display: block;
    padding: 8px 12px;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.components-list a:hover {
    background-color: #e0e0e0;
}

.components-list a.active {
    background-color: #007bff;
    color: white;
}

.components-content {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.alert-info {
    background-color: #e3f2fd;
    color: #0d47a1;
    border: 1px solid #bbdefb;
}

.alert-warning {
    background-color: #fff3e0;
    color: #e65100;
    border: 1px solid #ffe0b2;
}
</style>
