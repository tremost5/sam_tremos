<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

try {
    $user = User::firstOrNew(['email' => 'demo@local']);
    $user->name = 'Demo User';
    $user->password = bcrypt('secret');
    $user->save();
    echo "created\n";
} catch (\Exception $e) {
    echo "error: " . $e->getMessage() . "\n";
}
