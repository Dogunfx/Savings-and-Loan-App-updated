@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.verify_email",1)}}
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
                <p class="login-box-msg">{{trans_choice("user::general.verify_email",1)}}</p>

                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{trans_choice("user::general.email_link_sent",1)}}
                    </div>
                @endif
                {{trans_choice("user::general.check_verify_link",1)}}
                {{trans_choice("user::general.did_not_receive_email",1)}}, <a
                        href="{{ route('verification.resend') }}">{{trans_choice("user::general.request_another_link",1)}}</a>.

            </div>
        </div>
    </div>
@endsection