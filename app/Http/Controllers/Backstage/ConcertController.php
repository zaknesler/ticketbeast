<?php

namespace App\Http\Controllers\Backstage;

use Carbon\Carbon;
use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backstage\Concert\StoreConcertRequest;
use App\Http\Requests\Backstage\Concert\UpdateConcertRequest;

class ConcertController extends Controller
{
    /**
     * Show all concerts that belong to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $concerts = $request->user()->concerts()->latest()->get();

        return view('backstage.concerts.index', [
            'concerts' => $concerts,
        ]);
    }

    /**
     * Show the concert create form.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backstage.concerts.create');
    }

    /**
     * Show the concert edit form.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Concert $concert, Request $request)
    {
        abort_unless($concert->user->is($request->user()), 404);
        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', [
            'concert' => $concert,
        ]);
    }

    /**
     * Create a new concert.
     *
     * @param  \App\Http\Requests\Backstage\Concert\StoreConcertRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreConcertRequest $request)
    {
        $concert = $request->user()->concerts()->create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'date' => Carbon::parse(vsprintf('%s %s', [$request->date, $request->time])),
            'ticket_price' => $request->ticket_price * 100,
            'venue' => $request->venue,
            'venue_address' => $request->venue_address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'additional_information' => $request->additional_information,
        ])->addTickets($request->ticket_quantity);

        $concert->publish();

        return redirect()->route('concerts.show', $concert);
    }

    /**
     * Update an existing concert.
     *
     * @param  \App\Models\Concert  $concert
     * @param  \App\Http\Requests\Backstage\Concert\UpdateConcertRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Concert $concert, UpdateConcertRequest $request)
    {
        abort_unless($concert->user->is($request->user()), 404);
        abort_if($concert->isPublished(), 403);

        $concert->update([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'additional_information' => request('additional_information'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'ticket_price' => request('ticket_price') * 100,
        ]);

        return redirect()->route('backstage.concerts.index');
    }
}
