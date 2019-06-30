<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublishedConcertController extends Controller
{
    public function store(Request $request)
    {
        $concert = $request->user()->concerts()->findOrFail($request->concert_id);

        abort_if($concert->isPublished(), 422);

        $concert->publish();

        return redirect()->route('backstage.concerts.index');
    }
}
