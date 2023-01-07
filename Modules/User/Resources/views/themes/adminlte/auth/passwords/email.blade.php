@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.forgot_password",1)}}
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
                <p class="login-box-msg">{{trans_choice("user::general.enter_email_to_reset",1)}}</p>
                <form method="post" action="{{ route('password.email') }}">
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
                        <button class="btn btn-lg btn-primary btn-block">{{trans_choice("user::general.reset",1)}}</button>
                    </div>
                </form>
                <p class="mb-1">
                    <a href="{{ route('login') }}">{{trans_choice("user::general.back_to_login",1)}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
