@extends('layouts.base')

@section('title', 'Your Concerts')
@section('show-header', true)
@section('show-footer', true)

@section('content-full')
  <div class="h-full bg-gray-100">
    <div class="w-full bg-white border-b">
      <div class="px-6 py-4 mx-auto max-w-4xl flex items-baseline">
        <h3 class="font-semibold text-gray-700">{{ $concert->title }}</h3>
        <span class="mx-2 text-gray-500">&ndash;</span>
        <div class="text-sm font-light text-gray-600">{{ $concert->formatted_date }}</div>
      </div>
    </div>

    <div class="p-6 mx-auto max-w-4xl">
      <div class="text-gray-600">Overview</div>

      <div class="mt-2 border bg-white rounded-lg">
        <div class="p-4 border-b">
          <div>The show is {{ $concert->percentSoldOut() }}% sold out!</div>

          <div class="mt-4 h-2 rounded-full bg-gray-200 z-10">
            <div class="h-full bg-brand-500 z-20 rounded-full" style="width: {{ $concert->percentSoldOut() }}%"></div>
          </div>
        </div>

        <div class="flex">
          <div class="p-4 w-1/3 flex-1 border-r">
            <div class="text-sm text-gray-600">Total Tickets Remaining</div>
            <div class="text-3xl font-bold text-gray-800">{{ $concert->ticketsRemaining() }}</div>
          </div>

          <div class="p-4 w-1/3 flex-1 border-r">
            <div class="text-sm text-gray-600">Total Tickets Sold</div>
            <div class="text-3xl font-bold text-gray-800">{{ $concert->ticketsSold() }}</div>
          </div>

          <div class="p-4 w-1/3 flex-1">
            <div class="text-sm text-gray-600">Total Revenue</div>
            <div class="text-3xl font-bold text-gray-800">${{ number_format($concert->revenueInDollars(), 2) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
