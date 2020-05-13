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
            factory(App\User::class, (15 - $users->count()))->create();
        }
    }
}
