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
