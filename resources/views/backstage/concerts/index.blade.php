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
      @if ($concerts->count())
        <div class="mb-2 text-gray-600">Published</div>

        @if ($concerts->filter->isPublished()->count())
          <div class="-m-3 flex flex-wrap">
            @foreach ($concerts->filter->isPublished() as $concert)
              <div class="p-3 w-full sm:w-1/2 md:w-1/3">
                <div class="p-6 bg-white border rounded-lg flex flex-col">
                  <div class="flex-1">
                    <a class="text-lg text-gray-800 hover:underline font-semibold" href="{{ route('concerts.show', $concert) }}">{{ $concert->title }}</a>

                    <time class="block text-sm text-gray-600" datetime="{{ $concert->date }}">
                      {{ $concert->formatted_date }} @ {{ $concert->formatted_start_time }}
                    </time>
                  </div>

                  <div class="pt-6 text-sm flex items-center justify-between">
                    <div class="text-gray-500">
                      {{ $concert->ticketsRemaining() }} {{ Str::plural('ticket', $concert->ticketsRemaining()) }} left
                    </div>

                    <div>
                      <a class="text-brand-600 hover:underline" href="{{ route('concerts.show', $concert) }}">View</a>
                      @if (!$concert->isPublished())
                        <a class="pl-4 text-brand-600 hover:underline" href="{{ route('backstage.concerts.edit', $concert) }}">Edit</a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-16 bg-white border rounded-lg flex flex-col items-center justify-center text-center">
            <svg class="w-10 h-10 text-gray-400 fill-current self-center" viewBox="0 0 42 42" width="42" height="42" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.667 17.222V5.666c0-1.84-1.493-3.333-3.334-3.333C7.496 2.333 6 3.825 6 5.666v23.337C6 35.628 11.373 41 18 41c3.687 0 6.984-1.66 9.185-4.276l11.712-11.712c1.17-1.168.85-2.572-.712-3.133l-.585-.21c-1.565-.563-3.778-.068-4.948 1.102L30 25.424V4c0-1.65-1.343-3-3-3-1.653 0-3 1.343-3 3v11.333l-11.333 1.89z" />
            </svg>

            <div class="mt-8 text-lg text-gray-600">You haven't published any concerts yet!</div>
          </div>
        @endif

        <div class="mt-12 mb-2 text-gray-600">Drafts</div>
        @if ($concerts->reject->isPublished()->count())
          <div class="-m-3 flex flex-wrap">
          @foreach ($concerts->reject->isPublished() as $concert)
            <div class="p-3 w-full sm:w-1/2 md:w-1/3">
              <div class="p-6 bg-white border rounded-lg flex flex-col">
                <div class="flex-1">
                  <a class="text-lg text-gray-800 hover:underline font-semibold" href="{{ route('concerts.show', $concert) }}">{{ $concert->title }}</a>

                  <time class="block text-sm text-gray-600" datetime="{{ $concert->date }}">
                    {{ $concert->formatted_date }} @ {{ $concert->formatted_start_time }}
                  </time>
                </div>

                <div class="pt-6 text-sm flex items-center justify-between">
                  <div class="text-gray-500">
                    {{ $concert->ticketsRemaining() }} {{ Str::plural('ticket', $concert->ticketsRemaining()) }} left
                  </div>

                  <div>
                    <a class="text-brand-600 hover:underline" href="{{ route('concerts.show', $concert) }}">View</a>
                    @if (!$concert->isPublished())
                      <a class="pl-4 text-brand-600 hover:underline" href="{{ route('backstage.concerts.edit', $concert) }}">Edit</a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <div class="p-16 bg-white border rounded-lg flex flex-col items-center justify-center text-center">
            <svg class="w-10 h-10 text-gray-400 fill-current self-center" viewBox="0 0 42 42" width="42" height="42" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.667 17.222V5.666c0-1.84-1.493-3.333-3.334-3.333C7.496 2.333 6 3.825 6 5.666v23.337C6 35.628 11.373 41 18 41c3.687 0 6.984-1.66 9.185-4.276l11.712-11.712c1.17-1.168.85-2.572-.712-3.133l-.585-.21c-1.565-.563-3.778-.068-4.948 1.102L30 25.424V4c0-1.65-1.343-3-3-3-1.653 0-3 1.343-3 3v11.333l-11.333 1.89z" />
            </svg>

            <div class="mt-8 text-lg text-gray-600">You don't have any unpublished concerts!</div>

            <div class="mt-8">
              <a href="{{ route('backstage.concerts.create') }}" class="inline-block btn">Create a concert</a>
            </div>
          </div>
        @endif
      @else
        <div class="p-16 bg-white border rounded-lg flex flex-col items-center justify-center text-center">
          <svg class="w-10 h-10 text-gray-400 fill-current self-center" viewBox="0 0 42 42" width="42" height="42" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.667 17.222V5.666c0-1.84-1.493-3.333-3.334-3.333C7.496 2.333 6 3.825 6 5.666v23.337C6 35.628 11.373 41 18 41c3.687 0 6.984-1.66 9.185-4.276l11.712-11.712c1.17-1.168.85-2.572-.712-3.133l-.585-.21c-1.565-.563-3.778-.068-4.948 1.102L30 25.424V4c0-1.65-1.343-3-3-3-1.653 0-3 1.343-3 3v11.333l-11.333 1.89z" />
          </svg>

          <div class="mt-8 text-lg text-gray-600">You haven't created any concerts!</div>

          <div class="mt-8">
            <a href="{{ route('backstage.concerts.create') }}" class="inline-block btn">Create a concert</a>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
