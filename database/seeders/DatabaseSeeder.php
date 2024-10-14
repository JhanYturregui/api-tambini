<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::factory()
            ->count(10)
            ->hasPosts(1)
            ->create();
        User::factory()
            ->count(5)
            ->hasPosts(15)
            ->create();
        User::factory()
            ->count(3)
            ->hasPosts(27)
            ->create();
    }
}
