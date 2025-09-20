<?php
$layout = [
    'title' => $course['title'] ?? 'Course',
];
ob_start();
?>
<article class="card">
    <h1><?= htmlspecialchars($course['title']) ?></h1>
    <p><?= htmlspecialchars($course['summary']) ?></p>
    <?php if (!$enrollment): ?>
        <form method="post" action="/courses/<?= $course['id'] ?>/enroll">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
            <button class="button" type="submit">Enroll now</button>
        </form>
    <?php else: ?>
        <span class="badge">Enrolled</span>
    <?php endif; ?>
</article>
<?php foreach ($modules as $module): ?>
    <section class="card">
        <h2><?= htmlspecialchars($module['title']) ?></h2>
        <ol>
            <?php foreach ($lessons[$module['id']] ?? [] as $lesson): ?>
                <?php $isCompleted = !empty($progress[$lesson['id']]) && $progress[$lesson['id']]['status'] === 'completed'; ?>
                <li style="margin-bottom:0.8rem;">
                    <strong><?= htmlspecialchars($lesson['title']) ?></strong>
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-top:0.35rem;">
                        <span class="badge"><?= htmlspecialchars($lesson['lesson_type']) ?></span>
                        <?php if ($isCompleted): ?>
                            <span style="color:var(--race-teal);font-weight:600;">Completed</span>
                        <?php else: ?>
                            <form method="post" action="/lessons/<?= $lesson['id'] ?>/complete">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                                <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/courses') ?>">
                                <button class="button secondary" type="submit">Mark complete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
<?php endforeach; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
