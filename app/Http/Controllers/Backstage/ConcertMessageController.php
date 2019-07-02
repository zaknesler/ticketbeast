<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backstage\Concert\StoreAttendeeMessageRequest;

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

    /**
     * Store a message and send it to the attendees.
     *
     * @param  \App\Models\Concert  $concert
     * @param  \App\Http\Requests\Backstage\Concert\StoreAttendeeMessageRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Concert $concert, StoreAttendeeMessageRequest $request)
    {
        abort_unless($concert->user->is($request->user()), 404);
        abort_unless($concert->isPublished(), 404);

        $message = $concert->attendeeMessages()->create([
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('backstage.concerts.messages.create', $concert)
            ->with('flash', 'Message has been sent.');
    }
}
