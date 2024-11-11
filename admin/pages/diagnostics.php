<?php
require_once __DIR__ . '/../../test.php';

// Run diagnostics
$diagnostics = new Diagnostics();
$errors = $diagnostics->runTests();

// Save errors to file for potential CLI usage
$diagnostics->saveErrorsToFile();
?>

<div class="diagnostics-panel">
    <h2>System Diagnostics</h2>
    
    <div class="actions">
        <button onclick="runDiagnostics('components')" class="btn btn-primary">Test Components</button>
        <button onclick="runDiagnostics('sections')" class="btn btn-primary">Test Sections</button>
        <button onclick="runDiagnostics()" class="btn btn-primary">Test All</button>
    </div>

    <div class="results">
        <?php if (empty($errors)): ?>
            <div class="alert alert-success">
                No errors found. All systems operational.
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Found <?= count($errors) ?> error(s):
            </div>
            
            <div class="error-list">
                <?php foreach ($errors as $error): ?>
                    <div class="error-item">
                        <span class="badge <?= $error['type'] === 'component' ? 'badge-primary' : 'badge-secondary' ?>">
                            <?= htmlspecialchars(ucfirst($error['type'])) ?>
                        </span>
                        <strong><?= htmlspecialchars($error['name']) ?>:</strong>
                        <?= htmlspecialchars($error['error']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.diagnostics-panel {
    padding: 20px;
}

.actions {
    margin-bottom: 20px;
}

.actions button {
    margin-right: 10px;
}

.error-list {
    margin-top: 20px;
}

.error-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.error-item:last-child {
    border-bottom: none;
}

.badge {
    margin-right: 10px;
}

.badge-primary {
    background-color: #007bff;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>

<script>
function runDiagnostics(type = null) {
    const url = type ? 
        `diagnostics.php?type=${encodeURIComponent(type)}` : 
        'diagnostics.php';
        
    window.location.href = url;
}
</script>
