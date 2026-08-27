<?php

namespace Tests\Unit;

use App\Services\ContentQualityService;
use App\Services\ContentSimilarityService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContentQualityAndSimilarityTest extends TestCase
{
    #[Test]
    public function it_scores_fishing_content_between_zero_and_hundred(): void
    {
        $service = new ContentQualityService();

        $score = $service->score([
            'title' => 'Tips umpan nila pagi hari',
            'idea' => 'Pola umpan yang cocok saat pagi hari',
            'caption' => 'Hari ini saya mau share pengalaman mancing di pagi hari. Saat cuaca masih sejuk, umpan pilihan seperti pelet dan cacing sering lebih efektif.',
            'hashtags' => '#Nila #Mancing #SamTremos',
        ]);

        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
        $this->assertGreaterThan(70, $score);
    }

    #[Test]
    public function it_detects_duplicate_content_above_threshold(): void
    {
        $service = new ContentSimilarityService();

        $left = 'Tips umpan nila pagi hari di bendungan, fokus pada pola dan timing agar ikan mau makan.';
        $right = 'Tips umpan nila pagi hari di bendungan, fokus pada pola dan timing agar ikan mau makan banget.';

        $this->assertGreaterThanOrEqual(80, $service->similarityPercentage($left, $right));
        $this->assertTrue($service->isDuplicate($left, [$right]));
    }
}
