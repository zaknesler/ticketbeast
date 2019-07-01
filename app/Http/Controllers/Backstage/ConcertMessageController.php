<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConcertMessageController extends Controller
{
    /**
     * Show the form to create a new message.
     *
     * @param  \App\Models\Concert  $concert
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Concert $concert, Request $request)
    {
        abort_unless($concert->user->is($request->user()), 404);
        abort_unless($concert->isPublished(), 404);

        return view('backstage.concerts.messages.create', [
            'concert' => $concert,
        ]);
    }
}
