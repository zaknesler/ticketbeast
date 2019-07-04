<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Display the registration page for an invitation code.
     *
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $invitation = Invitation::findByCode($code);

        return view('invitations.show', [
            'invitation' => $invitation,
        ]);
    }
}
