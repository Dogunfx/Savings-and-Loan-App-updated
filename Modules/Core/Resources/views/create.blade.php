@extends('layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded jj from module: {!! config('core.name') !!}
    </p>
@stop
