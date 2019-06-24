@extends('layouts.base')

@section('title', $concert->title)
@section('show-header', false)

@section('head')
  <script src="https://checkout.stripe.com/checkout.js"></script>
@endsection

@section('content-full')
  <div class="min-h-full flex bg-gray-100">
    <div class="md:p-6 m-auto max-w-4xl w-full">
      <div class="bg-white md:rounded-lg md:flex md:shadow-xl">
        <div class="flex-1">
          <img class="w-full h-full pointer-events-none select-none md:rounded-l-lg" src="https://placehold.it/400x500" alt="Poster Image" />
        </div>

        <div class="p-8 flex-1 md:border md:border-l-0 md:rounded-r-lg">
          <div class="text-gray-900 text-2xl font-medium">{{ $concert->title }}</div>
          <div class="text-gray-600">{{ $concert->subtitle }}</div>

          <div class="mt-8">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                <path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z" />
              </svg>

              <span class="ml-3 text-gray-800 font-medium">{{ $concert->formatted_date }}</span>
            </div>

            <div class="mt-6 flex items-center">
              <svg class="w-5 h-5 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z" />
              </svg>

              <span class="ml-3 text-gray-800 font-medium">Doors open at {{ $concert->formatted_start_time }}</span>
            </div>

            <div class="mt-6 flex items-center">
              <svg class="w-5 h-5 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm1-5h1a3 3 0 0 0 0-6H7.99a1 1 0 0 1 0-2H14V5h-3V3H9v2H8a3 3 0 1 0 0 6h4a1 1 0 1 1 0 2H6v2h3v2h2v-2z" />
              </svg>

              <span class="ml-3 text-gray-800 font-medium">
                {{ number_format($concert->ticket_price / 100, 2) }}
                <span class="text-gray-600 font-normal text-sm">per ticket</span>
              </span>
            </div>

            <div class="mt-6">
              <div class="flex items-center">
                <svg class="mt-1 w-5 h-5 h-full text-brand-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                  <path d="M10 20S3 10.87 3 7a7 7 0 1 1 14 0c0 3.87-7 13-7 13zm0-11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
                </svg>

                <span class="ml-3 text-gray-800 font-medium">{{ $concert->venue }}</span>
              </div>

              <div class="ml-8 mt-1 text-gray-600">
                <p>{{ $concert->venue_address }}</p>
                <p>{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>
              </div>
            </div>

            <div class="mt-6">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-brand-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
                  <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM9 11v4h2V9H9v2zm0-6v2h2V5H9z" />
                </svg>

                <span class="ml-3 text-gray-800 font-medium">Additional Information</span>
              </div>

              <div class="ml-8 mt-1 text-gray-600">{{ $concert->additional_information }}</div>
            </div>

            <div class="my-6 w-full h-px bg-gray-200"></div>

            <purchase-tickets
              :concert-id="{{ $concert->id }}"
              concert-title="{{ $concert->title }}"
              :max-tickets="{{ min($concert->ticketsRemaining(), 10) }}"
              :ticket-price="{{ $concert->ticket_price }}"
            ></purchase-tickets>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
