<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Patient;
use App\User;
use Faker\Generator as Faker;

$factory->define(Patient::class, function (Faker $faker) {
    return [
        'users_id' => $faker->randomElement(User::where('is_patient', true)->pluck('id')->toArray()),
        'card_no' => 'TEMP-90210-' . $faker->randomDigit,
        'birth_date' => $faker->dateTimeBetween('-' . random_int(15, 80) . ' years')->format(('Y-m-d'))
    ];
});
