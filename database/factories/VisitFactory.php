<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Appointment;
use App\Visit;
use Faker\Generator as Faker;

$factory->define(Visit::class, function (Faker $faker) {
    $approvedAppointmentIds = Appointment::where('status', 'approved')->pluck('id')->toArray();
    if (empty($approvedAppointmentIds)) {
        $approvedAppointmentIds[] = factory(Appointment::class)->create(['status' => 'approved'])['id'];
    }

    return [
        'appointment_id' => $faker->randomElement($approvedAppointmentIds),
        'temperature' => "35.9",
        'blood_pressure' => "35.9",
        'height' => "198.00",
        'visuals' => "Perfect"
    ];
});
