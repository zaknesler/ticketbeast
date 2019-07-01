@extends('layouts.base')

@section('title', 'Your Concerts')
@section('show-header', true)
@section('show-footer', true)

@section('content-full')
  <div class="h-full bg-gray-100">
    <div class="w-full bg-white border-b">
      <div class="px-6 py-4 mx-auto max-w-4xl flex items-baseline justify-between">
        <div class="flex items-baseline">
          <h3 class="font-semibold text-gray-700">{{ $concert->title }}</h3>
          <span class="mx-2 text-xs text-gray-500">&ndash;</span>
          <div class="text-xs text-gray-600">{{ $concert->formatted_date }}</div>
        </div>

        <nav class="text-sm text-gray-800">
          <a href="{{ route('backstage.concerts.orders.show', $concert) }}" class="mr-4 hover:underline">Orders</a>
          <a href="{{ route('backstage.concerts.messages.create', $concert) }}" class="font-semibold hover:underline">Message Attendees</a>
        </nav>
      </div>
    </div>

    <div class="p-6 mx-auto max-w-4xl">
      <div class="text-gray-800 text-center">Send a Message</div>

      <form class="mt-6 p-6 mx-auto max-w-xl bg-white border rounded-lg" action="#" method="post">
        @csrf

        <div>
          <label>
            <span class="text-xs font-medium text-gray-600 {{ $errors->first('subject', 'text-red-700') }}">Subject</span>
            <input
              autofocus
              required
              tabindex="1"
              type="text"
              name="subject"
              value="{{ old('subject') }}"
              class="mt-1 form-input block w-full {{ $errors->first('subject', 'border-red-500') }}"
              placeholder="Enter a subject..."
            />

            @if ($errors->has('subject'))
              <div class="px-3 py-2 mt-2 text-xs font-semibold bg-red-100 text-red-700 rounded-lg">{{ $errors->first('subject') }}</div>
            @endif
          </label>
        </div>

        <div class="mt-3">
          <label>
            <span class="text-xs font-medium text-gray-600 {{ $errors->first('password', 'text-red-700') }}">Message</span>

            <textarea
              required
              tabindex="2"
              name="message"
              class="mt-1 form-textarea min-h-32 block w-full {{ $errors->first('password', 'border-red-500') }}"
              placeholder="Enter your message..."
              rows="10"
            >{{ old('message') }}</textarea>

            @if ($errors->has('password'))
              <div class="px-3 py-2 mt-2 text-xs font-semibold bg-red-100 text-red-700 rounded-lg">{{ $errors->first('password') }}</div>
            @endif
          </label>
        </div>

        <div class="mt-6 text-right">
          <button tabindex="4" class="btn px-5 py-2 text-sm">Send Message</button>
        </div>
      </form>
    </div>
  </div>
@endsection
