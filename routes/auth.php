<?php

Route::get('/home', 'HomeController@index')->name('home');

/**
 * --------------------------------------------------------------------------
 * Backstage
 * --------------------------------------------------------------------------
 */
Route::get('/backstage/concerts/new', 'Backstage\\ConcertController@create')->name('backstage.concerts.create');
