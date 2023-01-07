@extends('installer::layouts.master')

@section('title', trans('installer::general.install_complete'))
@section('content')
    <p class="paragraph">{!! trans('installer::general.install_complete_msg') !!}</p>
    <div class="form-group">
        <a href="{{ url('/') }}"
           class="btn btn-info pull-right">{{ trans('installer::general.install_login') }}</a>
    </div>
@endsection