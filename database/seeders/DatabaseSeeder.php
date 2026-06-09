<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            KelurahanPosyanduSeeder::class,
            UserAdminSeeder::class,
            KategoriSeeder::class,
            UserKaderSeeder::class,
            UserNakesSeeder::class,
        ]);
    }
}
