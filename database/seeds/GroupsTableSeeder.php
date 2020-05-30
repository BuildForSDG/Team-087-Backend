<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = App\Group::pluck('id');
        if ($groups->count() < 5) {
            factory(App\Group::class, (5 - $groups->count()))->create();
        }
    }
}
