<?php

use Illuminate\Database\Seeder;

class PatientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $patientUserIds = App\User::where('is_patient', true)->pluck('id');
        $patientUserIds->each(function ($userId) {
            factory(App\Patient::class)->create(['user_id' => $userId]);
        });

        if ($patientUserIds->count() < 3) {
            factory(App\User::class, (3 - $patientUserIds->count()))->create(['is_patient' => true])->each(function ($user) {
                factory(App\Patient::class)->create(['user_id' => $user->id]);
            });
        }
    }
}
