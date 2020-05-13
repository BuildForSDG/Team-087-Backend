<?php

use Illuminate\Database\Seeder;

class SpecialistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialistUserIds = App\User::where('is_specialist', true)->pluck('id');
        $specialistUserIds->each(function ($userId) {
            factory(App\Specialist::class)->create([
                'users_id' => $userId, 'license_no' => 'MH-LNO-19298-' . random_int(0, 255),
                'licensed_at' => '2009-01-01', 'last_renewed_at' => '2019-01-01', 'expires_at' => '2020-12-31'
            ]);
        });

        if ($specialistUserIds->count() < 3) {
            factory(App\User::class, (3 - $specialistUserIds->count()))->create(['is_specialist' => true])->each(function ($user) {
                factory(App\Specialist::class)->create([
                    'users_id' => $user->id, 'license_no' => 'MH-LNO-19298-' . random_int(0, 255),
                    'licensed_at' => '2009-01-01', 'last_renewed_at' => '2019-01-01', 'expires_at' => '2020-12-31'
                ]);
            });
        }
    }
}
