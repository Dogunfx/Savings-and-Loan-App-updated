@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.client',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.client',1) }}</h3>
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
        <form method="post" action="{{ url('client/store') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="branch_id"
                                       class="control-label">{{trans_choice('core::general.branch',1)}}</label>
                                <select class="form-control @error('branch_id') is-invalid @enderror" name="branch_id"
                                        id="branch_id" v-model="branch_id" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($branches as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="external_id"
                                       class="control-label">{{trans_choice('core::general.external_id',1)}}</label>
                                <input type="text" name="external_id" v-model="external_id"
                                       id="external_id"
                                       class="form-control @error('external_id') is-invalid @enderror">
                                @error('external_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title_id"
                                       class="control-label">{{trans_choice('client::general.title',1)}}</label>
                                <select class="form-control @error('title_id') is-invalid @enderror" name="title_id"
                                        id="title_id" v-model="title_id">
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($titles as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('title_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="first_name"
                                       class="control-label">{{trans_choice('core::general.first_name',1)}}</label>
                                <input type="text" name="first_name" id="first_name" v-model="first_name"
                                       class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="last_name"
                                       class="control-label">{{trans_choice('core::general.last_name',1)}}</label>
                                <input type="text" name="last_name" id="last_name" v-model="last_name"
                                       class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="gender"
                                       class="control-label">{{trans_choice('core::general.gender',1)}}</label>
                                <select class="form-control @error('gender') is-invalid @enderror" name="gender"
                                        id="gender" v-model="gender">
                                    <option value="male">{{trans_choice("core::general.male",1)}}</option>
                                    <option value="female">{{trans_choice("core::general.female",1)}}</option>
                                </select>
                                @error('gender')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="marital_status"
                                       class="control-label">{{trans_choice('client::general.marital_status',1)}}</label>
                                <select class="form-control @error('marital_status') is-invalid @enderror"
                                        name="marital_status"
                                        id="marital_status" v-model="marital_status">
                                    <option value=""></option>
                                    <option value="single">{{trans_choice("client::general.single",1)}}</option>
                                    <option value="married">{{trans_choice("client::general.married",1)}}</option>
                                    <option value="divorced">{{trans_choice("client::general.divorced",1)}}</option>
                                    <option value="widowed">{{trans_choice("client::general.widowed",1)}}</option>
                                </select>
                                @error('marital_status')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="mobile"
                                       class="control-label">{{trans_choice('core::general.mobile',1)}}</label>
                                <input type="text" name="mobile" id="mobile" v-model="mobile"
                                       class="form-control @error('mobile') is-invalid @enderror">
                                @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="country_id"
                                       class="control-label">{{trans_choice('core::general.country',1)}}</label>
                                <select class="form-control @error('country_id') is-invalid @enderror" name="country_id"
                                        id="country_id" v-model="country_id">
                                    <option value=""></option>
                                    @foreach($countries as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="dob"
                                       class="control-label">{{trans_choice('core::general.dob',1)}}</label>
                                <flat-pickr
                                        v-model="dob"
                                        class="form-control  @error('dob') is-invalid @enderror"
                                        name="dob" required>
                                </flat-pickr>
                                @error('dob')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan_officer_id"
                                       class="control-label">{{trans_choice('core::general.staff',1)}}</label>
                                <select class="form-control @error('loan_officer_id') is-invalid @enderror"
                                        name="loan_officer_id"
                                        id="loan_officer_id" v-model="loan_officer_id">
                                    <option value=""></option>
                                    @foreach($users as $key)
                                        <option value="{{$key->id}}">{{$key->first_name}} {{$key->first_name}}</option>
                                    @endforeach
                                </select>
                                @error('loan_officer_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email"
                                       class="control-label">{{trans_choice('core::general.email',1)}}</label>
                                <input type="email" name="email" id="email" v-model="email"
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="profession_id"
                                       class="control-label">{{trans_choice('client::general.profession',1)}}</label>
                                <select class="form-control @error('profession_id') is-invalid @enderror"
                                        name="profession_id"
                                        id="profession_id" v-model="profession_id">
                                    <option value=""></option>
                                    @foreach($professions as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('profession_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="client_type_id"
                                       class="control-label">{{trans_choice('client::general.type',1)}}</label>
                                <select class="form-control @error('client_type_id') is-invalid @enderror"
                                        name="client_type_id"
                                        id="client_type_id" v-model="client_type_id">
                                    <option value=""></option>
                                    @foreach($client_types as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_type_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="photo"
                                       class="control-label">{{trans_choice('core::general.photo',1)}}</label>
                                <input type="file" name="photo" id="photo"
                                       class="form-control @error('photo') is-invalid @enderror">
                                @error('photo')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address"
                                       class="control-label">{{trans_choice('core::general.address',1)}}</label>
                                <textarea type="text" name="address" v-model="address"
                                          id="address"
                                          class="form-control @error('address') is-invalid @enderror">
                                </textarea>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        @foreach($custom_fields as $custom_field)
                            <?php
                            $field = custom_field_build_form_field($custom_field);
                            ?>
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
                        @endforeach
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes"
                                       class="control-label">{{trans_choice('core::general.note',2)}}</label>
                                <textarea type="text" name="notes" v-model="notes"
                                          id="notes"
                                          class="form-control @error('notes') is-invalid @enderror">
                                </textarea>
                                @error('notes')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="created_date"
                                       class="control-label">{{trans_choice('core::general.submitted_on',1)}}</label>
                                <flat-pickr
                                        v-model="created_date"
                                        class="form-control  @error('created_date') is-invalid @enderror"
                                        name="created_date" required>
                                </flat-pickr>
                                @error('created_date')
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
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                branch_id: "{{old('branch_id')}}",
                external_id: "{{old('external_id')}}",
                title_id: "{{old('title_id')}}",
                first_name: "{{old('first_name')}}",
                last_name: "{{old('last_name')}}",
                gender: "{{old('gender')}}",
                marital_status: "{{old('marital_status')}}",
                country_id: "{{old('country_id')}}",
                mobile: "{{old('mobile')}}",
                dob: "{{old('dob')}}",
                loan_officer_id: "{{old('loan_officer_id')}}",
                email: "{{old('email')}}",
                profession_id: "{{old('profession_id')}}",
                client_type_id: "{{old('client_type_id')}}",
                active: "{{old('active',1)}}",
                address: "{{old('address')}}",
                notes: "{{old('notes')}}",
                created_date: "{{old('created_date',date('Y-m-d'))}}",
            }
        })
    </script>
@endsection