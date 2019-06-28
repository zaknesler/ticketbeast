<?php

namespace App\Http\Controllers\Backstage;

use Carbon\Carbon;
use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backstage\Concert\StoreConcertRequest;

class ConcertController extends Controller
{
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

        return redirect()->route('concerts.show', $concert);
    }
}
