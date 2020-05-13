<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Specialist;
use App\User;
use Faker\Generator as Faker;

$factory->define(Specialist::class, function (Faker $faker) {
    return [
        'users_id' => $faker->randomElement(User::where('is_specialist', true)->pluck('id')->toArray()),
        'license_no' => 'MH-LNO-19298-' . $faker->randomLetter,
        'licensed_at' => $faker->dateTimeBetween('-10 years'),
        'last_renewed_at' => $faker->dateTimeBetween('-2 years'),
        'expires_at' => '2020-12-31'
    ];
});
