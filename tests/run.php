<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Repositories\AssessmentQuestionRepository;
use App\Repositories\AssessmentRepository;
use App\Repositories\CareerRepository;
use App\Repositories\CareerSkillRepository;
use App\Repositories\CareerSubjectRepository;
use App\Repositories\LearnerRepository;
use App\Repositories\LearnerSkillRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\RecommendationRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\UserRepository;
use App\Services\CareerRecommendationService;
use App\Support\JsonStore;

$config = require __DIR__ . '/../bootstrap.php';
$store = new JsonStore($config['data_path']);

$users = new UserRepository($store);
$learnerRepository = new LearnerRepository($store);
$assessmentRepository = new AssessmentRepository($store);
$questionRepository = new AssessmentQuestionRepository($store);
$careerRepository = new CareerRepository($store);
$careerSkillRepository = new CareerSkillRepository($store);
$careerSubjectRepository = new CareerSubjectRepository($store);
$learnerSkillRepository = new LearnerSkillRepository($store);
$recommendations = new RecommendationRepository($store);
$subjectRepository = new SubjectRepository($store);
$programRepository = new ProgramRepository($store);

function assert_true(bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

assert_true($users->findByEmail('ama.mensah@example.com') !== null, 'Super admin user seeded.');
assert_true($assessmentRepository->active() !== [], 'Assessments available.');
assert_true(count($questionRepository->forAssessment(1)) >= 1, 'Assessment questions present.');

$learner = $learnerRepository->findByUserId(2);
assert_true($learner !== null, 'Learner profile available.');

$service = new CareerRecommendationService(
    $careerRepository,
    $careerSkillRepository,
    $careerSubjectRepository,
    $learnerSkillRepository,
    $recommendations,
    $subjectRepository,
    $programRepository,
);

$snapshot = $service->generateForLearner($learner['id'], [1 => 10], [1 => 5, 2 => 4]);
assert_true(count($snapshot['career_rankings']) > 0, 'Career rankings generated.');

echo "All checks passed." . PHP_EOL;
