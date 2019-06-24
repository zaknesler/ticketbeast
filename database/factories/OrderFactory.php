<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Order::class, function (Faker $faker) {
    return [
        'amount' => 2500,
        'email' => 'john@example.com',
        'confirmation_number' => 'ord_1234',
        'card_last_four' => '1234',
    ];
});
