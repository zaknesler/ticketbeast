<?php

use App\Models\User;
use App\Models\Concert;
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
        $user = factory(User::class)->create([
            'name' => 'Zak Nesler',
            'email' => 'zak@example.com',
            'password' => Hash::make('password'),
        ]);

        ConcertHelper::createPublished(['user_id' => $user->id, 'ticket_quantity' => 50]);
    }
}
