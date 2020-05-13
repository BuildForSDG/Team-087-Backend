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
            factory(App\Patient::class)->create([
                'users_id' => $userId, 'card_no' => 'TEMP-90210-' . random_int(1, 255), 'birth_date' => '1970-01-01'
            ]);
        });

        if ($patientUserIds->count() < 3) {
            factory(App\User::class, (3 - $patientUserIds->count()))->create(['is_specialist' => true])->each(function ($user) {
                factory(App\Patient::class)->create([
                    'users_id' => $user->id, 'card_no' => 'TEMP-90210-' . random_int(1, 255), 'birth_date' => '1970-01-01'
                ]);
            });
        }
    }
}
