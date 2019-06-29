@extends('layouts.base')

@section('title', 'Your Concerts')
@section('show-header', true)
@section('show-footer', true)

@section('content-full')
  <div class="h-full bg-gray-100">
    <div class="w-full bg-white border-b">
      <div class="px-6 py-4 mx-auto max-w-4xl flex items-center justify-between">
        <h3 class="text-lg font-light text-gray-700">Your Concerts</h3>

        <div class="text-sm">
          <a class="text-brand-600 hover:underline" href="{{ route('backstage.concerts.create') }}">Create Concert</a>
        </div>
      </div>
    </div>

    <div class="p-6 mx-auto max-w-4xl">
      <div class="-m-3 flex flex-wrap">
        @foreach ($concerts as $concert)
          <div class="p-3 w-full sm:w-1/2 md:w-1/3">
            <div class="p-6 bg-white border rounded-lg flex flex-col">
              <div class="flex-1">
                <div class="text-gray-800 font-semibold">{{ $concert->title }}</div>

                <time class="block text-sm text-gray-600" datetime="{{ $concert->date }}">
                  {{ $concert->formatted_date }}
                </time>
              </div>

              <div class="pt-6 text-sm flex items-center justify-between">
                <div class="text-gray-500">
                  {{ $concert->ticketsRemaining() }} {{ Str::plural('ticket', $concert->ticketsRemaining()) }} left
                </div>

                <div>
                  <a class="text-brand-600 hover:underline" href="#">View</a>
                  <a class="pl-4 text-brand-600 hover:underline" href="#">Edit</a>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endsection
