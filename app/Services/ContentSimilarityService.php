<?php

namespace App\Services;

class ContentSimilarityService
{
    public function similarityPercentage(string $left, string $right): float
    {
        $leftNormalized = $this->normalize($left);
        $rightNormalized = $this->normalize($right);

        if ($leftNormalized === '' || $rightNormalized === '') {
            return 0.0;
        }

        similar_text($leftNormalized, $rightNormalized, $percent);
        $wordOverlap = $this->wordOverlapPercentage($leftNormalized, $rightNormalized);

        return (float) max($percent, $wordOverlap);
    }

    public function isDuplicate(string $candidate, array $existing): bool
    {
        foreach ($existing as $item) {
            if ($this->similarityPercentage($candidate, (string) $item) >= 80.0) {
                return true;
            }
        }

        return false;
    }

    protected function normalize(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('/[^\pL\pN\s]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? $value;

        return $value;
    }

    protected function wordOverlapPercentage(string $left, string $right): float
    {
        $leftWords = array_filter(preg_split('/\s+/', $left) ?: [], 'strlen');
        $rightWords = array_filter(preg_split('/\s+/', $right) ?: [], 'strlen');

        if ($leftWords === [] || $rightWords === []) {
            return 0.0;
        }

        $leftSet = array_unique($leftWords);
        $rightSet = array_unique($rightWords);
        $intersection = count(array_intersect($leftSet, $rightSet));
        $union = count(array_unique(array_merge($leftSet, $rightSet)));

        if ($union === 0) {
            return 0.0;
        }

        return ($intersection / $union) * 100;
    }
}
