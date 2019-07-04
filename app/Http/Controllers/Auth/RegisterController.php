<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register a user who has a valid invitation code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $invitation = Invitation::findByCode($request->invitation_code);
        abort_if($invitation->hasBeenUsed(), 404);

        $request->validate([
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $invitation->update([
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        return redirect(route('backstage.concerts.index'));
    }
}
