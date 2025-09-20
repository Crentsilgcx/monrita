<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AssessmentAttemptRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\LessonProgressRepository;
use App\Repositories\LearnerRepository;

final class AnalyticsService
{
    public function __construct(
        private LearnerRepository $learners,
        private AssessmentAttemptRepository $attempts,
        private LessonProgressRepository $progress,
        private EnrollmentRepository $enrollments,
    ) {
    }

    public function metrics(): array
    {
        $learnerCount = count(array_filter($this->learners->all(), static fn (array $learner): bool => ($learner['onboarding_completed_at'] ?? null) !== null));
        $attempts = $this->attempts->all();
        $completedAttempts = array_filter($attempts, static fn (array $attempt): bool => ($attempt['completed_at'] ?? null) !== null);
        $progress = $this->progress->all();
        $completedLessons = array_filter($progress, static fn (array $progress): bool => ($progress['status'] ?? '') === 'completed');

        return [
            'active_learners' => $learnerCount,
            'assessment_completion_rate' => $attempts === [] ? 0 : round(count($completedAttempts) / count($attempts) * 100),
            'course_completion_events' => count($completedLessons),
            'enrollment_total' => count($this->enrollments->all()),
        ];
    }
}
