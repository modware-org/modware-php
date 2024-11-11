<?php
// Visualizer Section Index
require_once 'query.php';
require_once 'components/files/html.php';

// Initialize files component
$filesComponent = new FilesComponent('uploads/');

// Render files
$filesHtml = $filesComponent->render('grid');

// You can add additional visualizer-specific logic here if needed
?>
<div class="visualizer-section">
    <h2>File Visualizer</h2>
    <?php echo $filesHtml; ?>
</div>
