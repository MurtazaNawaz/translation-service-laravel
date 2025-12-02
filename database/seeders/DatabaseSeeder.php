<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Locale;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // dev: create test user only if not exists
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
            ]
        );

        // dev: create sample locales
        $locales = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'de', 'name' => 'German'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'pt', 'name' => 'Portuguese'],
            ['code' => 'ru', 'name' => 'Russian'],
            ['code' => 'ja', 'name' => 'Japanese'],
            ['code' => 'ko', 'name' => 'Korean'],
            ['code' => 'ar', 'name' => 'Arabic'],
        ];

        foreach ($locales as $locale) {
            Locale::firstOrCreate(
                ['code' => $locale['code']],
                ['name' => $locale['name']]
            );
        }

        // dev: create sample tags
        $tags = ['mobile', 'desktop', 'web', 'api', 'frontend', 'backend', 'admin', 'user', 'system', 'test'];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        // dev: create 100k+ translations in batches via bulk insert + attach pivot entries
        $faker = Faker::create();
        $batchSize = 5000; // dev safe batch
        $batches = 20;     // 20 * 5000 = 100k

        $localeIds = Locale::pluck('id')->toArray();
        $tagIds = Tag::pluck('id')->toArray();

        $globalCounter = DB::table('translations')->max('id') ?? 0;
        // If keys are 'key_1'... we'll compute keys using a counter to keep them unique
        $keyCounter = DB::table('translations')->count() ? DB::table('translations')->count() + 1 : 1;

        for ($b = 0; $b < $batches; $b++) {
            $rows = [];
            $keys = [];
            $now = now();

            for ($i = 0; $i < $batchSize; $i++) {
                $k = 'key_' . $keyCounter++;
                $rows[] = [
                    'key' => $k,
                    'value' => $faker->sentence(),
                    'locale_id' => $localeIds[array_rand($localeIds)],
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $keys[] = $k;
            }

            // bulk insert
            DB::table('translations')->insert($rows);

            // fetch inserted ids
            $inserted = DB::table('translations')->whereIn('key', $keys)->pluck('id')->toArray();

            // attach random tags in pivot table
            $pivotRows = [];
            foreach ($inserted as $tid) {
                // each translation get 1-3 tags randomly
                $countTags = rand(1, 3);
                $chosen = (array) array_rand(array_flip($tagIds), $countTags);
                foreach ($chosen as $tagId) {
                    $pivotRows[] = [
                        'translation_id' => $tid,
                        'tag_id' => $tagId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($pivotRows)) {
                DB::table('tag_translation')->insert($pivotRows);
            }

            echo "Batch " . ($b + 1) . " done\n";
        }
    }
}
