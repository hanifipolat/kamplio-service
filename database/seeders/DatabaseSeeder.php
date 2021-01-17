<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => "Ufuk Polat",
            'username' => "ufuk",
            'email' => "ufuk@admin.com",
            'email_verified_at' => now(),
            'password' => Hash::make('pass1234'),
            'gender'=>'M',
            'remember_token' => Str::random(10),
        ]);
        User::create([
            'name' => "Enes Öncan",
            'username' => "enesoncn",
            'email' => "enes@admin.com",
            'email_verified_at' => now(),
            'password' => Hash::make('pass1234'),
            'gender'=>'M',
            'remember_token' => Str::random(10),
        ]);
        User::create([
            'name' => "Yiğit Arik",
            'username' => "yigit",
            'email' => "yigit@admin.com",
            'email_verified_at' => now(),
            'password' => Hash::make('pass1234'),
            'gender'=>'M',
            'remember_token' => Str::random(10),
        ]);
         \App\Models\User::factory(10)->create();
    }
}
