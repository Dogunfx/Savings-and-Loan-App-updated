@extends('core::layouts.master')
@section('title')
    {{trans_choice('dashboard::general.dashboard',1)}}
@endsection
@section('content')
    <h1>Hello World</h1>

    <p>
        This view is ffff loaded from module: {!! config('dashboard.name') !!}
    </p>
@endsection
