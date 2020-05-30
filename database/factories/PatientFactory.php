<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Patient;
use App\User;
use Faker\Generator as Faker;

$factory->define(Patient::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomElement(User::where('is_patient', true)->pluck('id')->toArray()),
        'card_no' => 'TEMP-90210-' . random_int(1, 255),
        'blood_group' => 'O+',
        'genotype' => 'AA',
        'eye_colour' => $faker->randomElement(['brown', 'black', 'blue', 'red']),
        'skin_colour' => $faker->randomElement(['brown', 'black']),
    ];
});
