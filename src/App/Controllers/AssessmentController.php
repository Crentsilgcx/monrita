<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\AssessmentAttemptRepository;
use App\Repositories\AssessmentQuestionRepository;
use App\Repositories\AssessmentRepository;
use App\Repositories\AssessmentResponseRepository;
use App\Repositories\LearnerRepository;
use App\Services\CareerRecommendationService;
use App\Services\FeedService;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\View;

final class AssessmentController
{
    public function __construct(
        private View $view,
        private AssessmentRepository $assessments,
        private AssessmentQuestionRepository $questions,
        private AssessmentAttemptRepository $attempts,
        private AssessmentResponseRepository $responses,
        private LearnerRepository $learners,
        private CareerRecommendationService $careerRecommendations,
        private Auth $auth,
        private Csrf $csrf,
        private FeedService $feed,
    ) {
    }

    public function index(): Response
    {
        $content = $this->view->render('pages/assessments/index', [
            'assessments' => $this->assessments->active(),
        ]);

        return new Response($content);
    }

    public function take(int $assessmentId): Response
    {
        $assessment = $this->assessments->find($assessmentId);
        if (!$assessment) {
            return new Response('Assessment not found', 404);
        }
        $content = $this->view->render('pages/assessments/take', [
            'assessment' => $assessment,
            'questions' => $this->questions->forAssessment($assessmentId),
            'csrf' => $this->csrf->token(),
        ]);

        return new Response($content);
    }

    public function submit(Request $request, int $assessmentId): Response
    {
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $user = $this->auth->user();
        if (!$user) {
            return new Response('', 302, ['Location' => '/login']);
        }
        $learner = $this->learners->findByUserId($user['id']);
        if (!$learner) {
            return new Response('Learner profile missing', 400);
        }
        $attempt = $this->attempts->create([
            'learner_id' => $learner['id'],
            'assessment_id' => $assessmentId,
            'started_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'completed_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
        $total = 0.0;
        $questions = $this->questions->forAssessment($assessmentId);
        foreach ($questions as $question) {
            $value = (int) $request->input('question_' . $question['id'], 0);
            $score = $value * (float) ($question['weight'] ?? 1);
            $total += $score;
            $this->responses->create([
                'attempt_id' => $attempt['id'],
                'question_id' => $question['id'],
                'answer_json' => ['value' => $value],
                'score' => $score,
            ]);
        }
        $attempt = $this->attempts->update($attempt['id'], [
            'score' => $total,
            'result_json' => ['total' => $total],
        ]);

        $this->feed->publish($user['id'], 'completed', 'assessment', (string) $assessmentId, 'school');
        $this->careerRecommendations->generateForLearner($learner['id'], [$assessmentId => $total], []);

        return new Response('', 302, ['Location' => '/assessments/' . $assessmentId . '/result?attempt=' . $attempt['id']]);
    }

    public function result(Request $request, int $assessmentId): Response
    {
        $attemptId = (int) $request->input('attempt');
        $attempt = $this->attempts->find($attemptId);
        if (!$attempt) {
            return new Response('Attempt not found', 404);
        }
        $assessment = $this->assessments->find($assessmentId);
        $content = $this->view->render('pages/assessments/result', [
            'assessment' => $assessment,
            'attempt' => $attempt,
            'responses' => $this->responses->forAttempt($attemptId),
        ]);

        return new Response($content);
    }
}
