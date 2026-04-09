<?php

namespace App\Support;

use App\Models\User;

class MatchService
{
    public function score(User $viewer, User $candidate): int
    {
        $viewerTeach = collect($viewer->teach_skills ?? [])->map(fn ($skill) => mb_strtolower($skill));
        $viewerLearn = collect($viewer->learn_skills ?? [])->map(fn ($skill) => mb_strtolower($skill));
        $candidateTeach = collect($candidate->teach_skills ?? [])->map(fn ($skill) => mb_strtolower($skill));
        $candidateLearn = collect($candidate->learn_skills ?? [])->map(fn ($skill) => mb_strtolower($skill));

        $score = 0;

        if ($viewerLearn->intersect($candidateTeach)->isNotEmpty()) {
            $score += 25;
        }

        if ($viewerTeach->intersect($candidateLearn)->isNotEmpty()) {
            $score += 25;
        }

        if (
            $viewerLearn->intersect($candidateTeach)->isNotEmpty() &&
            $viewerTeach->intersect($candidateLearn)->isNotEmpty()
        ) {
            $score += 20;
        }

        if ($viewer->timezone && $viewer->timezone === $candidate->timezone) {
            $score += 10;
        }

        if ($viewer->availability && $viewer->availability === $candidate->availability) {
            $score += 10;
        }

        if ($viewer->skill_level && $viewer->skill_level === $candidate->skill_level) {
            $score += 5;
        }

        $formatOverlap = collect($viewer->formats ?? [])
            ->map(fn ($format) => mb_strtolower($format))
            ->intersect(collect($candidate->formats ?? [])->map(fn ($format) => mb_strtolower($format)))
            ->count();

        if ($formatOverlap > 0) {
            $score += min(5, $formatOverlap * 2);
        }

        return min(100, $score);
    }
}
