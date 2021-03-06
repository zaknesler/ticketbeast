<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(App\Models\Concert::class)->create()->id;
        },
        'order_id' => function () {
            return factory(App\Models\Order::class)->create()->id;
        },
        'code' => 'tik_1234',
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Models\Ticket::class, 'reserved', function (Faker $faker) {
    return [
        'reserved_at' => now(),
    ];
});
