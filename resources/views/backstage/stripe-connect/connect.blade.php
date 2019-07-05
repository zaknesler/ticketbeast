@extends('layouts.base')

@section('title', 'Connect with Stripe')
@section('show-header', false)

@section('content-full')
  <div class="h-full flex">
    <div class="p-6 m-auto max-w-xs w-full">
      <div class="flex items-center justify-center">
        <a href="/" class="font-semibold text-gray-700 hover:text-gray-900 no-underline">{{ config('app.name') }}</a>
      </div>

      <div class="mt-6 mx-auto max-w-xs text-center">
        <div class="text-gray-600">
          Before proceeding, you must connect your Ticketbeast account with your Stripe account.
        </div>

        <div class="mt-6">
          <a href="{{ route('backstage.stripe-connect.authorize') }}" class="inline-block btn px-3 py-2 text-sm">
            Connect with Stripe &rarr;
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
