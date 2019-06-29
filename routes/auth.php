<?php

Route::get('/home', 'HomeController@index')->name('home');

/**
 * --------------------------------------------------------------------------
 * Backstage
 * --------------------------------------------------------------------------
 */
Route::prefix('/backstage')->namespace('Backstage')->group(function () {
    Route::get('/concerts', 'ConcertController@index')->name('backstage.concerts.index');
    Route::post('/concerts', 'ConcertController@store')->name('backstage.concerts.store');
    Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concerts.create');
    Route::get('/concerts/{concert}/edit', 'ConcertController@edit')->name('backstage.concerts.edit');
    Route::patch('/concerts/{concert}', 'ConcertController@update')->name('backstage.concerts.update');
});
