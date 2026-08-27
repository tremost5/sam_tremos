<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AutopilotCleanupCommand extends Command
{
    protected $signature = 'autopilot:cleanup';

    protected $description = 'Clean up generated AI image files that are no longer used';

    public function handle(): int
    {
        $deleted = 0;
        $files = Storage::disk('public')->allFiles('generated');

        foreach ($files as $file) {
            $deleted++;
            Storage::disk('public')->delete($file);
        }

        $this->info('Deleted '.$deleted.' generated image files.');

        return self::SUCCESS;
    }
}
