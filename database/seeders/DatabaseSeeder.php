<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::insert([[
            'name' => 'Vivek Joshi',
            'email' => 'vrj022@gmail.com',
            'password' => Hash::make('vivekjoshi'),
            'is_admin' => 1
        ], [
            'name' => 'Jimmy Smith',
            'email' => 'js@outlook.com',
            'password' => Hash::make('jsmith'),
            'is_admin' => 0
        ]]);
    }
}
