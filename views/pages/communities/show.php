<?php
$layout = [
    'title' => $group['name'] ?? 'Group',
];
ob_start();
?>
<section class="card">
    <h1><?= htmlspecialchars($group['name']) ?></h1>
    <p><?= htmlspecialchars($group['description']) ?></p>
</section>
<section class="card">
    <h2>Post an update</h2>
    <form method="post" action="/communities/<?= $group['id'] ?>/posts">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
        <textarea name="body" rows="3" placeholder="Share something encouraging..." required></textarea>
        <button class="button" type="submit">Share</button>
    </form>
</section>
<?php foreach ($posts as $post): ?>
    <article id="post-<?= $post['id'] ?>" class="card">
        <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>
        <div style="display:flex;gap:0.75rem;margin-top:1rem;">
            <form method="post" action="/communities/<?= $group['id'] ?>/posts/<?= $post['id'] ?>/react">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                <select name="type">
                    <?php foreach (['like' => 'Like', 'insight' => 'Insight', 'question' => 'Question', 'helpful' => 'Helpful'] as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button secondary" type="submit">React</button>
            </form>
        </div>
        <section style="margin-top:1rem;">
            <h3 style="margin-bottom:0.5rem;font-size:1rem;">Comments</h3>
            <?php foreach ($post['comments'] as $comment): ?>
                <p style="background:rgba(13,68,147,0.06);padding:0.75rem;border-radius:1rem;">
                    <?= nl2br(htmlspecialchars($comment['body'])) ?><br>
                    <small><?= date('M j, H:i', strtotime($comment['created_at'])) ?></small>
                </p>
            <?php endforeach; ?>
            <form method="post" action="/communities/<?= $group['id'] ?>/posts/<?= $post['id'] ?>/comment">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
                <textarea name="body" rows="2" placeholder="Respond thoughtfully" required></textarea>
                <button class="button secondary" type="submit">Comment</button>
            </form>
        </section>
    </article>
<?php endforeach; ?>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
