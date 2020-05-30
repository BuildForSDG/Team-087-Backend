<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Appointment;
use App\Chat;
use App\User;
use Faker\Generator as Faker;

$factory->define(Chat::class, function (Faker $faker) {
    return [
        'appointment_id' => $faker->randomElement(Appointment::where('status', 'pending')->pluck('id')->toArray()),
        'user_id' => $faker->randomElement(User::where('is_admin', false)->pluck('id')->toArray()),
        'message' => 'Hmmm...'
    ];
});
