<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'name'=>'DJ KOU',
            'email'=>'kou@example.com',
            'password'=>Hash::make('password'),
        ]);
        User::create([
            'name'=>'RITA',
            'email'=>'rita@example.com',
            'password'=>Hash::make('password'),
        ]);
        User::create([
            'name'=>'YNI',
            'email'=>'yni@example.com',
            'password'=>Hash::make('password'),
        ]);
    }
}
