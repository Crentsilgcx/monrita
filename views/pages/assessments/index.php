<?php
$layout = [
    'title' => 'Assessments',
];
ob_start();
?>
<h1 style="color:var(--race-ink);margin-bottom:1.5rem;">Assessments</h1>
<div class="grid grid-two">
    <?php foreach ($assessments as $assessment): ?>
        <div class="card">
            <h2><?= htmlspecialchars($assessment['title']) ?></h2>
            <p><?= htmlspecialchars($assessment['description']) ?></p>
            <a class="button" href="/assessments/<?= $assessment['id'] ?>">Start</a>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
