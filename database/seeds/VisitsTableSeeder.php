<?php

use Illuminate\Database\Seeder;

class VisitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $totalVisits = App\Visit::pluck('id')->count();
        if ($totalVisits <= 5) {
            factory(App\Visit::class, (5 - $totalVisits))->create();
        }
    }
}
