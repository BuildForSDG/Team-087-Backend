<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $is_patient = $faker->boolean;

    return [
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2b$10$SmaYmzxefwKVyC3ZJ9j/teM2fUfIPWtw51ptLXRiyEDWbxWoPQOdW', //markspencer
        'photo' => $faker->imageUrl(150, 120),
        'is_patient' => $is_patient,
        'is_specialist' => !$is_patient,
        'is_guest' => true,
        'remember_token' => Str::random(15)
    ];
});
