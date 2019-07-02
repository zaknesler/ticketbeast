<?php

Route::get('/home', 'HomeController@index')->name('home');

/**
 * --------------------------------------------------------------------------
 * Backstage
 * --------------------------------------------------------------------------
 */
Route::prefix('/backstage')->namespace('Backstage')->group(function () {
    /**
     * --------------------------------------------------------------------------
     * Concerts
     * --------------------------------------------------------------------------
     */
    Route::get('/concerts', 'ConcertController@index')->name('backstage.concerts.index');
    Route::post('/concerts', 'ConcertController@store')->name('backstage.concerts.store');
    Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concerts.create');
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
