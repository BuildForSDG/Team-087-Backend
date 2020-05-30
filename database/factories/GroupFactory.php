<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\User;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    $administrativeUsers = User::where('is_admin', true)->pluck('id')->toArray();
    if (empty($administrativeUsers)) {
        $administrativeUsers[] = factory(User::class)->create(['is_admin' => true])['id'];
    }

    return [
        'name' => "group-{$faker->randomLetter}-{$faker->randomElement(range(120, 999990))}",
        'user_id' => $faker->randomElement($administrativeUsers),
    ];
});
