<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Tips Mancing',
            'Trik Mancing',
            'Mancing Tegek',
            'Umpan',
            'Nila',
            'Mujair',
            'Ikan Air Tawar',
            'Danau',
            'Bendungan',
            'Alam',
            'Rekreasi',
            'Cerita Pemancing',
            'Humor Mancing',
            'Motivasi',
            'Fakta Ikan',
            'Engagement',
            'Polling',
            'Fishing Lifestyle',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => str($category)->slug()->toString()],
                [
                    'name' => $category,
                    'description' => 'Kategori otomatis untuk niche fishing.',
                    'is_active' => true,
                ]
            );
        }
    }
}
