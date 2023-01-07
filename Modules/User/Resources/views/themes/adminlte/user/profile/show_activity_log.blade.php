@extends('core::layouts.master')
@section('title')
    {{ __('user::general.Activity Logs') }}
@endsection
@section('styles')
@stop

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ __('user::general.Activity Logs') }} / <strong
                                class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong>
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
                        <li class="breadcrumb-item active">{{ __('user::general.Activity Logs') }} / <strong
                                    class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong>
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-3">
                @include('user::themes.adminlte.user.profile.user_profile_menu')
            </div>
            <!-- /.col -->
            <div class="col-md-9">


                <div class="card card-bordered card-preview">

                    <div class="card-header">
                        <h4 class="card-title">{{ __('user::general.Activity Logs') }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>{{trans_choice('core::general.description',1)}}</th>
                                <td>
                                    {{$activity_log->description}}
                                </td>
                            </tr>
                            @foreach($activity_log->properties as $key=>$value)
                                <tr>
                                    <th>{{$key}}</th>
                                    <td>
                                        {{$value}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <th>{{trans_choice('core::general.created_at',1)}}</th>
                                <td>
                                    {{$activity_log->created_at}}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection
