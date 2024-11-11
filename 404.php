<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Страница не найдена</title>
    <link rel="stylesheet" href="/styles.css">
    <style>
        .error-container {
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
        }
        .error-message {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 1rem 0;
        }
        .error-description {
            color: #7f8c8d;
            margin-bottom: 2rem;
        }
        .home-link {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .home-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Страница не найдена</h2>
        <p class="error-description">
            К сожалению, запрашиваемая страница не существует или была перемещена.
        </p>
        <a href="/" class="home-link">Вернуться на главную</a>
    </div>
</body>
</html>
