<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Attempt to authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        if (Auth::attempt($request->all(['email', 'password']))) {
            return redirect(route('backstage.concerts.index'));
        }

        return redirect(route('auth.login'))
            ->withInput(['email' => $request->email])
            ->withErrors([
                'email' => ['These credentials do not match our records.'],
            ]);
    }

    /**
     * Unauthenticate the user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        return redirect(route('auth.login'));
    }
}
