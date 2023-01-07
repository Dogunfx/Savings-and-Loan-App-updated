@extends('core::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('loan.name') !!}
    </p>
@stop
@section('scripts')
    <script src="{{ asset('assets/dist/js/loan.js') }}"></script>
@stop