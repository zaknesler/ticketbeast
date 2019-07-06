@extends('layouts.base')

@section('title', 'Edit Concert')
@section('show-header', true)
@section('show-footer', true)

@section('content-full')
  <div class="bg-gray-100">
    <div class="w-full bg-white border-b">
      <div class="px-6 py-4 mx-auto max-w-4xl">
        <h3 class="font-semibold text-gray-700">Edit concert</h3>
      </div>
    </div>

    <div class="min-h-full h-full w-full">
      <div class="p-6 mx-auto max-w-4xl">
        <form class="block" action="{{ route('backstage.concerts.update', $concert) }}" method="post" enctype="multipart/form-data">
          @csrf
          @method('patch')

          <section class="sm:flex justify-between">
            <div class="sm:w-1/3">
              <div class="text-gray-900">Concert Details</div>

              <div class="mt-3 text-sm leading-relaxed text-gray-600">
                <p>Tell us who's playing!</p>
                <p class="mt-2">Include the headliner in the concert title and any additional bands in the subtitle.</p>
              </div>
            </div>

            <div class="mt-6 sm:mt-0 sm:ml-6 sm:w-3/5">
              <div>
                <label>
                  <span class="text-sm font-medium text-gray-800 {{ $errors->first('title', 'text-red-700') }}">Title</span>
                  <input
                    autofocus
                    required
                    tabindex="1"
                    type="text"
                    name="title"
                    value="{{ old('title', $concert->title) }}"
                    class="mt-1 form-input block w-full {{ $errors->first('title', 'border-red-500') }}"
                    placeholder="The Headliners"
                  />

                  @if ($errors->has('title'))
                    <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('title') }}</div>
                  @endif
                </label>
              </div>

              <div class="mt-4">
                <label>
                  <span class="text-sm font-medium text-gray-800 {{ $errors->first('subtitle', 'text-red-700') }}">Subtitle <span class="text-xs text-gray-500">(Optional)</span></span>
                  <input
                    tabindex="2"
                    type="text"
                    name="subtitle"
                    value="{{ old('subtitle', $concert->subtitle) }}"
                    class="mt-1 form-input block w-full {{ $errors->first('subtitle', 'border-red-500') }}"
                    placeholder="with The Openers"
                  />

                  @if ($errors->has('subtitle'))
                    <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('subtitle') }}</div>
                  @endif
                </label>
              </div>

              <div class="mt-4">
                <label>
                  <span class="text-sm font-medium text-gray-800 {{ $errors->first('additional_information', 'text-red-700') }}">Additional Information <span class="text-xs text-gray-500">(Optional)</span></span>
                  <textarea
                    tabindex="3"
                    name="additional_information"
                    class="mt-1 form-textarea block w-full min-h-32 {{ $errors->first('additional_information', 'border-red-500') }}"
                    placeholder="This concert is 18+."
                  >{{ old('additional_information', $concert->additional_information) }}</textarea>

                  @if ($errors->has('additional_information'))
                    <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('additional_information') }}</div>
                  @endif
                </label>
              </div>
            </div>
          </section>

          <div class="my-6 w-full h-px bg-gray-300"></div>

          <section class="sm:flex justify-between">
            <div class="sm:w-1/3">
              <div class="font-semibold text-gray-800">Concert Poster</div>

              <div class="mt-3 text-sm leading-relaxed text-gray-600">
                <p>Have a sweet poster for this concert? Upload it and we'll display it on the checkout page.</p>
                <p class="mt-2">The poster image must have an aspect ratio of 8.5 x 11.</p>
              </div>
            </div>

            <div class="mt-6 sm:mt-0 sm:ml-6 sm:w-3/5">
              <div>
                <label>
                  <span class="text-sm font-medium text-gray-800 {{ $errors->first('poster_image', 'text-red-700') }}">Poster Image <span class="text-xs text-gray-500">(Optional)</span></span>
                  <input
                    tabindex="4"
                    type="file"
                    name="poster_image"
                    value="{{ old('poster_image') }}"
                    class="mt-1 form-input form-file px-2 text-sm block w-full {{ $errors->first('poster_image', 'border-red-500') }}"
                  />

                  @if ($errors->has('poster_image'))
                    <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('poster_image') }}</div>
                  @endif
                </label>
              </div>
            </div>
          </section>

          <div class="my-6 w-full h-px bg-gray-300"></div>

          <section class="sm:flex justify-between">
            <div class="sm:w-1/3">
              <div class="text-gray-900">Date & Time</div>

              <div class="mt-3 text-sm leading-relaxed text-gray-600">
                <p>Let us know when the show starts! The starting time should be the time doors open and ticketholders are allowed to enter.</p>
              </div>
            </div>

            <div class="mt-6 sm:mt-0 sm:ml-6 sm:w-3/5">
              <div class="flex justify-between">
                <div class="flex-1">
                  <label>
                    <span class="text-sm font-medium text-gray-800 {{ $errors->first('date', 'text-red-700') }}">Date</span>
                    <input
                      required
                      tabindex="5"
                      type="text"
                      name="date"
                      value="{{ old('date', $concert->date->format('Y-m-d')) }}"
                      class="mt-1 form-input block w-full {{ $errors->first('date', 'border-red-500') }}"
                      placeholder="yyyy-mm-dd"
                    />

                    @if ($errors->has('date'))
                      <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('date') }}</div>
                    @endif
                  </label>
                </div>

                <div class="ml-6 flex-1">
                  <label>
                    <span class="text-sm font-medium text-gray-800 {{ $errors->first('time', 'text-red-700') }}">Start time</span>
                    <input
                      required
                      tabindex="6"
                      type="text"
                      name="time"
                      value="{{ old('time', $concert->date->format('g:ia')) }}"
                      class="mt-1 form-input block w-full {{ $errors->first('time', 'border-red-500') }}"
                      placeholder="8:30pm"
                    />

                    @if ($errors->has('time'))
                      <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('time') }}</div>
                    @endif
                  </label>
                </div>
              </div>
            </div>
          </section>

          <div class="my-6 w-full h-px bg-gray-300"></div>

          <section class="sm:flex justify-between">
            <div class="sm:w-1/3">
              <div class="text-gray-900">Venue Information</div>

              <div class="mt-3 text-sm leading-relaxed text-gray-600">
                <p>Where's the show at? Let fans know what the venue is and where they should be going.</p>
              </div>
            </div>

            <div class="mt-6 sm:mt-0 sm:ml-6 sm:w-3/5">
              <div>
                <label>
                  <span class="text-sm font-medium text-gray-800 {{ $errors->first('venue', 'text-red-700') }}">Venue</span>
                  <input
                    required
                    tabindex="7"
                    type="text"
                    name="venue"
                    value="{{ old('venue', $concert->venue) }}"
                    class="mt-1 form-input block w-full {{ $errors->first('venue', 'border-red-500') }}"
                    placeholder="The Mosh Pit"
                  />

                  @if ($errors->has('venue'))
                    <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('venue') }}</div>
                  @endif
                </label>
              </div>

              <div class="mt-4">
                <label>
                  <span class="text-sm font-medium text-gray-800 {{ $errors->first('venue_address', 'text-red-700') }}">Street Address</span>
                  <input
                    required
                    tabindex="8"
                    type="text"
                    name="venue_address"
                    value="{{ old('venue_address', $concert->venue_address) }}"
                    class="mt-1 form-input block w-full {{ $errors->first('venue_address', 'border-red-500') }}"
                    placeholder="123 Rock Lane"
                  />

                  @if ($errors->has('venue_address'))
                    <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('venue_address') }}</div>
                  @endif
                </label>
              </div>

              <div class="mt-4 md:flex justify-between">
                <div>
                  <label>
                    <span class="text-sm font-medium text-gray-800 {{ $errors->first('city', 'text-red-700') }}">City</span>
                    <input
                      required
                      tabindex="9"
                      type="text"
                      name="city"
                      value="{{ old('city', $concert->city) }}"
                      class="mt-1 form-input block w-full {{ $errors->first('city', 'border-red-500') }}"
                      placeholder="Beverly Hills"
                    />

                    @if ($errors->has('city'))
                      <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('city') }}</div>
                    @endif
                  </label>
                </div>

                <div class="mt-4 md:mt-0 md:ml-6 flex justify-between">
                  <div class="flex-1">
                    <label>
                      <span class="text-sm font-medium text-gray-800 {{ $errors->first('state', 'text-red-700') }}">State/Province</span>
                      <input
                        required
                        tabindex="10"
                        type="text"
                        name="state"
                        value="{{ old('state', $concert->state) }}"
                        class="mt-1 form-input block w-full {{ $errors->first('state', 'border-red-500') }}"
                        placeholder="CA"
                      />

                      @if ($errors->has('state'))
                        <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('state') }}</div>
                      @endif
                    </label>
                  </div>

                  <div class="ml-6 flex-1">
                    <label>
                      <span class="text-sm font-medium text-gray-800 {{ $errors->first('zip', 'text-red-700') }}">ZIP</span>
                      <input
                        required
                        tabindex="11"
                        type="text"
                        name="zip"
                        value="{{ old('zip', $concert->zip) }}"
                        class="mt-1 form-input block w-full {{ $errors->first('zip', 'border-red-500') }}"
                        placeholder="90210"
                      />

                      @if ($errors->has('zip'))
                        <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('zip') }}</div>
                      @endif
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <div class="my-6 w-full h-px bg-gray-300"></div>

          <section class="sm:flex justify-between">
            <div class="sm:w-1/3">
              <div class="text-gray-900">Tickets & Pricing</div>

              <div class="mt-3 text-sm leading-relaxed text-gray-600">
                <p>Set the ticket price and availability, but don't forget that rockers don't want to break the bank!</p>
              </div>
            </div>

            <div class="mt-6 sm:mt-0 sm:ml-6 sm:w-3/5">
              <div class="flex justify-between">
                <div class="flex-1">
                  <label>
                    <span class="text-sm font-medium text-gray-800 {{ $errors->first('ticket_price', 'text-red-700') }}">Price</span>
                    <input
                      required
                      tabindex="12"
                      type="text"
                      name="ticket_price"
                      value="{{ old('ticket_price', $concert->ticket_price_in_dollars) }}"
                      class="mt-1 form-input block w-full {{ $errors->first('ticket_price', 'border-red-500') }}"
                      placeholder="20.00"
                      min="5"
                    />

                    @if ($errors->has('ticket_price'))
                      <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('ticket_price') }}</div>
                    @endif
                  </label>
                </div>

                <div class="ml-6 flex-1">
                  <label>
                    <span class="text-sm font-medium text-gray-800 {{ $errors->first('ticket_quantity', 'text-red-700') }}">Tickets Available</span>
                    <input
                      required
                      tabindex="13"
                      type="number"
                      name="ticket_quantity"
                      value="{{ old('ticket_quantity', $concert->ticket_quantity) }}"
                      class="mt-1 form-input block w-full {{ $errors->first('ticket_quantity', 'border-red-500') }}"
                      placeholder="250"
                      min="1"
                    />

                    @if ($errors->has('ticket_quantity'))
                      <div class="px-3 py-2 mt-2 text-xs font-semibold border border-red-200 bg-red-100 text-red-700 rounded-lg">{{ $errors->first('ticket_quantity') }}</div>
                    @endif
                  </label>
                </div>
              </div>
            </div>
          </section>

          <div class="my-6 w-full h-px bg-gray-300"></div>

          <div class="text-right">
            <button tabindex="14" class="btn">Update Concert</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
