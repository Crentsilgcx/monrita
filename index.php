<?php

declare(strict_types=1);

$config = require __DIR__ . '/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AssessmentController;
use App\Controllers\AuthController;
use App\Controllers\CommunityController;
use App\Controllers\CourseController;
use App\Controllers\DashboardController;
use App\Controllers\EventController;
use App\Controllers\OnboardingController;
use App\Http\Request;
use App\Http\Router;
use App\Repositories\AssessmentAttemptRepository;
use App\Repositories\AssessmentQuestionRepository;
use App\Repositories\AssessmentRepository;
use App\Repositories\AssessmentResponseRepository;
use App\Repositories\CareerRepository;
use App\Repositories\CareerSkillRepository;
use App\Repositories\CareerSubjectRepository;
use App\Repositories\CommentRepository;
use App\Repositories\CourseRepository;
use App\Repositories\PostRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\EventRegistrationRepository;
use App\Repositories\EventRepository;
use App\Repositories\FeedRepository;
use App\Repositories\GroupRepository;
use App\Repositories\LearnerAttributeRepository;
use App\Repositories\LearnerRepository;
use App\Repositories\LearnerSkillRepository;
use App\Repositories\LessonProgressRepository;
use App\Repositories\LessonRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\RecommendationRepository;
use App\Repositories\ReactionRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\SkillRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\UserRepository;
use App\Services\AnalyticsService;
use App\Services\CareerRecommendationService;
use App\Services\FeedService;
use App\Support\AuditLogger;
use App\Support\Auth;
use App\Support\Cache;
use App\Support\CachedRepository;
use App\Support\Csrf;
use App\Support\JsonStore;
use App\Support\ProfanityFilter;
use App\Support\SessionManager;
use App\Support\View;


$session = new SessionManager();
$session->start();

$request = Request::capture();

$staticPath = $request->path();
if (str_starts_with($staticPath, '/public/')) {
    $file = __DIR__ . $staticPath;
    if (is_file($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $types = ['css' => 'text/css', 'js' => 'application/javascript', 'woff2' => 'font/woff2'];
        if (isset($types[$ext])) {
            header('Content-Type: ' . $types[$ext]);
        }
        readfile($file);
        return;
    }
}
$store = new JsonStore($config['data_path']);
$cache = new Cache($config['cache_path']);
$view = new View(__DIR__ . '/views');
$audit = new AuditLogger($config['audit_log']);

$userRepository = new UserRepository($store);
$learnerRepository = new LearnerRepository($store);
$learnerSkillRepository = new LearnerSkillRepository($store);
$learnerAttributeRepository = new LearnerAttributeRepository($store);
$assessmentRepository = new AssessmentRepository($store);
$questionRepository = new AssessmentQuestionRepository($store);
$attemptRepository = new AssessmentAttemptRepository($store);
$responseRepository = new AssessmentResponseRepository($store);
$courseRepository = new CourseRepository($store);
$moduleRepository = new ModuleRepository($store);
$lessonRepository = new LessonRepository($store);
$enrollmentRepository = new EnrollmentRepository($store);
$progressRepository = new LessonProgressRepository($store);
$groupRepository = new GroupRepository($store);
$postRepository = new PostRepository($store);
$commentRepository = new CommentRepository($store);
$reactionRepository = new ReactionRepository($store);
$eventRepository = new EventRepository($store);
$registrationRepository = new EventRegistrationRepository($store);
$feedRepository = new FeedRepository($store);
$careerRepository = new CareerRepository($store);
$careerSkillRepository = new CareerSkillRepository($store);
$careerSubjectRepository = new CareerSubjectRepository($store);
$subjectRepository = new SubjectRepository($store);
$programRepository = new ProgramRepository($store);
$recommendationRepository = new RecommendationRepository($store);
$organizationRepository = new OrganizationRepository($store);
$schoolRepository = new SchoolRepository($store);
$skillRepository = new SkillRepository($store);

$auth = new Auth($session, $userRepository);
$csrf = new Csrf($session);
$careerService = new CareerRecommendationService(
    $careerRepository,
    $careerSkillRepository,
    $careerSubjectRepository,
    $learnerSkillRepository,
    $recommendationRepository,
    $subjectRepository,
    $programRepository,
);
$feedService = new FeedService($feedRepository);
$analytics = new AnalyticsService($learnerRepository, $attemptRepository, $progressRepository, $enrollmentRepository);
$courseCache = new CachedRepository($cache, $courseRepository);
$profanity = new ProfanityFilter();

$authController = new AuthController($view, $auth, $csrf, $session, $audit);
$dashboardController = new DashboardController($view, $auth, $enrollmentRepository, $progressRepository, $assessmentRepository, $feedRepository, $learnerRepository, $recommendationRepository, $courseRepository);
$onboardingController = new OnboardingController($view, $auth, $learnerRepository, $learnerAttributeRepository, $careerService, $csrf);
$courseController = new CourseController($view, $auth, $courseCache, $courseRepository, $moduleRepository, $lessonRepository, $enrollmentRepository, $progressRepository, $csrf, $feedService);
$assessmentController = new AssessmentController($view, $assessmentRepository, $questionRepository, $attemptRepository, $responseRepository, $learnerRepository, $careerService, $auth, $csrf, $feedService);
$communityController = new CommunityController($view, $groupRepository, $postRepository, $commentRepository, $reactionRepository, $auth, $csrf, $profanity, $feedService);
$eventController = new EventController($view, $eventRepository, $registrationRepository, $auth, $csrf);
$adminController = new AdminController($view, $analytics, $auth);

$router = new Router();

$router->get('/', fn () => new App\Http\Response($view->render('pages/landing', ['user' => $auth->user()])));
$router->get('/login', fn () => $authController->showLogin());
$router->post('/login', fn (Request $request) => $authController->login($request));
$router->post('/logout', fn (Request $request) => $authController->logout($request));
$router->get('/dashboard', fn () => $dashboardController->show());
$router->get('/onboarding', fn () => $onboardingController->show());
$router->post('/onboarding', fn (Request $request) => $onboardingController->submit($request));
$router->get('/courses', fn () => $courseController->index());
$router->get('/courses/{id}', fn (Request $request, int $id) => $courseController->show($id));
$router->post('/courses/{id}/enroll', fn (Request $request, int $id) => $courseController->enroll($request, $id));
$router->post('/lessons/{id}/complete', fn (Request $request, int $id) => $courseController->completeLesson($request, $id));
$router->get('/assessments', fn () => $assessmentController->index());
$router->get('/assessments/{id}', fn (Request $request, int $id) => $assessmentController->take($id));
$router->post('/assessments/{id}', fn (Request $request, int $id) => $assessmentController->submit($request, $id));
$router->get('/assessments/{id}/result', fn (Request $request, int $id) => $assessmentController->result($request, $id));
$router->get('/communities', fn () => $communityController->index());
$router->get('/communities/{id}', fn (Request $request, int $id) => $communityController->show($id));
$router->post('/communities/{id}/posts', fn (Request $request, int $id) => $communityController->post($request, $id));
$router->post('/communities/{groupId}/posts/{postId}/comment', fn (Request $request, int $groupId, int $postId) => $communityController->comment($request, $groupId, $postId));
$router->post('/communities/{groupId}/posts/{postId}/react', fn (Request $request, int $groupId, int $postId) => $communityController->react($request, $groupId, $postId));
$router->get('/events', fn () => $eventController->index());
$router->post('/events/{id}/register', fn (Request $request, int $id) => $eventController->register($request, $id));
$router->get('/admin/analytics', fn () => $adminController->analytics());

$user = $auth->user();
$path = rtrim($request->path(), '/') ?: '/';
$allowedDuringOnboarding = ['/onboarding', '/logout', '/login'];
$allowPrefixes = ['/css', '/fonts'];
if ($user) {
    $learner = $learnerRepository->findByUserId($user['id']);
    if ($learner && empty($learner['onboarding_completed_at'])) {
        $allowed = in_array($path, $allowedDuringOnboarding, true);
        foreach ($allowPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            header('Location: /onboarding', true, 302);
            exit;
        }
    }
}

$response = $router->dispatch($request);
$response->send();
