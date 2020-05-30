<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Patient;
use App\Review;
use App\Specialist;
use Faker\Generator as Faker;

$factory->define(Review::class, function (Faker $faker) {
    return [
        'specialist_id' => $faker->randomElement(Specialist::pluck('users_id')->toArray()),
        'patient_id' => $faker->randomElement(Patient::pluck('users_id')->toArray()),
        'remark' => 'You are doing well.... Ooin',
        'rating' => '2.0'
    ];
});
