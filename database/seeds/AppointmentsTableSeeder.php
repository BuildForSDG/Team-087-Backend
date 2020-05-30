<?php

use Illuminate\Database\Seeder;

class AppointmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointments = App\Appointment::pluck('id');
        if ($appointments->count() < 10) {
            factory(App\Appointment::class, (10 - $appointments->count()))->create();
        }
    }
}
