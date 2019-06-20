<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;

class ConcertController extends Controller
{
    /**
     * Get a listing of all published concerts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $concerts = Concert::whereNotNull('published_at')->get();

        return view('concerts.index')
            ->with('concerts', $concerts);
    }

    /**
     * Display a single concert.
     *
     * @param  App\Models\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function show(Concert $concert)
    {
        if (is_null($concert->published_at)) {
            return abort(404);
        }

        return view('concerts.show')
            ->with('concert', $concert);
    }
}
