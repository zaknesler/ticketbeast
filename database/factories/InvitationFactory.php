<?php

use Faker\Generator as Faker;

/* @var $factory \Illuminate\Database\Eloquent\Factory */
$factory->define(\App\Models\Invitation::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\App\Models\User::class)->create()->id;
        },
        'email' => 'john@example.com',
        'code' => 'TESTCODE1234',
    ];
});
