<?php

use Faker\Generator as Faker;

/* @var $factory \Illuminate\Database\Eloquent\Factory */
$factory->define(\App\Models\Invitation::class, function (Faker $faker) {
    return [
        'code' => 'TESTCODE1234',
    ];
});
