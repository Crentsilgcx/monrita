<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CareerRepository;
use App\Repositories\CareerSkillRepository;
use App\Repositories\CareerSubjectRepository;
use App\Repositories\LearnerSkillRepository;
use App\Repositories\RecommendationRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\ProgramRepository;

final class CareerRecommendationService
{
    public function __construct(
        private CareerRepository $careers,
        private CareerSkillRepository $careerSkills,
        private CareerSubjectRepository $careerSubjects,
        private LearnerSkillRepository $learnerSkills,
        private RecommendationRepository $recommendations,
        private SubjectRepository $subjects,
        private ProgramRepository $programs,
    ) {
    }

    public function generateForLearner(int $learnerId, array $assessmentScores, array $subjectInterests): array
    {
        $skills = $this->learnerSkills->forLearner($learnerId);
        $skillMap = [];
        foreach ($skills as $skill) {
            $skillMap[$skill['skill_id']] = (int) $skill['level'];
        }

        $careerRankings = [];
        foreach ($this->careers->all() as $career) {
            $score = 0.0;
            foreach ($this->careerSkills->forCareer($career['id']) as $link) {
                $level = $skillMap[$link['skill_id']] ?? 0;
                $score += $level * (float) $link['weight'];
            }
            foreach ($this->careerSubjects->forCareer($career['id']) as $link) {
                $interest = $subjectInterests[$link['subject_id']] ?? 0;
                $score += $interest * (float) $link['weight'];
            }
            $score += $assessmentScores[$career['id']] ?? 0;
            $careerRankings[] = [
                'career' => $career,
                'score' => $score,
            ];
        }

        usort($careerRankings, static fn (array $a, array $b): int => $b['score'] <=> $a['score']);
        $top = array_slice($careerRankings, 0, 3);
        $programSuggestions = [];
        foreach ($top as $entry) {
            $programSuggestions[$entry['career']['id']] = array_slice($this->programs->all(), 0, 3);
        }

        $snapshot = [
            'generated_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'career_rankings' => $top,
            'program_suggestions' => $programSuggestions,
        ];

        $existing = $this->recommendations->findForLearner($learnerId);
        if ($existing) {
            $this->recommendations->update($existing['id'], [
                'snapshot_json' => $snapshot,
                'generated_at' => $snapshot['generated_at'],
            ]);
        } else {
            $this->recommendations->create([
                'learner_id' => $learnerId,
                'snapshot_json' => $snapshot,
                'generated_at' => $snapshot['generated_at'],
            ]);
        }

        return $snapshot;
    }
}
