<?php

namespace Tests\Unit;

use App\Services\AIContentEngine;
use App\Contracts\AITextProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AIContentEngineTest extends TestCase
{
    #[Test]
    public function it_generates_a_content_package_for_fishing_posts(): void
    {
        $provider = new class implements AITextProvider {
            public function name(): string { return 'test'; }
            public function generateText(string $prompt): array { return ['status' => 'success', 'data' => 'Generated test content']; }
            public function generateJson(string $prompt, array $schema = []): array { return ['status' => 'success', 'data' => []]; }
        };
        $engine = new AIContentEngine($provider);

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

    #[Test]
    public function it_does_not_return_fallback_content_when_provider_fails(): void
    {
        $provider = new class implements AITextProvider {
            public function name(): string { return 'failing-test'; }
            public function generateText(string $prompt): array { return ['status' => 'provider_error', 'message' => 'Provider unavailable']; }
            public function generateJson(string $prompt, array $schema = []): array { return ['status' => 'provider_error']; }
        };

        $this->expectException(\RuntimeException::class);
        (new AIContentEngine($provider))->generateContentPackage(['category' => 'Nila']);
    }
}
