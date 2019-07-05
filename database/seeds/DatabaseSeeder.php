<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Database\Helpers\ConcertHelper;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $gateway = new \App\Billing\FakePaymentGateway;

        $user = factory(User::class)->create([
            'email' => 'zak@example.com',
            'password' => Hash::make('password'),
            'stripe_account_id' => null,
            'stripe_access_token' => null,
        ]);

        $posterPath = Storage::disk('public')->putFile('posters', new File(base_path('tests/__stubs__/optimized-poster.png')));
        $concert = ConcertHelper::createPublished([
            'user_id' => $user->id,
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'additional_information' => 'For support, call (555) 555-5555.',
            'date' => Carbon::parse('November 25, 2020 8:30pm'),
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '90210',
            'ticket_price' => 3250,
            'ticket_quantity' => 100,
            'poster_image_path' => $posterPath,
        ]);

        foreach(range(1, 25) as $i) {
            Carbon::setTestNow(Carbon::instance($faker->dateTimeBetween('-2 months')));

            $concert->reserveTickets(rand(1, 4), $faker->safeEmail)
                    ->complete($gateway, $gateway->getValidTestToken($faker->creditCardNumber), 'test_acct_1234');
        }

        $posterPath = Storage::disk('public')->putFile('posters', new File(base_path('tests/__stubs__/fat-night-optimized.png')));
        ConcertHelper::createUnpublished([
            'user_id' => $user->id,
            'title' => 'Fat Night',
            'subtitle' => 'with Ginger Root',
            'date' => Carbon::parse('December 18, 2021 6:30pm'),
            'venue' => 'Jazz Hall',
            'venue_address' => '129 West 81st Street',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10024',
            'ticket_price' => 3250,
            'ticket_quantity' => 50,
            'poster_image_path' => $posterPath,
        ]);
    }
}
