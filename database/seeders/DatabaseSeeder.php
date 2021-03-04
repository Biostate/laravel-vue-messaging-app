<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\UserContact;
use \App\Models\Conversation;
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
        User::insert(
            [
                [
                    'name' => 'Süleyman',
                    'email' => 'suleyman@gmail.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
                [
                    'name' => 'Büşra',
                    'email' => 'busra@gmail.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            ]);
        User::factory(15)->create();
//        UserContact::factory(40)->create();
        Conversation::factory(10)->create();
        Participant::insert([
            [
                'user_id' => 1,
                'conversation_id' => 1
            ],
            [
                'user_id' => 2,
                'conversation_id' => 1
            ]
        ]);
//        $users = User::all();
//        $users->each(function ($user){
//
//        });
    }
}
