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
                        <table class="table  table-striped table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('general.description',1) }}</th>
                                <th>{{ trans_choice('general.created_at',1) }}</th>
                                <th>{{ trans_choice('general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key)
                                <tr>
                                    <td>{{$key->description}}</td>
                                    <td>{{$key->created_at->format('Y-m-d H:i:s')}}</td>
                                    <td>
                                        <a href="{{url('user/profile/activity_log/' . $key->id . '/show') }}"
                                           class="bg-white btn btn-sm btn-outline-light btn-icon btn-tooltip" title=""
                                           data-original-title="Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="card-footer">
                        {{$data->links()}}
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script>

    </script>
@endsection
