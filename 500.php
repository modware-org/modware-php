<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        .error-container {
            max-width: 600px;
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #e74c3c;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
        }
        .home-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .home-link:hover {
            background-color: #2980b9;
        }
        .error-details {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            font-family: monospace;
            text-align: left;
            display: none;
        }
        .show-details {
            margin-top: 10px;
            background: none;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            color: #666;
        }
        .show-details:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>500 Internal Server Error</h1>
        <p>Sorry, something went wrong on our end. Our team has been notified and is working to fix the issue.</p>
        <p>Please try again later.</p>
        <a href="/" class="home-link">Return to Homepage</a>
        
        <?php if (isset($e) && defined('DEBUG') && DEBUG === true): ?>
            <button class="show-details" onclick="toggleDetails()">Show Technical Details</button>
            <div class="error-details" id="errorDetails">
                <strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?><br>
                <strong>File:</strong> <?php echo htmlspecialchars($e->getFile()); ?><br>
                <strong>Line:</strong> <?php echo $e->getLine(); ?><br>
                <strong>Trace:</strong><br>
                <pre><?php echo htmlspecialchars($e->getTraceAsString()); ?></pre>
            </div>
            <script>
                function toggleDetails() {
                    var details = document.getElementById('errorDetails');
                    details.style.display = details.style.display === 'none' ? 'block' : 'none';
                }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
