<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/Database.php';

session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit;
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for JSON input first
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // If not JSON, use POST data
    if (!$data) {
        $data = [
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
    }

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(401);
        if (!empty($json)) {
            exit(json_encode(['error' => 'Invalid credentials']));
        }
        $error = 'Invalid credentials';
    } else {
        try {
            $db = AdminDatabase::getInstance();
            
            $query = "SELECT id, username, password_hash FROM admin_users WHERE username = :username AND is_active = 1";
            $result = $db->query($query, [':username' => $username]);
            
            if ($result) {
                $user = $result->fetchArray(SQLITE3_ASSOC);
                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    if (!empty($json)) {
                        exit(json_encode(['success' => true]));
                    }
                    header('Location: index.php?page=dashboard');
                    exit;
                }
            }
            
            http_response_code(401);
            if (!empty($json)) {
                exit(json_encode(['error' => 'Invalid credentials']));
            }
            $error = 'Invalid credentials';
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            http_response_code(401);
            if (!empty($json)) {
                exit(json_encode(['error' => 'Invalid credentials']));
            }
            $error = 'Invalid credentials';
        }
    }
}

// Only show the login form for GET requests or failed POST with form data
if ($_SERVER['REQUEST_METHOD'] === 'GET' || (isset($error) && empty($json))) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php
}
?>
