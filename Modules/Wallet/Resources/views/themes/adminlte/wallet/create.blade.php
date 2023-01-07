@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('wallet::general.wallet',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('wallet::general.wallet',1) }}
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
                                    href="{{url('wallet')}}">{{ trans_choice('wallet::general.wallet',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('wallet::general.share',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('wallet/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_id"
                                       class="control-label">{{trans_choice('client::general.client',1)}}</label>
                                <v-select label="name" :options="clients"
                                          :reduce="client => client.id"
                                          @input="change_client"
                                          v-model="client_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('client_id') is-invalid @enderror"
                                                :required="!client_id"
                                                v-bind="attributes"
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="branch_id"
                                       class="control-label">{{trans_choice('branch::general.branch',1)}}
                                </label>
                                <v-select label="name" :options="branches"
                                          :reduce="branch => branch.id"
                                          v-model="branch_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('branch_id') is-invalid @enderror"
                                                :required="!branch_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="branch_id"
                                       v-model="branch_id">
                                @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_id"
                                       class="control-label">{{trans_choice('core::general.currency',1)}}
                                </label>
                                <v-select label="name" :options="currencies"
                                          :reduce="currency => currency.id"
                                          v-model="currency_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('currency_id') is-invalid @enderror"
                                                :required="!currency_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="currency_id"
                                       v-model="currency_id">
                                @error('branch_id')
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
                                <label for="submitted_on_date"
                                       class="control-label">{{trans_choice('core::general.submitted_on',1)}}</label>
                                <flat-pickr v-model="submitted_on_date" name="submitted_on_date"
                                            class="form-control @error('submitted_on_date') is-invalid @enderror"
                                            required></flat-pickr>
                                @error('submitted_on_date')
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
                client_id: parseInt("{{old('client_id')}}"),
                currency_id: parseInt("{{old('currency_id')}}"),
                client_type: "{{old('client_type')}}",
                branch_id: parseInt("{{old('branch_id')}}"),
                status: '{{old('status','active')}}',
                description: `{{old('description')}}`,
                submitted_on_date: "{{old('submitted_on_date',date("Y-m-d"))}}",
                branches: branches,
                clients: clients,
                currencies: currencies,
            },
            methods: {
                change_client() {
                    this.clients.forEach(item => {
                        if (this.client_id == item.id) {
                            this.branch_id = item.branch_id;
                        }
                    });
                },
                onSubmit() {

                },
            }
        })
    </script>
@endsection