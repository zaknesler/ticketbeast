<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(App\Models\Concert::class)->create()->id;
        },
    ];
});
