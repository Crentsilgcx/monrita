<?php
$layout = [
    'title' => 'Communities',
];
ob_start();
?>
<h1 style="color:var(--race-ink);margin-bottom:1.5rem;">Communities</h1>
<div class="grid grid-two">
    <?php foreach ($groups as $group): ?>
        <div class="card">
            <h2><?= htmlspecialchars($group['name']) ?></h2>
            <p><?= htmlspecialchars($group['description']) ?></p>
            <a class="button secondary" href="/communities/<?= $group['id'] ?>">Open group</a>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
