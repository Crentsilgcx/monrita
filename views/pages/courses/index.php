<?php
$layout = [
    'title' => 'Courses',
];
ob_start();
?>
<h1 style="color:var(--race-ink);margin-bottom:1.5rem;">Courses</h1>
<div class="grid grid-two">
    <?php foreach ($courses as $course): ?>
        <div class="card">
            <h2><?= htmlspecialchars($course['title']) ?></h2>
            <p><?= htmlspecialchars($course['summary']) ?></p>
            <a class="button secondary" href="/courses/<?= $course['id'] ?>">View details</a>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
