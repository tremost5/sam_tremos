<?php

namespace Database\Seeders;

use App\Models\AiPromptTemplate;
use Illuminate\Database\Seeder;

class AiPromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'generate idea',
                'type' => 'idea',
                'prompt' => 'Buatkan 5 ide konten Facebook tentang mancing, fishing lifestyle, danau, bendungan, tegek, nila, mujair, umpan, dan rekreasi. Gunakan gaya natural, santai, seperti pemancing sungguhan, tidak terlalu formal, tidak terlalu banyak emoji, dan mendorong komentar. Bahasa Indonesia.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'generate caption',
                'type' => 'caption',
                'prompt' => 'Tulis caption Facebook untuk konten fishing. Gaya natural, santai, seperti pemancing sungguhan. Hindari terlalu formal, tidak terasa seperti AI, dan hindari clickbait berlebihan. Jangan lebih dari 3 paragraf. Sertakan cara atau pengalaman yang relevan dengan niche mancing.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'generate hashtag',
                'type' => 'hashtag',
                'prompt' => 'Buat 5-12 hashtag relevan untuk konten mancing Indonesia. Fokus pada nila, mujair, tegek, danau, bendungan, umpan, fishing lifestyle, alam, rekreasi, dan komunitas pemancing. Gunakan format #tag tanpa spasi dan hindari generic yang terlalu luas.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'generate engagement',
                'type' => 'engagement',
                'prompt' => 'Buat pertanyaan engagement yang sederhana, relevan dengan komunitas pemancing, dan mendorong komentar. Pakai gaya natural dan tidak terlalu formal. Fokus pengalaman mancing di alam, umpan, teknik, cuaca, atau momen lucu.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'generate image prompt',
                'type' => 'image',
                'prompt' => 'Buat prompt gambar untuk ilustrasi fotografi mancing di Indonesia. Visual harus menampilkan lanskap alam, bendungan, danau, tegek, joran, pancing, ikan nila atau mujair, suasana sunrise/sunset, natural lighting, realism, detail, warna natural, cinematic composition.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'validate content',
                'type' => 'validation',
                'prompt' => 'Validasi konten yang dihasilkan untuk niche mancing Indonesia. Pastikan relevan dengan fishing lifestyle, tidak clickbait berlebihan, mudah dibaca, dan cocok untuk Facebook. Beri rekomendasi singkat jika perlu diperbaiki.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'calculate quality',
                'type' => 'quality',
                'prompt' => 'Hitung quality score 0-100 untuk konten Facebook niche mancing berdasarkan originality, relevance, readability, engagement potential, dan visual relevance. Berikan skor saja.',
                'version' => 'v1',
                'is_active' => true,
            ],
            [
                'name' => 'rewrite content',
                'type' => 'rewrite',
                'prompt' => 'Tulis ulang konten ini dengan gaya lebih natural, lebih manusiawi, dan lebih relevan untuk pemancing Indonesia. Jaga inti pesan, lebih santai, lebih enak dibaca, dan hindari terdengar seperti AI.',
                'version' => 'v1',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            AiPromptTemplate::query()->updateOrCreate(
                ['name' => $template['name'], 'type' => $template['type']],
                $template
            );
        }
    }
}
