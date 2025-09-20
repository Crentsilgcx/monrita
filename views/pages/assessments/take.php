<?php
$layout = [
    'title' => $assessment['title'] ?? 'Assessment',
];
ob_start();
?>
<section class="card" style="max-width:720px;margin:0 auto;">
    <h1><?= htmlspecialchars($assessment['title']) ?></h1>
    <p><?= htmlspecialchars($assessment['description']) ?></p>
    <form method="post" action="/assessments/<?= $assessment['id'] ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
        <?php foreach ($questions as $question): ?>
            <div style="margin-bottom:1.5rem;">
                <label for="question-<?= $question['id'] ?>" style="font-weight:600;"><?= htmlspecialchars($question['prompt']) ?></label>
                <input type="number" id="question-<?= $question['id'] ?>" name="question_<?= $question['id'] ?>" min="1" max="5" required>
            </div>
        <?php endforeach; ?>
        <button class="button" type="submit">Submit responses</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
