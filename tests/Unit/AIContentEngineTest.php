<?php

namespace Tests\Unit;

use App\Services\AIContentEngine;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AIContentEngineTest extends TestCase
{
    #[Test]
    public function it_generates_a_content_package_for_fishing_posts(): void
    {
        $engine = new AIContentEngine();

        $content = $engine->generateContentPackage([
            'category' => 'Nila',
            'idea' => 'Tips umpan nila pagi hari',
        ]);

        $this->assertSame('Nila', $content['category']);
        $this->assertNotEmpty($content['title']);
        $this->assertNotEmpty($content['caption']);
        $this->assertNotEmpty($content['hashtags']);
        $this->assertNotEmpty($content['engagement_question']);
        $this->assertNotEmpty($content['image_prompt']);
        $this->assertGreaterThanOrEqual(0, $content['quality_score']);
        $this->assertLessThanOrEqual(100, $content['quality_score']);
    }
}
