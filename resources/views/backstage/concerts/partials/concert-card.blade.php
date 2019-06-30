<div class="p-3 w-full sm:w-1/2 md:w-1/3">
  <div class="p-6 bg-white border rounded-lg flex flex-col">
    <div class="flex-1">
      <div class="text-lg text-gray-800 font-semibold">{{ $concert->title }}</div>
      @if ($concert->subtitle)
        <div class="text-sm text-gray-600">{{ $concert->subtitle }}</div>
      @endif

      <div class="mt-3 flex items-center">
        <svg class="w-4 h-4 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
          <path d="M10 20S3 10.87 3 7a7 7 0 1 1 14 0c0 3.87-7 13-7 13zm0-11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
        </svg>

        <div class="ml-2 block text-sm text-gray-600">
          {{ $concert->venue }}
        </div>
      </div>

      <div class="mt-2 flex items-center">
        <svg class="w-4 h-4 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
          <path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z" />
        </svg>

        <time class="ml-2 block text-sm text-gray-600" datetime="{{ $concert->date }}">
          {{ $concert->formatted_date }}
        </time>
      </div>

      <div class="mt-2 flex items-center">
        <svg class="w-4 h-4 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
          <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z" />
        </svg>

        <time class="ml-2 block text-sm text-gray-600" datetime="{{ $concert->date }}">
          {{ $concert->formatted_start_time }}
        </time>
      </div>

      <div class="mt-2 flex items-center">
        <svg class="w-4 h-4 text-brand-500 fill-current flex-no-shrink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20">
          <path d="M20 12v5H0v-5a2 2 0 1 0 0-4V3h20v5a2 2 0 1 0 0 4zM3 5v10h14V5H3zm7 7.08l-2.92 2.04L8.1 10.7 5.27 8.56l3.56-.08L10 5.12l1.17 3.36 3.56.08-2.84 2.15 1.03 3.4L10 12.09z" />
        </svg>

        <div class="ml-2 block text-sm text-gray-600">
          {{ number_format($concert->ticket_quantity) }} {{ Str::plural('ticket', $concert->ticket_quantity) }}
        </div>
      </div>
    </div>

    @if (!$concert->isPublished())
      <div class="mt-6 text-sm flex items-center justify-end">
        <a class="btn px-3 py-1 inline-block bg-gray-200 hover:bg-gray-300 focus:bg-gray-300 text-gray-800" href="{{ route('backstage.concerts.edit', $concert) }}">Edit</a>

        <form action="{{ route('backstage.publishedConcerts.store') }}" method="POST" class="inline">
          @csrf
          <input type="hidden" name="concert_id" value="{{ $concert->id }}" />
          <button type="submit" class="ml-3 btn px-3 py-1 inline-block">Publish</button>
        </form>
      </div>
    @else
      <div class="mt-6 text-sm text-right">
        <a class="text-brand-600 hover:underline" href="{{ route('concerts.show', $concert) }}">Public Link</a>
        <a class="btn px-3 py-1 ml-3 inline-block bg-gray-200 hover:bg-gray-300 focus:bg-gray-300 text-gray-800" href="{{ route('backstage.concerts.orders.show', $concert) }}">Manage</a>
      </div>
    @endif
  </div>
</div>
