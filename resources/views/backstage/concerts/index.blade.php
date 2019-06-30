@extends('layouts.base')

@section('title', 'Your Concerts')
@section('show-header', true)
@section('show-footer', true)

@section('content-full')
  <div class="h-full bg-gray-100">
    <div class="w-full bg-white border-b">
      <div class="px-6 py-4 mx-auto max-w-4xl flex items-center justify-between">
        <h3 class="font-light text-gray-700">Your Concerts</h3>

        <div class="text-sm">
          <a class="text-brand-600 hover:underline" href="{{ route('backstage.concerts.create') }}">Create Concert</a>
        </div>
      </div>
    </div>

    <div class="p-6 mx-auto max-w-4xl">
      @if ($concerts->count())
        <div class="mb-2 text-gray-600">Published</div>

        @if ($concerts->filter->isPublished()->count())
          <div class="-m-3 flex flex-wrap">
            @foreach ($concerts->filter->isPublished() as $concert)
              @include('backstage.concerts.partials.concert-card', $concert)
            @endforeach
          </div>
        @else
          @include('backstage.concerts.partials.blank-state', ['hideCreateButton' => true, 'text' => 'You haven\'t published any concerts yet!'])
        @endif

        <div class="mt-12 mb-2 text-gray-600">Drafts</div>

        @if ($concerts->reject->isPublished()->count())
          <div class="-m-3 flex flex-wrap">
            @foreach ($concerts->reject->isPublished() as $concert)
              @include('backstage.concerts.partials.concert-card', $concert)
            @endforeach
          </div>
        @else
          @include('backstage.concerts.partials.blank-state', ['text' => 'You don\'t have any unpublished concerts!'])
        @endif
      @else
        @include('backstage.concerts.partials.blank-state')
      @endif
    </div>
  </div>
@endsection
