@extends('layouts.base')

@section('title', 'Order Confirmation')
@section('show-header', false)

@section('content-full')
  <div class="min-h-full bg-gray-100">
    <div class="p-6 md:p-16 m-auto max-w-4xl w-full">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="text-lg font-light text-gray-800">Order Summary</h3>
          <time datetime="{{ $order->created_at->toDateTimeString() }}" class="text-sm text-gray-600">
            {{ $order->created_at->format('l, F j, Y') }}
          </time>
        </div>

        <a href="{{ route('orders.show', $order->confirmation_number) }}" class="font-mono text-brand-600 hover:text-brand-800">#{{ $order->confirmation_number }}</a>
      </div>

      <div class="mt-6 w-full h-px bg-gray-300"></div>

      <div class="mt-3">
        <div class="text-xl font-semibold">${{ number_format($order->amount / 100, 2) }}</div>
        <div class="mt-2 text-xs font-mono text-gray-600">
          &bullet;&bullet;&bullet;&bullet;
          &bullet;&bullet;&bullet;&bullet;
          &bullet;&bullet;&bullet;&bullet;
          <span class="text-sm">{{ $order->card_last_four }}</span>
        </div>
      </div>

      <div class="mt-3 w-full h-px bg-gray-300"></div>

      <h3 class="mt-6 text-lg font-light">Your Tickets</h3>

      <div class="-mt-8">
        @foreach($order->tickets as $ticket)
          <div class="mt-12 rounded-lg">
            <div class="p-6 bg-gray-700 rounded-t-lg flex justify-between">
              <div>
                <div class="text-xl font-light text-white">{{ $ticket->concert->title }}</div>
                <div class="-mt-1 font-light text-gray-500">{{ $ticket->concert->subtitle }}</div>
              </div>

              <div class="text-right">
                <div class="font-semibold text-white">General Admission</div>
                <div class="-mt-1 font-light text-gray-500">Admit one</div>
              </div>
            </div>

            <div class="p-8 bg-white border-l border-r sm:flex">
              <div class="flex-1">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-brand-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                    <path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z" />
                  </svg>

                  <time datetime="{{ $ticket->concert->date->toDateTimeString() }}" class="ml-3 text-gray-800 font-medium">
                    {{ $ticket->concert->date->format('l, F j, Y') }}
                  </time>
                </div>

                <div class="ml-8 text-gray-600">
                  Doors at
                  <time datetime="{{ $ticket->concert->date->toDateTimeString() }}">
                    {{ $ticket->concert->date->format('g:ia') }}
                  </time>
                </div>
              </div>

              <div class="mt-8 sm:mt-0 sm:ml-6 flex-1">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-brand-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                    <path d="M10 20S3 10.87 3 7a7 7 0 1 1 14 0c0 3.87-7 13-7 13zm0-11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
                  </svg>

                  <div class="ml-3 text-gray-800 font-medium">{{ $ticket->concert->venue }}</div>
                </div>

                <div class="ml-8 text-gray-600">
                  <p>{{ $ticket->concert->venue_address }}</p>
                  <p>{{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}</p>
                </div>
              </div>
            </div>

            <div class="px-6 py-4 bg-white border rounded-b-lg flex justify-between items-center">
              <div class="text-xl font-mono text-gray-700">{{ $ticket->code }}</div>
              <div class="text-gray-600">{{ $order->email }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endsection
