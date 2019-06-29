<div class="p-16 bg-white border rounded-lg flex flex-col items-center justify-center text-center">
  <svg class="w-10 h-10 text-gray-400 fill-current self-center" viewBox="0 0 42 42" width="42" height="42" xmlns="http://www.w3.org/2000/svg">
    <path d="M12.667 17.222V5.666c0-1.84-1.493-3.333-3.334-3.333C7.496 2.333 6 3.825 6 5.666v23.337C6 35.628 11.373 41 18 41c3.687 0 6.984-1.66 9.185-4.276l11.712-11.712c1.17-1.168.85-2.572-.712-3.133l-.585-.21c-1.565-.563-3.778-.068-4.948 1.102L30 25.424V4c0-1.65-1.343-3-3-3-1.653 0-3 1.343-3 3v11.333l-11.333 1.89z" />
  </svg>

  <div class="mt-8 text-lg text-gray-600">{{ $text ?? 'You haven\'t created any concerts!' }}</div>

  @if (!$hideCreateButton)
    <div class="mt-8">
      <a href="{{ route('backstage.concerts.create') }}" class="inline-block btn">Create a concert</a>
    </div>
  @endif
</div>
