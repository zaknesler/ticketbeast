<?php

use App\Http\Middleware\ConnectStripeAccount;

Route::get('/home', 'HomeController@index')->name('home');

/**
 * --------------------------------------------------------------------------
 * Backstage
 * --------------------------------------------------------------------------
 */
Route::prefix('/backstage')->namespace('Backstage')->group(function () {
    Route::middleware(ConnectStripeAccount::class)->group(function () {
        /**
         * --------------------------------------------------------------------------
         * Concerts
         * --------------------------------------------------------------------------
         */
        Route::get('/concerts', 'ConcertController@index')->name('backstage.concerts.index');
        Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concerts.create');
        Route::post('/concerts', 'ConcertController@store')->name('backstage.concerts.store');
        Route::get('/concerts/{concert}/edit', 'ConcertController@edit')->name('backstage.concerts.edit');
        Route::patch('/concerts/{concert}', 'ConcertController@update')->name('backstage.concerts.update');

        Route::post('/published-concerts', 'PublishedConcertController@store')->name('backstage.publishedConcerts.store');

        /**
         * --------------------------------------------------------------------------
         * Concert Orders
         * --------------------------------------------------------------------------
         */
        Route::get('/concerts/{concert}/orders', 'ConcertOrderController@show')->name('backstage.concerts.orders.show');

        /**
         * --------------------------------------------------------------------------
         * Concert Messages
         * --------------------------------------------------------------------------
         */
        Route::get('/concerts/{concert}/messages/new', 'ConcertMessageController@create')->name('backstage.concerts.messages.create');
        Route::post('/concerts/{concert}/messages', 'ConcertMessageController@store')->name('backstage.concerts.messages.store');
    });

    /**
     * --------------------------------------------------------------------------
     * Stripe Connect
     * --------------------------------------------------------------------------
     */
    Route::get('/stripe-connect/connect', 'StripeConnectController@connect')->name('backstage.stripe-connect.connect');
    Route::get('/stripe-connect/redirect', 'StripeConnectController@redirect')->name('backstage.stripe-connect.redirect');
    Route::get('/stripe-connect/authorize', 'StripeConnectController@authorizeRedirect')->name('backstage.stripe-connect.authorize');
});
