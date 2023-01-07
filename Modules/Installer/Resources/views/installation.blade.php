@extends('installer::layouts.master')

@section('title', trans('installer::general.install_installation'))
@section('content')
    <p class="paragraph">{{ trans('installer::general.install_msg') }}</p>
    <form method="post" action="{{ url('install/installation') }}">
        {{csrf_field()}}
        <div class="form-group">
            <button type="submit"
                    class="btn btn-info pull-right"> {{ trans('installer::general.install_install') }}</button>
        </div>
    </form>
@endsection