<?php

namespace App\Contracts;

interface AIImageProvider
{
    public function name(): string;

    public function generate(string $prompt, array $options = []): array;
}
