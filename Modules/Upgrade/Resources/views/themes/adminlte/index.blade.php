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
        <div class="card card-bordered card-preview">
            <div class="card-body">
                <div class="alert alert-info">Upgrading from v2? <a href="{{url('upgrade/v2')}}">Click Here</a></div>
                <p>
                    {{ trans_choice('upgrade::general.current',1) }} {{ trans_choice('core::general.version',1) }}:
                    <b>{{\Modules\Setting\Entities\Setting::where('setting_key','core.system_version')->first()->setting_value}}</b>
                </p>
                <p>{{ trans_choice('upgrade::general.server',1) }} {{ trans_choice('core::general.version',1) }}
                    :
                    <span id="server_version"></span>
                </p>
                <div id="update_message"></div>
            </div>
            <div class="card-footer border-top ">
                <button type="button"
                        class="btn btn-primary  float-right" id="check_update">{{ trans_choice('upgrade::general.check',1) }} {{ trans_choice('core::general.version',1) }}</button>
            </div>
        </div><!-- .card-preview -->

    </section>
@endsection
@section('scripts')

@endsection