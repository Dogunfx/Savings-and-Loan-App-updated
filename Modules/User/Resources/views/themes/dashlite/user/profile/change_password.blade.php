@extends('core::layouts.master')
@section('title')
    {{__('user::general.Change Password')}}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{__('user::general.Change Password')}} / <strong
                            class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong></h3>

            </div>
            <div class="nk-block-head-content">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em
                            class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span></a>
            </div>
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="row">
            <div class="col-md-3">
                @include('user::themes.dashlite.user.profile.user_profile_menu')
            </div>
            <!-- /.col -->
            <div class="col-md-9">

                <form method="post" action="{{ url('user/profile/update_password') }}">
                    {{csrf_field()}}
                    <div class="card card-bordered card-preview">

                        <div class="card-inner">
                            <div class="nk-block-head nk-block-head-lg">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">{{__('user::general.Change Password')}}</h4>
                                    </div>

                                </div>
                            </div>
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
    </div>
@endsection
@section('scripts')

@endsection
