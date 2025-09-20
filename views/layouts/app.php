<?php
/** @var array{title?:string,user?:array|null} $layout */
$title = $layout['title'] ?? 'Race JHS Platform';
$user = $layout['user'] ?? null;
$csrfToken = $layout['csrf'] ?? ($_SESSION['_csrf'] ?? '');
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
    <div class="brand">Race JHS</div>
    <nav>
        <?php if ($user): ?>
            <form method="post" action="/logout" style="display:inline;">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button class="button secondary" type="submit">Sign out</button>
            </form>
        <?php else: ?>
            <a class="button" href="/login">Sign in</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
    <?= $content ?? '' ?>
</main>
</body>
</html>
