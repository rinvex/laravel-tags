<?php

declare(strict_types=1);

use Faker\Generator as Faker;

$factory->define(Rinvex\Pages\Models\Page::class, function (Faker $faker) {
    return ['name' => [$faker->languageCode => $faker->title], 'slug' => $faker->slug];
});
