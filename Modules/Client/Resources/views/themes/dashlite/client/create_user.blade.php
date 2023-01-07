@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.user',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.user',1) }}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <a href="#" onclick="window.history.back()"
                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                    </a>

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <form method="post" action="{{ url('client/'.$client->id.'/user/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="existing"
                                       class="control-label">{{trans('client::general.existing_user')}}</label>
                                <select class="form-control" name="existing" id="existing" v-model="existing" required>
                                    <option value=""></option>
                                    <option value="1" selected>{{trans_choice("core::general.yes",1)}}</option>
                                    <option value="0">{{trans_choice("core::general.no",1)}}</option>
                                </select>
                                @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4" v-if="existing==1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role"
                                       class="control-label">{{trans_choice('user::general.user',1)}}</label>
                                <v-select label="full_name" :options="users" :reduce="user => user.id"
                                          v-model="user_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                class="vs__search @error('user_id') is-invalid @enderror"
                                                :required="!user_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="user_id" v-model="user_id">
                                @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div  v-if="existing==0">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name"
                                           class="control-label">{{trans('user::general.first_name')}}</label>
                                    <input type="text" name="first_name" v-model="first_name"
                                           id="first_name"
                                           class="form-control @error('first_name') is-invalid @enderror" required>
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
                                           class="form-control @error('last_name') is-invalid @enderror" required>
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
                                    <select class="form-control @error('gender') is-invalid @enderror" name="gender"
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password"
                                           class="control-label">{{trans_choice('user::general.password',1)}}</label>
                                    <input type="password" name="password" id="password" v-model="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required>
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
                                           class="control-label">{{trans_choice('user::general.password_confirmation',1)}}</label>
                                    <input type="password" name="password_confirmation"
                                           v-model="password_confirmation" id="password_confirmation"
                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                           required>
                                    @error('password_confirmation')
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
                                    <label for="address"
                                           class="control-label">{{trans('user::general.address')}}</label>
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
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes"
                                           class="control-label">{{trans_choice('core::general.note',2)}}</label>
                                    <textarea type="text" name="notes" id="notes" v-model="notes"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              rows="3"></textarea>
                                    @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div><!-- .card-preview -->
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                existing: "{{old('existing_user',1)}}",
                user_id: "{{old('country_id')}}",
                country_id: "{{old('country_id')}}",
                first_name: "{{old('first_name')}}",
                last_name: "{{old('last_name')}}",
                phone: "{{old('phone')}}",
                email: "{{old('email')}}",
                gender: "{{old('gender')}}",
                notes: "{{old('notes')}}",
                address: "{{old('address')}}",
                photo: "{{old('photo')}}",
                selected_roles: [],
                password: "",
                password_confirmation: "",
                users: {!! json_encode($users) !!},
            }
        })
    </script>
@endsection