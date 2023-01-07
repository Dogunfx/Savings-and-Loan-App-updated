@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.user',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.user',1) }}
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
                        <li class="breadcrumb-item"><a
                                    href="{{url('user')}}">{{ trans_choice('user::general.user',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.user',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('user/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
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
                                <label for="role"
                                       class="control-label">{{trans_choice('user::general.role',2)}}</label>
                                <v-select label="name" :options="roles" :reduce="role => role.id"
                                          v-model="selected_roles" multiple>
                                    <template #search="{attributes, events}">
                                        <input
                                                class="vs__search @error('roles') is-invalid @enderror"
                                                :required="!selected_roles"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="roles[]" v-model="selected_roles">
                                @error('roles')
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
                    @foreach($custom_fields as $custom_field)
                        <?php
                        $field = custom_field_build_form_field($custom_field);
                        ?>
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    @if($custom_field->type=='radio')
                                        <label class="control-label"
                                               for="field_{{$custom_field->id}}">{{$field['label']}}</label>
                                        {!! $field['html'] !!}
                                    @else
                                        <label class="control-label"
                                               for="field_{{$custom_field->id}}">{{$field['label']}}</label>
                                        {!! $field['html'] !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div><!-- .card-preview -->
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
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
                roles: {!! json_encode($roles) !!},
            }
        })
    </script>
@endsection