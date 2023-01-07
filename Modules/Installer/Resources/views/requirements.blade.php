@extends('installer::layouts.master')

@section('title', trans('installer::general.install_requirements'))
@section('content')

    <ul class="list-group">
        @foreach($requirements as $extension => $enabled)
            <li class="list-group-item">
                {{ $extension }}
                @if($enabled)
                    <span class="badge badge1"><i class="fa fa-check"></i></span>
                @else
                    <span class="badge badge2"><i class="fa fa-times"></i></span>
                @endif
            </li>
        @endforeach
    </ul>

    <div class="form-group">
        @if($next)
            <a href="{{ url('install/permissions') }}"
               class="btn btn-info pull-right">{{ trans('installer::general.install_next') }}</a>
        @else
            <div class="alert alert-danger">{{ trans('installer::general.install_check') }}</div>
            <a class="btn btn-info pull-right" href="{{ Request::url() }}">
                {{trans('installer::general.refresh')}}
                <i class="fa fa-refresh"></i></a>
        @endif

    </div>
@endsection