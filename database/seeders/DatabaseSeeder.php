<?php

namespace Database\Seeders;

use App\Models\HistoryBet;
use App\Models\Transection;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        foreach (range(7, 15) as $index) {
            $user =  User::create([
                'name' => 'User ' . $index,
                'email' => 'user' . $index . '@gmail.com',
                'password' => Hash::make('password'),
            ]);
            $transection = Transection::create([
                'date' => '2021-10-10',
                'time' => '10:00',
                'result' => 1,
            ]);
            HistoryBet::create([
                'transaction_id' => $transection->id,
                'user_id' => $user->id,
                'bet' => 1,
                'amount' => 100,
                'status' => 'published',
            ]);
        }
    }
}
