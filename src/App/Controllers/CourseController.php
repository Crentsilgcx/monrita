<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\CourseRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\LessonProgressRepository;
use App\Repositories\LessonRepository;
use App\Repositories\ModuleRepository;
use App\Services\FeedService;
use App\Support\Auth;
use App\Support\CachedRepository;
use App\Support\Csrf;
use App\Support\View;

final class CourseController
{
    public function __construct(
        private View $view,
        private Auth $auth,
        private CachedRepository $courseCache,
        private CourseRepository $courses,
        private ModuleRepository $modules,
        private LessonRepository $lessons,
        private EnrollmentRepository $enrollments,
        private LessonProgressRepository $progress,
        private Csrf $csrf,
        private FeedService $feed,
    ) {
    }

    public function index(): Response
    {
        $courses = $this->courseCache->all('courses:index');
        $content = $this->view->render('pages/courses/index', [
            'courses' => $courses,
            'user' => $this->auth->user(),
        ]);

        return new Response($content);
    }

    public function show(int $courseId): Response
    {
        $course = $this->courses->find($courseId);
        if (!$course) {
            return new Response('Course not found', 404);
        }
        $modules = $this->modules->forCourse($courseId);
        $lessons = [];
        foreach ($modules as $module) {
            $lessons[$module['id']] = $this->lessons->forModule($module['id']);
        }
        $user = $this->auth->user();
        $enrollment = $user ? $this->enrollments->findForCourseUser($courseId, $user['id']) : null;
        $progress = [];
        if ($user) {
            foreach ($this->progress->forUser($user['id']) as $record) {
                $progress[$record['lesson_id']] = $record;
            }
        }
        $content = $this->view->render('pages/courses/show', [
            'course' => $course,
            'modules' => $modules,
            'lessons' => $lessons,
            'enrollment' => $enrollment,
            'progress' => $progress,
            'csrf' => $this->csrf->token(),
        ]);

        return new Response($content);
    }

    public function enroll(Request $request, int $courseId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        if (!$this->enrollments->findForCourseUser($courseId, $user['id'])) {
            $this->enrollments->create([
                'course_id' => $courseId,
                'user_id' => $user['id'],
                'role' => 'learner',
                'enrolled_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
            $this->feed->publish($user['id'], 'enrolled', 'course', (string) $courseId, 'school');
        }

        return new Response('', 302, ['Location' => '/courses/' . $courseId]);
    }

    public function completeLesson(Request $request, int $lessonId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        $existing = $this->progress->findForLessonUser($lessonId, $user['id']);
        if ($existing) {
            $this->progress->update($existing['id'], [
                'status' => 'completed',
                'progress_percent' => 100,
                'last_accessed_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
        } else {
            $this->progress->create([
                'lesson_id' => $lessonId,
                'user_id' => $user['id'],
                'status' => 'completed',
                'progress_percent' => 100,
                'last_accessed_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
        }
        $this->feed->publish($user['id'], 'completed', 'lesson', (string) $lessonId, 'school');

        return new Response('', 302, ['Location' => $request->input('return_to', '/dashboard')]);
    }
}
