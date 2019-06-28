<?php

Route::get('/home', 'HomeController@index')->name('home');

/**
 * --------------------------------------------------------------------------
 * Backstage
 * --------------------------------------------------------------------------
 */
Route::prefix('/backstage')->namespace('Backstage')->group(function () {
    Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concerts.create');
    Route::post('/concerts', 'ConcertController@store')->name('backstage.concerts.store');
});
