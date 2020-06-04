<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = App\User::pluck('id');
        if ($users->count() < 15) {
            factory(App\User::class, (15 - $users->count()))->create()->each(function ($user) {
                if ($user->is_patient) {
                    $user->patient()->save(factory(App\Patient::class)->make());
                } else if ($user->is_specialist) {
                    $user->specialist()->save(factory(App\Specialist::class)->make());
                }
            });
        }
    }
}
