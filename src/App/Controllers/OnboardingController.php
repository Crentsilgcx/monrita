<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\LearnerAttributeRepository;
use App\Repositories\LearnerRepository;
use App\Services\CareerRecommendationService;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\View;

final class OnboardingController
{
    private const STEPS = ['identity', 'profile', 'interests'];

    public function __construct(
        private View $view,
        private Auth $auth,
        private LearnerRepository $learners,
        private LearnerAttributeRepository $attributes,
        private CareerRecommendationService $careerRecommendations,
        private Csrf $csrf,
    ) {
    }

    public function show(): Response
    {
        $learner = $this->learner();
        $step = $learner['onboarding_step'] ?? self::STEPS[0];
        $content = $this->view->render('pages/onboarding', [
            'step' => $step,
            'csrf' => $this->csrf->token(),
            'learner' => $learner,
        ]);

        return new Response($content);
    }

    public function submit(Request $request): Response
    {
        $learner = $this->learner();
        if (!$this->csrf->validate($request->input('_token'))) {
            return new Response('Invalid CSRF token', 419);
        }
        $step = $learner['onboarding_step'] ?? self::STEPS[0];

        if ($step === 'identity') {
            $dob = (string) $request->input('date_of_birth');
            $consent = $request->input('consent') === 'on';
            if ($dob === '' || !$consent) {
                return $this->redirectWithError('Please provide your date of birth and guardian consent.');
            }
            $this->learners->update($learner['id'], [
                'date_of_birth' => $dob,
                'guardian_consent' => $consent,
                'onboarding_step' => 'profile',
            ]);
        } elseif ($step === 'profile') {
            $district = trim((string) $request->input('district'));
            $region = trim((string) $request->input('region'));
            $level = (string) $request->input('jhs_level');
            if ($district === '' || $region === '' || !in_array($level, ['JHS1', 'JHS2', 'JHS3'], true)) {
                return $this->redirectWithError('Complete your location and class details.');
            }
            $this->learners->update($learner['id'], [
                'district' => $district,
                'region' => $region,
                'jhs_level' => $level,
                'onboarding_step' => 'interests',
            ]);
        } else {
            $subjects = (array) $request->input('subjects');
            $interests = [];
            foreach ($subjects as $subjectId => $value) {
                $score = max(0, min(5, (int) $value));
                $interests[$subjectId] = $score;
                $this->attributes->create([
                    'learner_id' => $learner['id'],
                    'key' => 'subject_interest_' . $subjectId,
                    'value' => $score,
                    'recorded_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
                ]);
            }

            $this->learners->update($learner['id'], [
                'onboarding_completed_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
                'onboarding_step' => 'complete',
            ]);

            $this->careerRecommendations->generateForLearner(
                $learner['id'],
                [],
                $interests
            );

            return new Response('', 302, ['Location' => '/dashboard']);
        }

        return new Response('', 302, ['Location' => '/onboarding']);
    }

    private function learner(): array
    {
        $user = $this->auth->user();
        if (!$user) {
            throw new \RuntimeException('Unauthenticated');
        }

        $learner = $this->learners->findByUserId($user['id']);
        if (!$learner) {
            throw new \RuntimeException('Learner profile missing');
        }

        return $learner;
    }

    private function redirectWithError(string $message): Response
    {
        return new Response(
            $this->view->render('pages/onboarding', [
                'step' => 'identity',
                'csrf' => $this->csrf->token(),
                'learner' => $this->learner(),
                'error' => $message,
            ]),
            422
        );
    }
}
