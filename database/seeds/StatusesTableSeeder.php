<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$users = factory(User::class)->times(50)->make();
        $ids=[1,2,3];
        $faker = app(Faker\Generator::class);
        $statuses = factory(Status::class)->times(100)->make()->each(function($status) use($faker,$ids){
            $status->user_id = $faker->randomElement($ids);
        });
        Status::insert($statuses->toArray());
    }
}
