<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Appointment;
use App\Patient;
use App\Specialist;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Appointment::class, function (Faker $faker) {
    $daysFromNow = random_int(7, 15);
    $startsAt = $faker->dateTimeInInterval('now', "+{$daysFromNow} days", 'Africa/Lagos');

    return [
        'specialist_id' => $faker->randomElement(Specialist::pluck('user_id')->toArray()),
        'patient_id' => $faker->randomElement(Patient::pluck('user_id')->toArray()),
        'purpose' => "my belle o, my {$faker->randomElement(['head', 'neck'])} o",
        'starts_at' => $startsAt,
        'ends_at' => Carbon::parse($startsAt)->addMinutes(15)->toDateTime(),
        'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'cancelled'])
    ];
});
