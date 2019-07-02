<?php

use Faker\Generator as Faker;

/* @var $factory \Illuminate\Database\Eloquent\Factory */
$factory->define(\App\Models\AttendeeMessage::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(\App\Models\Concert::class)->create()->id,
        },
        'subject' => 'Example Subject',
        'body' => 'Example Body',
    ];
});
