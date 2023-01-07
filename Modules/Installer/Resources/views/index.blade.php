@extends('installer::layouts.master')
@section('title', trans('installer::general.install'))
@section('content')
    <p class="paragraph">{{ trans('installer::general.install_welcome') }}</p>
    <div class="form-group">
        <a href="{{ url('install/requirements') }}"
           class="btn btn-info pull-right">{{ trans('installer::general.install_next') }}</a>
    </div>
@stop
