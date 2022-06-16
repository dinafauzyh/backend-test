<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'fullname' => 'Example Admin',
                'username' => 'example_admin',
                'email' => 'example_admin@mail.com',
                'password' => bcrypt('admin12345'),
                'role' => 'Admin',
            ],
            [
                'fullname' => 'Example Customer',
                'username' => 'example_customer',
                'email' => 'example_customer@mail.com',
                'password' => bcrypt('customer12345'),
                'role' => 'Customer',
            ]
        ]);
    }
}
