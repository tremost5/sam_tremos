<?php

namespace App\Console\Commands;

use App\Services\AutopilotService;
use Illuminate\Console\Command;

class AutopilotGenerateCommand extends Command
{
    protected $signature = 'autopilot:generate {--user-id=}';

    protected $description = 'Generate content inventory based on autopilot configuration';

    public function handle(AutopilotService $service): int
    {
        $userId = $this->option('user-id');
        if ($userId) {
            $user = \App\Models\User::find($userId);
            if (! $user) {
                $this->error('User not found.');

                return self::FAILURE;
            }

            $result = $service->runForUser($user);
            $this->info('Generated '.$result['generated'].' posts for user '.$user->id.'.');

            return self::SUCCESS;
        }

        $count = $service->runDue();
        $this->info('Generated '.$count.' posts across all enabled autopilot users.');

        return self::SUCCESS;
    }
}
