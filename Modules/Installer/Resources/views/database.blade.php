@extends('installer::layouts.master')

@section('title', trans('installer::general.install_database'))
@section('content')
    <form method="post" action="{{ url('install/database') }}">
        {{csrf_field()}}
        <div class="form-group">
            <label for="host" class="control-label">{{trans_choice('installer::general.install_host',1)}}</label>
            <input type="text" name="host" value="{{ env('DB_HOST') }}" id="host" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="username"
                   class="control-label">{{trans_choice('installer::general.install_username',1)}}</label>
            <input type="text" name="username" value="{{ env('DB_USERNAME') }}" id="username" class="form-control"
                   required>
        </div>
        <div class="form-group">
            <label for="password"
                   class="control-label">{{trans_choice('installer::general.install_password',1)}}</label>
            <input type="text" name="password" value="{{ env('DB_PASSWORD') }}" id="password" class="form-control">
        </div>
        <div class="form-group">
            <label for="name" class="control-label">{{trans_choice('installer::general.install_name',1)}}</label>
            <input type="text" name="name" value="{{ env('DB_DATABASE') }}" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="port" class="control-label">{{trans_choice('installer::general.install_port',1)}}</label>
            <input type="text" name="port" value="{{ env('DB_PORT') }}" id="port" class="form-control" required>
        </div>
        <div class="form-group">

            <button type="submit"
                    class="btn btn-info pull-right"> {{ trans('installer::general.install_next') }}</button>

        </div>
    </form>
@endsection