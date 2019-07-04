<?php

Route::view('/', 'welcome')->name('index');

/**
 * --------------------------------------------------------------------------
 * Auth
 * --------------------------------------------------------------------------
 */
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('auth.login');
Route::post('/login', 'Auth\LoginController@login')->name('auth.login.store');
Route::post('/logout', 'Auth\LoginController@logout')->name('auth.logout');

// Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Route::get('/email/verify', 'Auth\VerificationController@show')->name('verification.notice');
// Route::get('/email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
// Route::get('/email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

/**
 * --------------------------------------------------------------------------
 * Concerts
 * --------------------------------------------------------------------------
 */
Route::get('/concerts', 'ConcertController@index')->name('concerts.index');
Route::get('/concerts/{concert}', 'ConcertController@show')->name('concerts.show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store')->name('concerts.orders.store');

/**
 * --------------------------------------------------------------------------
 * Orders
 * --------------------------------------------------------------------------
 */
Route::get('/orders/{confirmationNumber}', 'OrderController@show')->name('orders.show');

/**
 * --------------------------------------------------------------------------
 * Invitations
 * --------------------------------------------------------------------------
 */
Route::get('/invitations/{code}', 'InvitationController@show')->name('invitations.show');
