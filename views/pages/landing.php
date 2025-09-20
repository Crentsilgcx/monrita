<?php
/** @var array|null $user */
$layout = [
    'title' => 'Race JHS Platform',
    'user' => $user ?? null,
];
ob_start();
?>
<section class="card" style="text-align:center;">
    <h1 style="font-size:2.4rem;color:var(--race-ink);">Empowering Ghanaian JHS Learners</h1>
    <p style="font-size:1.05rem;line-height:1.8;max-width:650px;margin:1rem auto;">
        Race JHS helps learners in JHS1–3 explore interests, build skills, and connect with future career pathways.
        Secure onboarding, guided assessments, and vibrant communities make it easy to grow together.
    </p>
    <div style="margin-top:2rem;display:flex;gap:1rem;justify-content:center;">
        <a class="button" href="<?= $user ? '/dashboard' : '/login' ?>">
            <?= $user ? 'Go to dashboard' : 'Sign in' ?>
        </a>
        <a class="button secondary" href="#features">Explore features</a>
    </div>
</section>
<section id="features" class="grid grid-two" style="margin-top:2.5rem;">
    <div class="card">
        <h2>Learner Onboarding</h2>
        <p>Invite-only onboarding with guardian consent ensures every learner enters the platform safely and confidently.</p>
    </div>
    <div class="card">
        <h2>Assessments &amp; Careers</h2>
        <p>Short interest and values assessments combine with subject preferences to recommend tailored career pathways.</p>
    </div>
    <div class="card">
        <h2>Courses &amp; Projects</h2>
        <p>Interactive lessons, project-based learning, and mentor feedback keep learners engaged and progressing.</p>
    </div>
    <div class="card">
        <h2>Communities &amp; Events</h2>
        <p>Safe discussion groups and school events encourage collaboration, curiosity, and celebration.</p>
    </div>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
