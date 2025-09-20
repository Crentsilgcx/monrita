<?php
$layout = [
    'title' => 'Dashboard',
    'user' => $user,
];
ob_start();
?>
<h1 style="font-size:2rem;color:var(--race-ink);margin-bottom:1.5rem;">Akwaaba, <?= htmlspecialchars($user['name'] ?? 'Learner') ?>!</h1>
<div class="grid grid-two">
    <div class="card">
        <h2>Courses</h2>
        <?php if ($enrollments): ?>
            <ul>
                <?php foreach ($enrollments as $enrollment): ?>
                    <?php $course = array_values(array_filter($courses, fn($c) => $c['id'] === $enrollment['course_id']))[0] ?? null; ?>
                    <?php if ($course): ?>
                        <li style="margin-bottom:0.8rem;">
                            <strong><?= htmlspecialchars($course['title']) ?></strong><br>
                            <span class="badge"><?= htmlspecialchars($course['visibility']) ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No enrollments yet. Explore <a href="/courses">courses</a> to get started.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h2>Assessments</h2>
        <ul>
            <?php foreach ($assessments as $assessment): ?>
                <li style="margin-bottom:0.8rem;">
                    <strong><?= htmlspecialchars($assessment['title']) ?></strong>
                    <div><a class="button secondary" href="/assessments/<?= $assessment['id'] ?>">Take assessment</a></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card">
        <h2>Activity feed</h2>
        <?php if ($feed): ?>
            <ul>
                <?php foreach ($feed as $item): ?>
                    <li><?= htmlspecialchars($item['verb']) ?> <?= htmlspecialchars($item['entity_type']) ?> • <?= date('M j', strtotime($item['created_at'])) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Activity will appear here as you engage with the platform.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h2>Recommended careers</h2>
        <?php if ($recommendation): ?>
            <ul>
                <?php foreach ($recommendation['snapshot_json']['career_rankings'] ?? [] as $entry): ?>
                    <li><strong><?= htmlspecialchars($entry['career']['name']) ?></strong> – score <?= number_format($entry['score'], 1) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Complete an assessment to unlock personalised career suggestions.</p>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
