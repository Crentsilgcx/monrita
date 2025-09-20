<?php
$layout = [
    'title' => 'Assessment result',
];
ob_start();
?>
<section class="card" style="max-width:720px;margin:0 auto;">
    <h1><?= htmlspecialchars($assessment['title']) ?> result</h1>
    <p>Score: <strong><?= number_format((float) ($attempt['score'] ?? 0), 1) ?></strong></p>
    <table class="table">
        <thead>
            <tr><th>Question</th><th>Response</th><th>Score</th></tr>
        </thead>
        <tbody>
        <?php foreach ($responses as $response): ?>
            <tr>
                <td>#<?= $response['question_id'] ?></td>
                <td><?= htmlspecialchars((string) ($response['answer_json']['value'] ?? '')) ?></td>
                <td><?= number_format((float) ($response['score'] ?? 0), 1) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a class="button secondary" href="/dashboard">Back to dashboard</a>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
