<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;
use App\Repositories\AssessmentRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\FeedRepository;
use App\Repositories\LearnerRepository;
use App\Repositories\LessonProgressRepository;
use App\Repositories\RecommendationRepository;
use App\Repositories\CourseRepository;
use App\Support\Auth;
use App\Support\View;

final class DashboardController
{
    public function __construct(
        private View $view,
        private Auth $auth,
        private EnrollmentRepository $enrollments,
        private LessonProgressRepository $progress,
        private AssessmentRepository $assessments,
        private FeedRepository $feeds,
        private LearnerRepository $learners,
        private RecommendationRepository $recommendations,
        private CourseRepository $courses,
    ) {
    }

    public function show(): Response
    {
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }

        $enrollments = $this->enrollments->forUser($user['id']);
        $progress = $this->progress->forUser($user['id']);
        $progressByLesson = [];
        foreach ($progress as $record) {
            $progressByLesson[$record['lesson_id']] = $record;
        }
        $assessments = $this->assessments->active();
        $feed = $this->feeds->latest();
        $learner = $this->learners->findByUserId($user['id']);
        $recommendation = $learner ? $this->recommendations->findForLearner($learner['id']) : null;
        $courses = $this->courses->all();

        $content = $this->view->render('pages/dashboard', [
            'user' => $user,
            'enrollments' => $enrollments,
            'progress' => $progressByLesson,
            'assessments' => $assessments,
            'feed' => $feed,
            'recommendation' => $recommendation,
            'courses' => $courses,
        ]);

        return new Response($content);
    }
}
