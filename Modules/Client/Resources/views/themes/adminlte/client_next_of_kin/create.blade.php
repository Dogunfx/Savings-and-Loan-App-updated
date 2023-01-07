@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.next_of_kin',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.next_of_kin',1) }}
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
                                    href="{{url('client')}}">{{ trans_choice('client::general.client',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.next_of_kin',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('client/'.$id.'/client_next_of_kin/store') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="client_relationship_id"
                                       class="control-label">{{trans_choice('client::general.relationship',1)}}</label>
                                <v-select label="name" :options="relationships"
                                          :reduce="client_relationship => client_relationship.id"
                                          v-model="client_relationship_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                class="vs__search @error('client_relationship_id') is-invalid @enderror"
                                                :required="!client_relationship_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="client_relationship_id" v-model="client_relationship_id">
                                @error('client_relationship_id')
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
                                <v-select label="name" :options="titles" :reduce="title => title.id"
                                          v-model="title_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                class="vs__search @error('title_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="title_id" v-model="title_id">
                                @error('title_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


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
                                <label for="mobile" class="control-label">{{trans('core::general.mobile')}}</label>
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
                                <label for="dob"
                                       class="control-label">{{trans_choice('core::general.dob',1)}}</label>
                                <flat-pickr
                                        v-model="dob"
                                        class="form-control  @error('dob') is-invalid @enderror"
                                        name="dob">
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
                                <label for="marital_status"
                                       class="control-label">{{trans('client::general.marital_status')}}</label>
                                <select class="form-control @error('marital_status') is-invalid @enderror"
                                        name="marital_status" id="marital_status" v-model="marital_status">
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
                                <label for="country_id"
                                       class="control-label">{{trans_choice('core::general.country',1)}}</label>
                                <v-select label="name" :options="countries" :reduce="country => country.id"
                                          v-model="country_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                class="vs__search @error('country_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="country_id" v-model="country_id">
                                @error('country_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email"
                                       class="control-label">{{trans_choice('user::general.email',1)}}</label>
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
                                <label for="profession_id"
                                       class="control-label">{{trans_choice('client::general.profession',1)}}</label>
                                <v-select label="name" :options="professions" :reduce="profession => profession.id"
                                          v-model="profession_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                class="vs__search @error('profession_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="profession_id" v-model="profession_id">
                                @error('profession_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

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
                client_relationship_id: "{{old('client_relationship_id')}}",
                marital_status: "{{old('marital_status')}}",
                country_id: "{{old('country_id')}}",
                title_id: "{{old('title_id')}}",
                profession_id: "{{old('profession_id')}}",
                first_name: "{{old('first_name')}}",
                last_name: "{{old('last_name')}}",
                mobile: "{{old('mobile')}}",
                dob: "{{old('dob')}}",
                email: "{{old('email')}}",
                gender: "{{old('gender')}}",
                notes: "{{old('notes')}}",
                address: "{{old('address')}}",
                photo: "{{old('photo')}}",
                countries: {!! json_encode($countries) !!},
                professions: {!! json_encode($professions) !!},
                relationships: {!! json_encode($client_relationships) !!},
                titles: {!! json_encode($titles) !!},

            }
        })
    </script>
@endsection