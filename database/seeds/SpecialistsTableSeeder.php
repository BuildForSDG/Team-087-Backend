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
            factory(App\Specialist::class)->create(['user_id' => $userId]);
        });

        if ($specialistUserIds->count() < 3) {
            factory(App\User::class, (3 - $specialistUserIds->count()))->create(['is_specialist' => true])->each(function ($user) {
                factory(App\Specialist::class)->create(['user_id' => $user->id]);
            });
        }
    }
}
