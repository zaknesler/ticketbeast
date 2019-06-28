<?php

Route::get('/home', 'HomeController@index')->name('home');

/**
 * --------------------------------------------------------------------------
 * Backstage
 * --------------------------------------------------------------------------
 */
Route::get('/backstage/concerts/new', 'Backstage\\ConcertController@create')->name('backstage.concerts.create');
Route::post('/backstage/concerts', 'Backstage\\ConcertController@store')->name('backstage.concerts.store');
