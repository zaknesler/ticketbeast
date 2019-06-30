<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-full h-full">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title') @yield('title') &dash; {{ config('app.name') }} @else {{ config('app.name') }} @endif</title>

    @yield('head')

    <link rel="icon" href="/favicon.png">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
  </head>
  <body class="font-sans font-normal text-base tracking-normal leading-normal bg-white text-gray-700 min-h-full h-full">
    <div id="app" class="min-h-full h-full flex flex-col" v-cloak>
      <div class="flex-1">
        @hasSection('show-header')
          @include('layouts/partials/_header')
        @endif

        @hasSection('content-full')
          @yield('content-full')
        @endif

        @hasSection('content')
          <div class="p-6 w-full">
            <div class="max-w-2xl mx-auto">
              @yield('content')
            </div>
          </div>
        @endif
      </div>

      @hasSection('show-footer')
        <div class="px-6 py-8 w-full bg-gray-900 text-gray-500 shadow-inner text-center text-sm">
          &copy; Ticketbeast {{ date('Y') }}
        </div>
      @endif
    </div>
  </body>
</html>
