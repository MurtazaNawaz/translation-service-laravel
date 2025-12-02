<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Translation;
use App\Models\Locale;
use App\Models\Tag;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        static $counter = 1; // dev: ensure unique keys

        return [
            'key' => 'key_' . $counter++, // dev: unique key like key_1, key_2 ...
            'value' => $this->faker->sentence(),
            'locale_id' => Locale::inRandomOrder()->first()?->id ?? 1,
            // no tag_id here — we use pivot table to attach tags in seeder
        ];
    }
}
