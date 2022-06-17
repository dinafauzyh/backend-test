<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpeditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expeditions')->insert([
            [
                'name' => 'JNE',
            ],
            [
                'name' => 'J&T',
            ],
            [
                'name' => 'Sicepat',
            ],
        ]);
    }
}
