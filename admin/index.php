<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DBT Unity Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>DBT Unity</h2>
                <p>Admin Panel</p>
            </div>
            <ul class="nav-menu">
                <li><a href="?page=dashboard" <?php echo $page === 'dashboard' ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <li><a href="?page=content" <?php echo $page === 'content' ? 'class="active"' : ''; ?>>Content</a></li>
                <li><a href="?page=sections" <?php echo $page === 'sections' ? 'class="active"' : ''; ?>>Sections</a></li>
                <li><a href="?page=pages" <?php echo $page === 'pages' ? 'class="active"' : ''; ?>>Pages</a></li>
                <li><a href="?page=menu" <?php echo $page === 'menu' ? 'class="active"' : ''; ?>>Menu</a></li>
                <li><a href="?page=components" <?php echo $page === 'components' ? 'class="active"' : ''; ?>>Components</a></li>
                <li><a href="?page=seo" <?php echo $page === 'seo' ? 'class="active"' : ''; ?>>SEO</a></li>
                <li><a href="?page=diagnostics" <?php echo $page === 'diagnostics' ? 'class="active"' : ''; ?>>Diagnostics</a></li>
                <li><a href="?page=config" <?php echo $page === 'config' ? 'class="active"' : ''; ?>>Configuration</a></li>
                <li><a href="?page=media" <?php echo $page === 'media' ? 'class="active"' : ''; ?>>Media</a></li>
                <li><a href="?page=visualizer" <?php echo $page === 'visualizer' ? 'class="active"' : ''; ?>>Structure Visualizer</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php include "pages/{$page}.php"; ?>
        </main>
    </div>

    <script>
        // Common admin panel functionality
        const handleApiRequest = async (endpoint, method = 'GET', data = null) => {
            try {
                const options = {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };

                if (data) {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(`../api/${endpoint}`, options);
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'API request failed');
                }

                return result;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        };

        // Show/hide modal helper
        const toggleModal = (modalId, show = true) => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = show ? 'flex' : 'none';
            }
        };

        // Form submission helper
        const handleFormSubmit = async (form, endpoint) => {
            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                await handleApiRequest(endpoint, 'POST', data);
                window.location.reload();
            } catch (error) {
                alert(error.message);
            }
        };

        // Success message helper
        const showSuccess = (message) => {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success';
            alert.textContent = message;
            document.querySelector('.main-content').prepend(alert);
            setTimeout(() => alert.remove(), 3000);
        };

        // Error message helper
        const showError = (message) => {
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger';
            alert.textContent = message;
            document.querySelector('.main-content').prepend(alert);
            setTimeout(() => alert.remove(), 3000);
        };
    </script>
</body>
</html>
