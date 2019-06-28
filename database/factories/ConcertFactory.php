<?php

use Carbon\Carbon;
use App\Models\User;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Concert::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'title' => 'Example Band',
        'subtitle' => 'with The Fake Openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example Ln.',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'additional_information' => 'Some example additional information.',
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Models\Concert::class, 'published', function (Faker $faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Models\Concert::class, 'unpublished', function (Faker $faker) {
    return [
        'published_at' => null,
    ];
});
