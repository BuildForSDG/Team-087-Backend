<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class, PatientsTableSeeder::class, SpecialistsTableSeeder::class,
            AppointmentsTableSeeder::class, GroupsTableSeeder::class, VisitsTableSeeder::class
        ]);
    }
}
