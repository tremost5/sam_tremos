<?php

namespace App\Contracts;

interface AITextProvider
{
    public function name(): string;

    public function generateText(string $prompt): array;

    public function generateJson(string $prompt, array $schema = []): array;
}
