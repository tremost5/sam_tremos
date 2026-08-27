<?php

namespace App\Services;

class ContentQualityService
{
    public function score(array $content): int
    {
        $title = (string) ($content['title'] ?? '');
        $idea = (string) ($content['idea'] ?? '');
        $caption = (string) ($content['caption'] ?? '');
        $hashtags = (string) ($content['hashtags'] ?? '');

        $originality = $this->scoreOriginality($title, $idea, $caption);
        $relevance = $this->scoreRelevance($title, $idea, $caption, $hashtags);
        $readability = $this->scoreReadability($caption);
        $engagementPotential = $this->scoreEngagementPotential($title, $caption, $hashtags);
        $visualRelevance = $this->scoreVisualRelevance($title, $idea, $caption);

        $weighted = (
            $originality * 0.25 +
            $relevance * 0.25 +
            $readability * 0.2 +
            $engagementPotential * 0.2 +
            $visualRelevance * 0.1
        );

        return max(0, min(100, (int) round($weighted)));
    }

    protected function scoreOriginality(string $title, string $idea, string $caption): float
    {
        $length = strlen(trim($title.' '.$idea.' '.$caption));
        $base = 55 + min(35, $length / 12);

        return max(0, min(100, $base));
    }

    protected function scoreRelevance(string $title, string $idea, string $caption, string $hashtags): float
    {
        $keywords = ['mancing', 'nila', 'mujair', 'tegek', 'umpan', 'bendungan', 'danau', 'alam', 'fishing', 'pemancing'];
        $haystack = strtolower($title.' '.$idea.' '.$caption.' '.$hashtags);
        $matches = 0;

        foreach ($keywords as $keyword) {
            if (str_contains($haystack, strtolower($keyword))) {
                $matches++;
            }
        }

        return max(0, min(100, 50 + ($matches * 8)));
    }

    protected function scoreReadability(string $caption): float
    {
        $text = trim($caption);
        if ($text === '') {
            return 0;
        }

        $words = str_word_count($text);
        $avgLength = $words > 0 ? strlen(str_replace('\n', ' ', $text)) / $words : 0;

        return max(0, min(100, 60 + ($avgLength > 4 ? 15 : 0) + min(20, $words / 20)));
    }

    protected function scoreEngagementPotential(string $title, string $caption, string $hashtags): float
    {
        $text = strtolower($title.' '.$caption.' '.$hashtags);
        $engagementWords = ['siapa', 'kalian', 'apakah', 'cara', 'tips', 'trik', 'mancing', 'comment', 'yang', 'bagaimana'];
        $matches = 0;

        foreach ($engagementWords as $word) {
            if (str_contains($text, $word)) {
                $matches++;
            }
        }

        return max(0, min(100, 50 + ($matches * 6)));
    }

    protected function scoreVisualRelevance(string $title, string $idea, string $caption): float
    {
        $text = strtolower($title.' '.$idea.' '.$caption);
        $visualWords = ['pagi', 'sore', 'danau', 'bendungan', 'alam', 'air', 'ikan', 'umpan', 'tegek', 'sunrise', 'river', 'lake'];
        $matches = 0;

        foreach ($visualWords as $word) {
            if (str_contains($text, $word)) {
                $matches++;
            }
        }

        return max(0, min(100, 45 + ($matches * 8)));
    }
}
