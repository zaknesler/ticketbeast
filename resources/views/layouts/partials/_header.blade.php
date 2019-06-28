<div class="w-full bg-gray-900 text-white border-b">
  <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between">
    <div class="m-6 sm:mr-0">
      <a href="/" class="font-semibold text-white hover:text-brand-400 no-underline flex items-center">
        <svg class="w-5 h-5 fill-current" viewBox="0 0 42 42" width="42" height="42" xmlns="http://www.w3.org/2000/svg">
          <path d="M12.667 17.222V5.666c0-1.84-1.493-3.333-3.334-3.333C7.496 2.333 6 3.825 6 5.666v23.337C6 35.628 11.373 41 18 41c3.687 0 6.984-1.66 9.185-4.276l11.712-11.712c1.17-1.168.85-2.572-.712-3.133l-.585-.21c-1.565-.563-3.778-.068-4.948 1.102L30 25.424V4c0-1.65-1.343-3-3-3-1.653 0-3 1.343-3 3v11.333l-11.333 1.89z" />
        </svg>

        <span class="ml-1">Ticketbeast</span>
      </a>
    </div>

    <div class="m-3 block sm:hidden">
      <a
        href="#"
        class="p-3 flex items-center no-underline text-brand-400 hover:text-white focus:bg-gray-800 rounded-lg"
        aria-label="Toggle navigation"
        :aria-expanded="displayNavigation"
        aria-controls="#headerNav"
        @click.prevent="displayNavigation = !displayNavigation"
      >
        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
          <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
        </svg>
      </a>
    </div>

    <div
      id="#headerNav"
      class="py-3 sm:p-0 sm:mr-6 w-full sm:w-auto sm:flex sm:flex-grow sm:items-center shadow-inner sm:shadow-none bg-gray-800 sm:bg-transparent"
      :class="{ hidden: !displayNavigation }"
      :aria-expanded="displayNavigation"
    >
      <nav class="sm:flex-1" aria-label="Left navigation">
        <ul class="sm:flex sm:items-baseline">
          {{-- <li><a href="{{ route('home') }}" class="px-6 py-3 sm:p-0 sm:ml-5 block sm:inline-block text-brand-400 hover:text-white hover:bg-gray-900 sm:hover:bg-transparent no-underline">Home</a></li> --}}
        </ul>
      </nav>

      <nav class="sm:flex sm:items-center" aria-label="Right navigation">
        <ul class="sm:flex sm:items-baseline">
          @auth
            <li>
              <a href="#" class="px-6 py-3 sm:p-0 sm:ml-5 block sm:inline-block text-brand-400 hover:text-white hover:bg-gray-900 sm:hover:bg-transparent no-underline" onclick="document.querySelector('#logoutForm').submit()">Log out</a>
              <form action="{{ route('logout') }}" method="POST" id="logoutForm" class="hidden">@csrf</form>
            </li>
          @else
            <li><a href="{{ route('login') }}" class="px-6 py-3 sm:p-0 sm:ml-5 block sm:inline-block text-brand-400 hover:text-white hover:bg-gray-900 sm:hover:bg-transparent no-underline">Sign in</a></li>
          @endauth
        </ul>
      </nav>
    </div>
  </div>
</div>
