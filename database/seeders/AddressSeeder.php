<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addresses')->insert([
            [
                'title' => 'Rumah',
                'province' => 'Jawa Barat',
                'city' => 'Cimahi',
                'district' => 'Cimahi Selatan',
                'address' => 'Jl. Cimahi Selatan No. 1',
                'postal_code' => 40132,
                'is_default' => true,
                'user_id' => 2,
            ],
            [
                'title' => 'Kantor',
                'province' => 'Jawa Barat',
                'city' => 'Bandung',
                'district' => 'Cipayung',
                'address' => 'Jl. Cipayung No. 1',
                'postal_code' => 40137,
                'is_default' => false,
                'user_id' => 2,
            ]
        ]);
    }
}
