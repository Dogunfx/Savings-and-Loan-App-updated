@extends('core::layouts.master')
@section('title')
    {{__('user::general.Two Factor Authentication')}}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{__('user::general.Two Factor Authentication')}} / <strong
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
                        <li class="breadcrumb-item active">{{__('user::general.Two Factor Authentication')}} / <strong
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
                        <h4 class="card-title">{{__('user::general.Two Factor Authentication')}}</h4>
                    </div>
                    <div class="card-body">
                        @if($user->enable_google2fa==1 && !empty($user->google2fa_secret))
                            <form method="post"
                                  action="{{url('user/profile/two_factor/disable')}}"
                                  class="form">
                                {{csrf_field()}}
                                <div class="form-group" id="">
                                    <div class="form-group">
                                        <label for="password"
                                               class="control-label">{{__('user::general.Current Password')}}</label>
                                        <input type="password" name="password" id="password" value=""
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               required autocomplete="off">
                                        @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit"
                                        class="btn btn-danger float-right">{{__('user::general.Disable')}}</button>

                            </form>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <p>{{__('user::general.Set up your two factor authentication by scanning the barcode below. Alternatively, you can use the code')}}</p>
                                    <div class="img-center text-center">
                                        <img src="{{ $qr_image }}" class="">
                                        <h4 class="text-center">{{ $secret }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form method="post"
                                          action="{{url('user/profile/two_factor/enable')}}"
                                          class="form">
                                        {{csrf_field()}}

                                        <div class="form-group" id="">
                                            <label for="google_app_code"
                                                   class="">{{__('user::general.Google App Code')}}</label>
                                            <input type="number" name="google_app_code"
                                                   class="form-control"
                                                   id="google_app_code" required>
                                        </div>
                                        <button type="submit"
                                                class="btn btn-primary float-right">{{__('user::general.Enable')}}</button>

                                    </form>
                                </div>
                            </div>

                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection
