<?php
/**
 * Simple landing page.
 */

declare(strict_types=1);

$siteTitle = 'Welcome to Monrita';
$today = new DateTimeImmutable('now', new DateTimeZone('UTC'));

$messages = [
    'Explore the features of this minimal PHP app.',
    'Customize this page by editing index.php.',
    'Current server time is shown below in UTC.'
];

// Choose a message based on the current minute so it changes over time.
$message = $messages[((int) $today->format('i')) % count($messages)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        :root {
            color-scheme: light dark;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        body {
            margin: 0;
            padding: 2rem;
            display: grid;
            place-items: center;
            min-height: 100vh;
            background: radial-gradient(circle at top left, #f0f4ff, #d9e2f3);
        }

        main {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 1.5rem;
            padding: 2.5rem;
            max-width: 32rem;
            text-align: center;
            box-shadow: 0 1rem 2.5rem rgba(15, 23, 42, 0.2);
            backdrop-filter: blur(6px);
        }

        h1 {
            margin-top: 0;
            font-size: clamp(2rem, 4vw, 3rem);
            color: #1d3a6b;
        }

        p {
            line-height: 1.6;
            color: #334155;
        }

        time {
            display: inline-block;
            margin-top: 1rem;
            font-weight: 600;
            color: #1d4ed8;
        }
    </style>
</head>
<body>
<main>
    <h1><?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>
        <strong>Server time (UTC):</strong>
        <time datetime="<?= $today->format(DateTimeInterface::ATOM); ?>">
            <?= $today->format('l, F j Y \a\t H:i:s \U\T\C'); ?>
        </time>
    </p>
</main>
</body>
</html>
