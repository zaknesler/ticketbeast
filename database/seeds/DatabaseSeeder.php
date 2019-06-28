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
        factory(User::class)->create([
            'email' => 'zak@example.com',
            'password' => Hash::make('password'),
        ]);

        factory(Concert::class)
            ->states('published')
            ->create()
            ->addTickets(10);
    }
}
