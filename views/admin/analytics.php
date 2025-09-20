<?php
$layout = [
    'title' => 'Analytics dashboard',
];
ob_start();
?>
<h1 style="color:var(--race-ink);margin-bottom:1.5rem;">Key metrics</h1>
<div class="grid grid-two">
    <?php foreach ($metrics as $label => $value): ?>
        <div class="card stat">
            <span><?= ucwords(str_replace('_', ' ', $label)) ?></span>
            <span><?= htmlspecialchars((string) $value) ?></span>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
