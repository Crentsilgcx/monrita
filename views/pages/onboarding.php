<?php
/** @var array $learner */
$layout = [
    'title' => 'Learner Onboarding',
];
ob_start();
?>
<section class="card" style="max-width:620px;margin:0 auto;">
    <h1>Complete your onboarding</h1>
    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/onboarding">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf) ?>">
        <?php if ($step === 'identity'): ?>
            <p>Confirm your identity and share guardian consent to access the platform.</p>
            <label for="date_of_birth">Date of birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?= htmlspecialchars($learner['date_of_birth'] ?? '') ?>" required>
            <label>
                <input type="checkbox" name="consent" <?= !empty($learner['guardian_consent']) ? 'checked' : '' ?>>
                My guardian has provided consent.
            </label>
        <?php elseif ($step === 'profile'): ?>
            <p>Tell us about your location and current JHS level to personalise learning.</p>
            <label for="district">District</label>
            <input type="text" name="district" id="district" value="<?= htmlspecialchars($learner['district'] ?? '') ?>" required>
            <label for="region">Region</label>
            <input type="text" name="region" id="region" value="<?= htmlspecialchars($learner['region'] ?? '') ?>" required>
            <label for="jhs_level">Current class</label>
            <select name="jhs_level" id="jhs_level" required>
                <?php foreach (['JHS1','JHS2','JHS3'] as $level): ?>
                    <option value="<?= $level ?>" <?= (($learner['jhs_level'] ?? '') === $level) ? 'selected' : '' ?>><?= $level ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p>Rate your interest in each subject to help us suggest careers.</p>
            <?php foreach ([1 => 'Mathematics', 2 => 'Integrated Science', 3 => 'ICT', 4 => 'English Language'] as $subjectId => $label): ?>
                <label for="subject-<?= $subjectId ?>"><?= $label ?> (0-5)</label>
                <input type="number" id="subject-<?= $subjectId ?>" name="subjects[<?= $subjectId ?>]" min="0" max="5" value="3">
            <?php endforeach; ?>
        <?php endif; ?>
        <button class="button" type="submit">Continue</button>
    </form>
</section>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
