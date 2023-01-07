@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.login",1)}}
@endsection
@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{url('/')}}" class="logo-link text-center">
                @if(!empty($logo=\Modules\Setting\Entities\Setting::where('setting_key','core.company_logo')->first()->setting_value))
                    <img class="logo-light logo-img logo-img-lg" src="{{asset('storage/uploads/'.$logo)}}"
                         srcset="{{asset('storage/uploads/'.$logo)}} 2x"
                         alt="logo">
                @else
                    <h4>{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}</h4>
                @endif
            </a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">{{trans_choice("user::general.login_msg",1)}}</p>

                <form method="post" action="{{ route('login') }}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="email">{{trans_choice("user::general.email",1)}}</label>
                        </div>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                               name="email"
                               placeholder="{{trans_choice("user::general.email",1)}}" value="{{ old('email') }}"
                               required
                               autocomplete="email" id="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label"
                                   for="password">{{trans_choice("user::general.password",1)}}</label>
                            <a class="link link-primary link-sm" tabindex="-1" href="{{ route('password.request') }}">
                                {{trans_choice("user::general.forgot_password",1)}}
                            </a>
                        </div>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                               data-target="password">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" name="password"
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   placeholder="{{trans_choice("user::general.password",1)}}" required
                                   autocomplete="password" id="password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                   {{ old('remember') ? 'checked' : '' }} id="remember">
                            <label class="custom-control-label"
                                   for="remember">{{trans_choice("user::general.remember_me",1)}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-lg btn-primary btn-block">{{trans_choice("user::general.login",1)}}</button>
                    </div>
                </form>
                @if(\Modules\Setting\Entities\Setting::where('setting_key','user.enable_registration')->first()->setting_value=='yes')
                    <p class="mb-1">
                        <a href="{{ route('register') }}">{{trans_choice("user::general.register_msg",1)}}</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
