@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.reset",1)}} {{trans_choice("user::general.password",1)}}
@endsection
@section('content')
    <div class="nk-block nk-block-middle nk-auth-body bg-white">
        <div class="brand-logo pb-5">
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
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <div class="nk-block-des">
                    <p>{{trans_choice("user::general.reset",1)}} {{trans_choice("user::general.password",1)}}</p>
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <form method="post" action="{{ route('password.update') }}">
            {{csrf_field()}}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <div class="form-label-group">
                    <label class="form-label" for="email">{{trans_choice("user::general.email",1)}}</label>
                </div>
                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                       name="email"
                       placeholder="{{trans_choice("user::general.email",1)}}" value="{{ old('email') }}" required
                       autocomplete="email" id="email" autofocus>
                @error('email')
                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                @enderror
            </div>
            <div class="form-group">
                <div class="form-label-group">
                    <label class="form-label" for="password">{{trans_choice("user::general.password",1)}}</label>
                </div>
                <div class="form-control-wrap">
                    <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                    </a>
                    <input type="password" name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="{{trans_choice("user::general.password",1)}}" required
                           autocomplete="off" id="password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="form-label-group">
                    <label class="form-label" for="password_confirmation">{{trans_choice("user::general.password_confirmation",1)}}</label>
                </div>
                <div class="form-control-wrap">
                    <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password_confirmation">
                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                    </a>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                           placeholder="{{trans_choice("user::general.password_confirmation",1)}}" required id="password_confirmation">
                    @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-lg btn-primary btn-block">{{trans_choice("user::general.reset",1)}}</button>
            </div>
        </form>
        <div class="form-note-s2 pt-4">
            <a href="{{ route('login') }}">{{trans_choice("user::general.back_to_login",1)}}</a>
        </div>
    </div>
@endsection
