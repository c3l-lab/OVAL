<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(oval\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});



$factory->define(oval\Video::class, function(Faker\Generator $faker) {
	return [
		'identifier' => str_random(10),
		'title' =>  $faker->optional()->sentence,
		'description' =>  $faker->optional()->sentence,
		'duration' => $faker->randomNumber,
		'thumbnail_url' => $faker->url,
		'media_type' => 'helix',
		'point_one' => $faker->optional()->sentence,
		'point_two' => $faker->optional()->sentence,
		'point_three' => $faker->optional()->sentence,
		'added_by' => function() {
			return factory(oval\User::class)->create()->id;
		}
	];
});

$factory->define(oval\Course::class, function(Faker\Generator $faker) {
	return [
		'name' => $faker->sentence
	];
});

$factory->define(oval\Group::class, function(Faker\Generator $faker) {
	return [
		'name' => $faker->word,
		'course_id' => function() {
			return factory(oval\Course::class)->create()->id;
		}
	];
});

$factory->define(oval\Annotation::class, function(Faker\Generator $faker) {
	return [
		'video_id' =>function() {
			return factory(oval\Video::class)->create()->id;
		},
		'user_id' => function() {
			return factory(oval\User::class)->create()->id;
		},
		'start_time' => $faker->randomNumber,
		'description' => $faker->sentence,
		'tags' => $faker->optional()->word,
		'is_private' => $faker->boolean($chanceOfGettingTrue = 20),
	];
});

$factory->define(oval\Comment::class, function(Faker\Generator $faker) {
	return [
		'video_id' =>function() {
			return factory(oval\Video::class)->create()->id;
		},
		'user_id' => function() {
			return factory(oval\User::class)->create()->id;
		},
		'description' => $faker->sentence,
		'tags' => $faker->optional()->word,
		'is_private' => $faker->boolean($chanceOfGettingTrue = 20),
	];
});