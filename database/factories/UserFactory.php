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
    $isPatient = $faker->boolean;
    $minimumAge = $isPatient ? 15 : 35;
    $emailAddress = $faker->unique()->safeEmail;

    return [
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'gender' => $faker->randomElement(['male', 'female']),
        'birth_date' => $faker->dateTimeBetween('-' . random_int($minimumAge, 80) . ' years')->format(('Y-m-d')), //'1970-01-01'
        'email' => $emailAddress,
        'password' => 'markspencer',
        //'photo' => $faker->imageUrl(150, 120),
        'marital_status' => $faker->randomElement(['single', 'married', 'divorced', 'complicated']),
        'is_patient' => $isPatient,
        'is_specialist' => !$isPatient,
        'profile_code' => hash('sha512', $emailAddress),
        'remember_token' => Str::random(15)
    ];
});
