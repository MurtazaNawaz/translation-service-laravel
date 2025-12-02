<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Locale;

class LocaleFactory extends Factory
{
    protected $model = Locale::class;

    public function definition()
    {
        static $counter = 1; // dev: make code unique

        return [
            'code' => 'lc' . $counter, // dev: unique code like lc1, lc2
            'name' => $this->faker->unique()->word()
        ];
    }
}
