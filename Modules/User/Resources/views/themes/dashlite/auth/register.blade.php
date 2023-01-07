@extends('core::layouts.auth')
@section("title")
    {{trans_choice("user::general.register",1)}}
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
                    <p>{{trans_choice("user::general.register_msg",1)}}</p>
                </div>
            </div>
        </div><!-- .nk-block-head -->
        <form method="post" action="{{ route('password.update') }}">
            {{csrf_field()}}
            <div class="form-group has-feedback @error('branch_id') has-error @enderror">
                <div class="form-label-group">
                    <label class="form-label" for="email">{{trans_choice("core::general.branch",1)}}</label>
                </div>
                <select class="form-control select2" name="branch_id" id="branch_id" required>
                    <option value="" disabled selected>{{trans_choice("core::general.select",1)}}</option>
                    @foreach($branches as $key)
                        <option value="{{$key->id}}">{{$key->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group has-feedback @error('first_name') has-error @enderror">
                <div class="form-label-group">
                    <label class="form-label" for="first_name">{{trans_choice("user::general.first_name",1)}}</label>
                </div>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" id="first_name"
                       placeholder="{{trans_choice("user::general.first_name",1)}}" value="{{ old('first_name') }}"
                       required
                       autocomplete="first_name" autofocus>
                @error('first_name')
                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                @enderror
            </div>
            <div class="form-group has-feedback @error('last_name') has-error @enderror">
                <div class="form-label-group">
                    <label class="form-label" for="last_name">{{trans_choice("user::general.last_name",1)}}</label>
                </div>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" id="last_name"
                       placeholder="{{trans_choice("user::general.last_name",1)}}" value="{{ old('last_name') }}"
                       required
                       autocomplete="last_name">
                @error('last_name')
                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                @enderror
            </div>
            <div class="form-group has-feedback @error('gender') has-error @enderror">
                <div class="form-label-group">
                    <label class="form-label" for="gender">{{trans_choice("user::general.gender",1)}}</label>
                </div>
                <select class="form-control @error('gender') is-invalid @enderror" name="gender" id="gender" required>
                    <option value="" disabled selected>{{trans_choice("user::general.gender",1)}}</option>
                    <option value="male">{{trans_choice("user::general.male",1)}}</option>
                    <option value="female">{{trans_choice("user::general.female",1)}}</option>
                </select>
                @error('gender')
                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                @enderror
            </div>
            <div class="form-group has-feedback @error('phone') has-error @enderror">
                <div class="form-label-group">
                    <label class="form-label" for="phone">{{trans_choice("user::general.phone",1)}}</label>
                </div>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone"
                       placeholder="{{trans_choice("user::general.phone",1)}}" value="{{ old('phone') }}"
                       required
                       autocomplete="phone">
                @error('phone')
                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                @enderror
            </div>
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
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           {{ old('agree') ? 'checked' : '' }} id="agree">
                    <label class="custom-control-label"
                           for="agree">{!!  trans_choice("user::general.agree_to_terms",1)!!}</label>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-lg btn-primary btn-block">{{trans_choice("user::general.register",1)}}</button>
            </div>
        </form>
        <div class="form-note-s2 pt-4">
            <a href="{{ route('login') }}">{{trans_choice("user::general.back_to_login",1)}}</a>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $.validator.addMethod('mypassword', function (value, element) {
                return this.optional(element) || (value.match(/[a-zA-Z]/) && value.match(/[0-9]/));
            },
            'Password must contain at least one numeric and one alphabetic character.');
        $(".register-form").validate({
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                mobile: {
                    required: true
                },
                gender: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 6,
                    mypassword: true
                },
                password_confirmation: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection