<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tag;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition()
    {
        return [
            // dev: simple tag names
            'name' => $this->faker->unique()->word()
        ];
    }
}
