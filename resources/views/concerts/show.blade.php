@extends('layouts.base')

@section('title', $concert->title)
@section('show-header', false)

@section('head')
  <script src="https://checkout.stripe.com/checkout.js"></script>
@endsection

@section('content-full')
  <div class="h-full min-h-full flex sm:bg-gray-100">
    @if ($concert->hasPoster())
      @include('concerts.partials.with-poster', $concert)
    @else
      @include('concerts.partials.without-poster', $concert)
    @endif
  </div>
@endsection
