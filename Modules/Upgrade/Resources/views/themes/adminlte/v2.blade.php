@extends('core::layouts.master')
@section('title')
    {{ trans_choice('setting::general.system',1) }} {{ trans_choice('upgrade::general.upgrade',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('setting::general.system',1) }} {{ trans_choice('upgrade::general.upgrade',1) }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('setting::general.system',1) }} {{ trans_choice('upgrade::general.upgrade',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">

            <div class="card-body">
                @if( $upgrade_done)
                    <div class="alert alert-danger">Upgrade already done</div>
                @else
                    <p>Please fill your v2 database details below</p>
                    <div class="alert alert-info">
                        The upgrade will be done in the background. You will be notified via
                        email when its complete.
                    </div>
                    <form method="post">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="host"
                                   class="control-label">{{trans_choice('installer::general.install_host',1)}}</label>
                            <input type="text" name="host" value="{{ env('OLD_DB_HOST') }}" id="host"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="username"
                                   class="control-label">{{trans_choice('installer::general.install_username',1)}}</label>
                            <input type="text" name="username" value="{{ env('OLD_DB_USERNAME') }}" id="username"
                                   class="form-control"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="password"
                                   class="control-label">{{trans_choice('installer::general.install_password',1)}}</label>
                            <input type="text" name="password" value="{{ env('OLD_DB_PASSWORD') }}" id="password"
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="name"
                                   class="control-label">{{trans_choice('installer::general.install_name',1)}}</label>
                            <input type="text" name="name" value="{{ env('OLD_DB_DATABASE') }}" id="name"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="port"
                                   class="control-label">{{trans_choice('installer::general.install_port',1)}}</label>
                            <input type="text" name="port" value="{{ env('OLD_DB_PORT') }}" id="port"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-info btn-sm" type="submit">
                                {{ trans_choice('upgrade::general.upgrade',1) }}
                            </button>
                        </div>
                    </form>
                @endif

            </div>
            <!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection