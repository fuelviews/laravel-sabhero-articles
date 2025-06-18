<?php

namespace Fuelviews\SabHeroArticle\Database\Factories;

use Fuelviews\SabHeroArticle\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug(),
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'image' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'registerMediaConversionsUsingModelInstance' => $this->faker->boolean(),
        ];
    }
}
