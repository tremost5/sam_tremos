<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Sam Tremos',
                'role' => 'admin',
                'password' => bcrypt('password123'),
            ]
        );

        $this->call(CategorySeeder::class);
        $this->call(AiPromptTemplateSeeder::class);
    }
}
