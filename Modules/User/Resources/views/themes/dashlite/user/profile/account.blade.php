@extends('core::layouts.master')
@section('title')
    {{ $user->first_name }} {{ $user->last_name }}
@endsection
@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{ __('user::general.Profile') }} / <strong
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

                <form method="post" action="{{ url('user/profile/update_profile') }}"
                      enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="card card-bordered card-preview">

                        <div class="card-inner">
                            <div class="nk-block-head nk-block-head-lg">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">{{ __('user::general.Account Information') }}</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name"
                                               class="control-label">{{trans('user::general.first_name')}}</label>
                                        <input type="text" name="first_name" v-model="first_name"
                                               id="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               required>
                                        @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name"
                                               class="control-label">{{trans('user::general.last_name')}}</label>
                                        <input type="text" name="last_name" v-model="last_name"
                                               id="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror"
                                               required>
                                        @error('last_name')
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
                                        <label for="gender" class="control-label">{{trans('user::general.gender')}}</label>
                                        <select class="form-control @error('gender') is-invalid @enderror"
                                                name="gender"
                                                id="gender" v-model="gender">
                                            <option value="male">{{trans_choice("user::general.male",1)}}</option>
                                            <option value="female">{{trans_choice("user::general.female",1)}}</option>
                                        </select>
                                        @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="control-label">{{trans('user::general.phone')}}</label>
                                        <input type="text" name="phone" id="phone" v-model="phone"
                                               class="form-control @error('phone') is-invalid @enderror">
                                        @error('phone')
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
                                        <label for="photo"
                                               class="control-label">{{trans_choice('user::general.photo',1)}}</label>
                                        <input type="file" name="photo" id="photo"
                                               class="form-control @error('photo') is-invalid @enderror">
                                        @error('photo')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"
                                               class="control-label">{{trans_choice('user::general.email',1)}}</label>
                                        <input type="email" name="email" id="email" v-model="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               required>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row gy-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address" class="control-label">{{trans('user::general.address')}}</label>
                                        <textarea type="text" name="address" id="address" v-model="address"
                                                  class="form-control @error('address') is-invalid @enderror"
                                                  rows="3"></textarea>
                                        @error('address')
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
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                country_id: "{{old('country_id',$user->country_id)}}",
                first_name: "{{old('first_name',$user->first_name)}}",
                last_name: "{{old('last_name',$user->last_name)}}",
                phone: "{{old('phone',$user->phone)}}",
                email: "{{old('email',$user->email)}}",
                gender: "{{old('gender',$user->gender)}}",
                notes: "{{old('notes',$user->notes)}}",
                address: "{{old('address',$user->address)}}",
                password: "",
                password_confirmation: "",
            }
        })
    </script>
@endsection
