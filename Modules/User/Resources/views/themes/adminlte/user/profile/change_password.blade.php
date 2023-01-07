@extends('core::layouts.master')
@section('title')
    {{__('user::general.Change Password')}}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{__('user::general.Change Password')}} / <strong
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
                        <li class="breadcrumb-item active">{{__('user::general.Change Password')}} / <strong
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

                <form method="post" action="{{ url('user/profile/update_password') }}">
                    {{csrf_field()}}
                    <div class="card card-bordered card-preview">
                        <div class="card-header">
                            <h4 class="card-title">{{__('user::general.Change Password')}}</h4>

                        </div>
                        <div class="card-body">

                            <div class="row gy-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="current_password"
                                               class="control-label">{{__('user::general.Current Password')}}</label>
                                        <input type="password" name="current_password" id="current_password" value=""
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               required autocomplete="off">
                                        @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password"
                                               class="control-label">{{__('user::general.Password')}}</label>
                                        <input type="password" name="password" id="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               required autocomplete="off">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation"
                                               class="control-label">{{__('user::general.Confirm Password')}}</label>
                                        <input type="password" name="password_confirmation"
                                               id="password_confirmation"
                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                               required autocomplete="off">
                                        @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                             </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top ">
                            <button type="submit"
                                    class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection
