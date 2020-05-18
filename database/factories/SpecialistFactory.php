<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Specialist;
use App\User;
use Faker\Generator as Faker;

$factory->define(Specialist::class, function (Faker $faker) {
    return [
        'users_id' => $faker->randomElement(User::where('is_specialist', true)->pluck('id')->toArray()),
        'license_no' => 'MH-LNO-19298-' . random_int(0, 255),
        'licensed_at' => $faker->dateTimeBetween('-10 years'), //'2009-01-01'
        'last_renewed_at' => $faker->dateTimeBetween('-2 years'), //'2019-01-01'
        'is_verified' => $faker->boolean,
        'expires_at' => '2020-12-31'
    ];
});
