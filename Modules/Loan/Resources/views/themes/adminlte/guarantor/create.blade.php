@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.guarantor',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.guarantor',1) }}
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
                                    href="{{url('loan/'.$id.'/show')}}">{{ trans_choice('loan::general.loan',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.guarantor',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/'.$id.'/guarantor/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="form-group">
                        <label for="is_client"
                               class="control-label">{{trans_choice('loan::general.is_client',1)}}</label>
                        <select class="form-control" name="is_client" id="is_client" v-model="is_client" required>
                            <option value="1" selected>{{trans_choice('core::general.yes',1)}}</option>
                            <option value="0">{{trans_choice('core::general.no',1)}}</option>
                        </select>
                    </div>
                    <div v-if="is_client=='1'">
                        <div class="form-group">
                            <label for="client_id"
                                   class="control-label">{{trans_choice('client::general.client',1)}}</label>
                            <v-select label="name_id" :options="clients"
                                      :reduce="client => client.id"
                                      v-model="client_id">
                                <template #search="{attributes, events}">
                                    <input
                                            autocomplete="off"
                                            class="vs__search @error('client_id') is-invalid @enderror"
                                            v-bind="attributes"
                                            :required="!client_id"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                            <input type="hidden" name="client_id"
                                   v-model="client_id">
                            @error('client_id')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div v-if="is_client=='0'">
                        <div class="form-group">
                            <label for="client_relationship_id"
                                   class="control-label">{{trans_choice('client::general.relationship',1)}}</label>
                            <v-select label="name" :options="client_relationships"
                                      :reduce="client_relationship => client_relationship.id"
                                      v-model="client_relationship_id">
                                <template #search="{attributes, events}">
                                    <input
                                            autocomplete="off"
                                            class="vs__search @error('client_relationship_id') is-invalid @enderror"
                                            v-bind="attributes"
                                            :required="!client_relationship_id"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                            <input type="hidden" name="client_relationship_id"
                                   v-model="client_relationship_id">
                            @error('client_relationship_id')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="title_id"
                                   class="control-label">{{trans_choice('client::general.title',1)}}</label>
                            <v-select label="name" :options="titles"
                                      :reduce="title => title.id"
                                      v-model="title_id">
                                <template #search="{attributes, events}">
                                    <input
                                            autocomplete="off"
                                            class="vs__search @error('title_id') is-invalid @enderror"
                                            v-bind="attributes"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                            <input type="hidden" name="title_id"
                                   v-model="title_id">
                            @error('title_id')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

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

                        <div class="form-group">
                            <label for="gender"
                                   class="control-label">{{trans_choice('core::general.gender',1)}}</label>
                            <select class="form-control @error('gender') is-invalid @enderror" name="gender"
                                    id="gender" v-model="gender" required>
                                <option value="male">{{trans_choice("core::general.male",1)}}</option>
                                <option value="female">{{trans_choice("core::general.female",1)}}</option>
                            </select>
                            @error('gender')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

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

                        <div class="form-group">
                            <label for="country_id"
                                   class="control-label">{{trans_choice('core::general.country',1)}}</label>
                            <v-select label="name" :options="countries"
                                      :reduce="country => country.id"
                                      v-model="country_id">
                                <template #search="{attributes, events}">
                                    <input
                                            autocomplete="off"
                                            class="vs__search @error('country_id') is-invalid @enderror"
                                            v-bind="attributes"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                            <input type="hidden" name="country_id"
                                   v-model="country_id">
                            @error('country_id')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

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

                        <div class="form-group">
                            <label for="profession_id"
                                   class="control-label">{{trans_choice('client::general.profession',1)}}</label>
                            <v-select label="name" :options="professions"
                                      :reduce="profession => profession.id"
                                      v-model="profession_id">
                                <template #search="{attributes, events}">
                                    <input
                                            autocomplete="off"
                                            class="vs__search @error('profession_id') is-invalid @enderror"
                                            v-bind="attributes"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                            <input type="hidden" name="profession_id"
                                   v-model="profession_id">
                            @error('profession_id')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

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
                    <div class="form-group">
                        <label for="guaranteed_amount" class="control-label">{{trans('core::general.amount')}}</label>
                        <input type="number" name="guaranteed_amount" value="{{ old('guaranteed_amount') }}"
                               id="guaranteed_amount" v-model="guaranteed_amount"
                               class="form-control @error('guaranteed_amount') is-invalid @enderror numeric">
                        @error('guaranteed_amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                is_client: "{{old('is_client',1)}}",
                client_id: parseInt("{{old('client_id')}}"),
                client_relationship_id: parseInt("{{old('client_relationship_id')}}"),
                title_id: parseInt("{{old('title_id')}}"),
                first_name: "{{old('first_name')}}",
                last_name: "{{old('last_name')}}",
                gender: "{{old('gender')}}",
                marital_status: "{{old('marital_status')}}",
                country_id: parseInt("{{old('country_id')}}"),
                mobile: "{{old('mobile')}}",
                dob: "{{old('dob')}}",
                email: "{{old('email')}}",
                profession_id: parseInt("{{old('profession_id')}}"),
                client_type_id: parseInt("{{old('client_type_id')}}"),
                active: "{{old('active',1)}}",
                address: `{{old('address')}}`,
                notes: `{{old('notes')}}`,
                guaranteed_amount: "{{old('guaranteed_amount')}}",
                created_date: "{{old('created_date',date('Y-m-d'))}}",
                clients: {!! json_encode($clients) !!},
                client_relationships:  {!! json_encode($client_relationships) !!},
                titles:  {!! json_encode($titles) !!},
                professions:  {!! json_encode($professions) !!},
                countries:  {!! json_encode($countries) !!},
            },
            created: function () {

            },
            methods: {
                onSubmit() {

                }
            }
        });
    </script>
@endsection