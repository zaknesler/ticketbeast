@extends('layouts.base')

@section('title', $concert->title)
@section('show-header', true)
@section('show-footer', true)

@section('content-full')
  <div class="h-full bg-gray-100">
    <div class="w-full bg-white border-b">
      <div class="px-6 py-4 mx-auto max-w-4xl sm:flex sm:items-baseline sm:justify-between">
        <div class="flex items-baseline">
          <h3 class="font-semibold text-gray-700">{{ $concert->title }}</h3>
          <span class="mx-2 text-xs text-gray-500">&ndash;</span>
          <div class="text-xs text-gray-600">{{ $concert->formatted_date }}</div>
        </div>

        <nav class="mt-6 sm:mt-0 text-sm text-gray-800">
          <a href="{{ route('backstage.concerts.orders.show', $concert) }}" class="mr-4 font-semibold hover:underline">Orders</a>
          <a href="{{ route('backstage.concerts.messages.create', $concert) }}" class="hover:underline">Message Attendees</a>
        </nav>
      </div>
    </div>

    <div class="p-6 mx-auto max-w-4xl">
      <section>
        <div class="text-gray-600">Overview</div>

        <div class="mt-2 border bg-white rounded-lg">
          <div class="p-4 border-b">
            <div>The show is {{ $concert->percentSoldOut() }}% sold out!</div>

            <div class="mt-4 h-2 rounded-full bg-gray-200 z-10">
              <div class="h-full bg-brand-500 z-20 rounded-full" style="width: {{ $concert->percentSoldOut() }}%"></div>
            </div>
          </div>

          <div class="sm:flex">
            <div class="p-4 sm:w-1/3 flex-1 border-b sm:border-b-0 sm:border-r">
              <div class="text-sm text-gray-600">Total Tickets Remaining</div>
              <div class="text-3xl font-bold text-gray-800">{{ $concert->ticketsRemaining() }}</div>
            </div>

            <div class="p-4 sm:w-1/3 flex-1 border-b sm:border-b-0 sm:border-r">
              <div class="text-sm text-gray-600">Total Tickets Sold</div>
              <div class="text-3xl font-bold text-gray-800">{{ $concert->ticketsSold() }}</div>
            </div>

            <div class="p-4 sm:w-1/3 flex-1">
              <div class="text-sm text-gray-600">Total Revenue</div>
              <div class="text-3xl font-bold text-gray-800">${{ number_format($concert->revenueInDollars(), 2) }}</div>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-12">
        <div class="text-gray-600">Recent Orders</div>

        @if (count($orders))
          <div class="mt-2 p-4 border bg-white rounded-lg">
            <div class="overflow-x-auto">
              <table class="w-full table">
                <thead>
                  <tr>
                    <th scope="col">Email</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Card</th>
                    <th scope="col">Purchased</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($orders as $order)
                    <tr>
                      <td>{{ $order->email }}</td>

                      <td>{{ $order->ticketQuantity() }}</td>

                      <td>${{ number_format($order->amount / 100, 2) }}</td>

                      <td class="text-gray-500 text-sm overflow-visible">
                        &bullet;&bullet;&bullet;&bullet;
                        <span class="text-sm font-mono text-gray-700">
                          {{ $order->card_last_four }}
                        </span>
                      </td>

                      <td class="text-sm text-gray-600">
                        {{ $order->created_at->format('M j, Y g:ia') }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @else
          <div class="mt-2">
            @include('backstage.concerts.partials.blank-state', ['hideCreateButton' => true, 'text' => 'There are no orders for this concert yet!'])
          </div>
        @endif
      </section>
    </div>
  </div>
@endsection
