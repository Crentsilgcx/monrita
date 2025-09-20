<?php
$layout = [
    'title' => 'Events',
];
ob_start();
?>
<h1 style="color:var(--race-ink);margin-bottom:1.5rem;">Events</h1>
<div class="grid grid-two">
    <?php foreach ($events as $event): ?>
        <div class="card">
            <h2><?= htmlspecialchars($event['title']) ?></h2>
            <p><?= htmlspecialchars($event['description']) ?></p>
            <p><strong><?= date('M j, g:i a', strtotime($event['starts_at'])) ?></strong></p>
            <form method="post" action="/events/<?= $event['id'] ?>/register">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf ?? '') ?>">
                <button class="button" type="submit">Register</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
