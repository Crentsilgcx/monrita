<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Support\JsonStore;

$config = require __DIR__ . '/../bootstrap.php';
$store = new JsonStore($config['data_path']);

$password = password_hash('Password123!', PASSWORD_DEFAULT);

$roles = [
    ['id' => 1, 'name' => 'Super Admin', 'slug' => 'super_admin', 'permissions' => ['analytics.view', 'users.manage', 'courses.manage']],
    ['id' => 2, 'name' => 'Learner', 'slug' => 'learner', 'permissions' => []],
];

$permissions = [];

$users = [
    '1' => [
        'id' => 1,
        'organization_id' => 1,
        'school_id' => 1,
        'name' => 'Ama Mensah',
        'email' => 'ama.mensah@example.com',
        'phone' => '+233201234567',
        'email_verified_at' => (new DateTimeImmutable())->format(DATE_ATOM),
        'password' => $password,
        'status' => 'active',
        'last_login_at' => null,
        'remember_token' => null,
        'roles' => ['super_admin'],
        'permissions' => [],
    ],
    '2' => [
        'id' => 2,
        'organization_id' => 1,
        'school_id' => 1,
        'name' => 'Kojo Asare',
        'email' => 'kojo.asare@example.com',
        'phone' => '+233209876543',
        'email_verified_at' => null,
        'password' => $password,
        'status' => 'active',
        'last_login_at' => null,
        'remember_token' => null,
        'roles' => ['learner'],
        'permissions' => [],
    ],
];

$learners = [
    '1' => [
        'id' => 1,
        'user_id' => 2,
        'organization_id' => 1,
        'school_id' => 1,
        'jhs_level' => 'JHS2',
        'date_of_birth' => null,
        'gender' => 'female',
        'address_line' => 'Community 5',
        'community' => 'Tema',
        'district' => null,
        'region' => null,
        'guardian_name' => 'Esi Asare',
        'guardian_phone' => '+233208888888',
        'guardian_email' => null,
        'household_info' => [],
        'consents' => [],
        'onboarding_completed_at' => null,
        'onboarding_step' => 'identity',
    ],
];

$organizations = [
    '1' => ['id' => 1, 'name' => 'Race JHS Network', 'type' => 'NGO'],
];

$schools = [
    '1' => ['id' => 1, 'organization_id' => 1, 'name' => 'Race JHS Accra', 'district' => 'Accra Metro', 'region' => 'Greater Accra', 'level' => 'JHS'],
];

$courses = [
    '1' => ['id' => 1, 'organization_id' => 1, 'school_id' => 1, 'code' => 'LIT101', 'title' => 'Creative Writing Basics', 'summary' => 'Spark imagination through storytelling.', 'cover_url' => null, 'visibility' => 'school', 'meta' => []],
    '2' => ['id' => 2, 'organization_id' => 1, 'school_id' => 1, 'code' => 'STEM201', 'title' => 'STEM Explorers', 'summary' => 'Hands-on science and maths challenges.', 'cover_url' => null, 'visibility' => 'school', 'meta' => []],
    '3' => ['id' => 3, 'organization_id' => 1, 'school_id' => 1, 'code' => 'LIFE101', 'title' => 'Life Skills Lab', 'summary' => 'Build resilience and communication skills.', 'cover_url' => null, 'visibility' => 'school', 'meta' => []],
];

$modules = [
    '1' => ['id' => 1, 'course_id' => 1, 'title' => 'Story Foundations', 'order' => 1],
    '2' => ['id' => 2, 'course_id' => 2, 'title' => 'Science Investigations', 'order' => 1],
    '3' => ['id' => 3, 'course_id' => 3, 'title' => 'Communication', 'order' => 1],
];

$lessons = [
    '1' => ['id' => 1, 'module_id' => 1, 'title' => 'Character Building', 'content_html' => '<p>Define characters and motivations.</p>', 'order' => 1, 'duration_minutes' => 25, 'lesson_type' => 'page', 'package_path' => null, 'meta' => []],
    '2' => ['id' => 2, 'module_id' => 2, 'title' => 'Build a Bridge', 'content_html' => '<p>Experiment with materials.</p>', 'order' => 1, 'duration_minutes' => 30, 'lesson_type' => 'page', 'package_path' => null, 'meta' => []],
    '3' => ['id' => 3, 'module_id' => 3, 'title' => 'Active Listening', 'content_html' => '<p>Practice listening with peers.</p>', 'order' => 1, 'duration_minutes' => 20, 'lesson_type' => 'page', 'package_path' => null, 'meta' => []],
];

$enrollments = [];
$lessonProgress = [];

$assessments = [
    '1' => ['id' => 1, 'assessment_type_id' => 1, 'title' => 'Interests Screener', 'description' => 'Discover the activities you enjoy most.', 'is_active' => true],
];

$assessmentQuestions = [
    '1' => ['id' => 1, 'assessment_id' => 1, 'code' => 'RIASEC_R', 'prompt' => 'I enjoy fixing or building things.', 'response_type' => 'likert', 'options' => [], 'weight' => 1.2],
    '2' => ['id' => 2, 'assessment_id' => 1, 'code' => 'RIASEC_A', 'prompt' => 'I love performing or creating art.', 'response_type' => 'likert', 'options' => [], 'weight' => 1.0],
    '3' => ['id' => 3, 'assessment_id' => 1, 'code' => 'RIASEC_E', 'prompt' => 'I like leading teams toward a goal.', 'response_type' => 'likert', 'options' => [], 'weight' => 1.1],
];

$assessmentAttempts = [];
$assessmentResponses = [];

$groups = [
    '1' => ['id' => 1, 'organization_id' => 1, 'school_id' => 1, 'name' => 'STEM Innovators', 'description' => 'Share science discoveries and projects.', 'visibility' => 'school', 'is_moderated' => true],
    '2' => ['id' => 2, 'organization_id' => 1, 'school_id' => 1, 'name' => 'Creative Writers', 'description' => 'Poetry, spoken word, and stories.', 'visibility' => 'school', 'is_moderated' => true],
];

$posts = [
    '1' => ['id' => 1, 'group_id' => 1, 'user_id' => 2, 'body' => 'Excited for the next science challenge!', 'attachments' => [], 'created_at' => (new DateTimeImmutable('-1 day'))->format(DATE_ATOM)],
];

$comments = [];
$reactions = [];

$events = [
    '1' => ['id' => 1, 'organization_id' => 1, 'school_id' => 1, 'title' => 'Career Discovery Fair', 'description' => 'Meet mentors from different industries.', 'starts_at' => (new DateTimeImmutable('+7 days'))->format(DATE_ATOM), 'ends_at' => (new DateTimeImmutable('+7 days +2 hours'))->format(DATE_ATOM), 'location' => 'School Hall', 'visibility' => 'school'],
    '2' => ['id' => 2, 'organization_id' => 1, 'school_id' => 1, 'title' => 'Community Service Day', 'description' => 'Give back to the community together.', 'starts_at' => (new DateTimeImmutable('+14 days'))->format(DATE_ATOM), 'ends_at' => (new DateTimeImmutable('+14 days +3 hours'))->format(DATE_ATOM), 'location' => 'Tema Community', 'visibility' => 'school'],
];

$eventRegistrations = [];

$careers = [
    '1' => ['id' => 1, 'name' => 'Software Engineer', 'description' => 'Builds and improves digital tools.', 'meta' => []],
    '2' => ['id' => 2, 'name' => 'Nurse', 'description' => 'Cares for patients in clinics and hospitals.', 'meta' => []],
    '3' => ['id' => 3, 'name' => 'Electrician', 'description' => 'Installs and maintains electrical systems.', 'meta' => []],
];

$subjects = [
    '1' => ['id' => 1, 'name' => 'Mathematics', 'code' => 'MATH'],
    '2' => ['id' => 2, 'name' => 'Science', 'code' => 'SCI'],
    '3' => ['id' => 3, 'name' => 'ICT', 'code' => 'ICT'],
];

$programs = [
    '1' => ['id' => 1, 'name' => 'General Science', 'description' => 'Focus on sciences for SHS.', 'meta' => []],
    '2' => ['id' => 2, 'name' => 'Home Economics', 'description' => 'Health, nutrition, and care careers.', 'meta' => []],
    '3' => ['id' => 3, 'name' => 'Technical Skills', 'description' => 'Hands-on technical pathways.', 'meta' => []],
];

$careerSkills = [
    '1' => ['id' => 1, 'career_id' => 1, 'skill_id' => 1, 'weight' => 1.5],
    '2' => ['id' => 2, 'career_id' => 2, 'skill_id' => 2, 'weight' => 1.3],
    '3' => ['id' => 3, 'career_id' => 3, 'skill_id' => 3, 'weight' => 1.4],
];

$skills = [
    '1' => ['id' => 1, 'name' => 'Problem Solving', 'slug' => 'problem-solving', 'description' => 'Identify and solve challenges.'],
    '2' => ['id' => 2, 'name' => 'Caregiving', 'slug' => 'caregiving', 'description' => 'Support others with empathy.'],
    '3' => ['id' => 3, 'name' => 'Technical Craft', 'slug' => 'technical-craft', 'description' => 'Use tools to build and repair.'],
];

$careerSubjects = [
    '1' => ['id' => 1, 'career_id' => 1, 'subject_id' => 3, 'weight' => 1.2],
    '2' => ['id' => 2, 'career_id' => 2, 'subject_id' => 2, 'weight' => 1.4],
    '3' => ['id' => 3, 'career_id' => 3, 'subject_id' => 1, 'weight' => 1.1],
];

$learnerSkills = [
    '1' => ['id' => 1, 'learner_id' => 1, 'skill_id' => 1, 'level' => 3, 'evidence_url' => null],
    '2' => ['id' => 2, 'learner_id' => 1, 'skill_id' => 2, 'level' => 2, 'evidence_url' => null],
];

$learnerAttributes = [];
$recommendations = [];
$feed = [];
$organizationsData = $organizations;
$schoolsData = $schools;

$store->write('roles', array_column($roles, null, 'id'));
$store->write('permissions', $permissions);
$store->write('users', $users);
$store->write('learners', $learners);
$store->write('organizations', $organizationsData);
$store->write('schools', $schoolsData);
$store->write('courses', $courses);
$store->write('course_modules', $modules);
$store->write('lessons', $lessons);
$store->write('enrollments', $enrollments);
$store->write('lesson_progress', $lessonProgress);
$store->write('assessments', $assessments);
$store->write('assessment_questions', $assessmentQuestions);
$store->write('assessment_attempts', $assessmentAttempts);
$store->write('assessment_responses', $assessmentResponses);
$store->write('groups', $groups);
$store->write('posts', $posts);
$store->write('comments', $comments);
$store->write('reactions', $reactions);
$store->write('events', $events);
$store->write('event_registrations', $eventRegistrations);
$store->write('careers', $careers);
$store->write('subjects', $subjects);
$store->write('programs', $programs);
$store->write('career_skills', $careerSkills);
$store->write('skills', $skills);
$store->write('career_subjects', $careerSubjects);
$store->write('learner_skills', $learnerSkills);
$store->write('learner_attributes', $learnerAttributes);
$store->write('learner_recommendations', $recommendations);
$store->write('feed_items', $feed);

file_put_contents($config['audit_log'], "Seed completed at " . (new DateTimeImmutable())->format(DATE_ATOM) . PHP_EOL);

echo "Seeded data. Super admin login: ama.mensah@example.com / Password123!" . PHP_EOL;
