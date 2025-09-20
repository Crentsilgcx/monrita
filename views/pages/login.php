<?php
$layout = [
    'title' => 'Sign in • Race JHS',
];
ob_start();
?>
<section class="card" style="max-width:480px;margin:0 auto;">
    <h1 style="color:var(--race-ink);margin-bottom:1.5rem;">Sign in</h1>
    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/login">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <button class="button" type="submit">Continue</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
