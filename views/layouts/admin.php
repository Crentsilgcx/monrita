<?php
$title = $layout['title'] ?? 'Admin • Race JHS';
$user = $layout['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/public/css/app.css">
</head>
<body>
<header class="navbar">
    <div class="brand">Admin Console</div>
    <nav>
        <a class="button secondary" href="/dashboard">Back to app</a>
    </nav>
</header>
<main class="container">
    <?= $content ?? '' ?>
</main>
</body>
</html>
