@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}</h3>
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
        <form method="post" action="{{ url('loan/charge/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name"
                                       class="control-label">{{trans_choice('core::general.name',1)}}</label>
                                <input type="text" name="name" v-model="name"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan_charge_type_id"
                                       class="control-label">{{trans_choice('loan::general.charge',1)}} {{trans_choice('core::general.type',1)}}
                                </label>
                                <v-select label="name" :options="charge_types"
                                          :reduce="charge_type => charge_type.id"
                                          v-model="loan_charge_type_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('loan_charge_type_id') is-invalid @enderror"
                                                :required="!loan_charge_type_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="loan_charge_type_id"
                                       v-model="loan_charge_type_id">
                                @error('loan_charge_type_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="amount"
                                       class="control-label">{{trans_choice('core::general.amount',1)}}</label>
                                <input type="text" name="amount" v-model="amount"
                                       id="amount"
                                       class="form-control numeric @error('amount') is-invalid @enderror" required>
                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan_charge_option_id"
                                       class="control-label">{{trans_choice('loan::general.charge',1)}} {{trans_choice('core::general.option',1)}}
                                </label>
                                <v-select label="name" :options="charge_options"
                                          :reduce="charge_option => charge_option.id"
                                          v-model="loan_charge_option_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('loan_charge_type_id') is-invalid @enderror"
                                                :required="!loan_charge_option_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="loan_charge_option_id"
                                       v-model="loan_charge_option_id">
                                @error('loan_charge_option_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
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
                                @error('currency_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="is_penalty" class="control-label">{{trans_choice('loan::general.penalty',1)}}</label>
                                <select v-model="is_penalty" class="form-control @error('is_penalty') is-invalid @enderror" name="is_penalty" id="is_penalty" required>
                                    <option value="0" selected>{{trans_choice("core::general.no",1)}}</option>
                                    <option value="1">{{trans_choice("core::general.yes",1)}}</option>
                                </select>
                                @error('is_penalty')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="allow_override" class="control-label">{{trans('loan::general.override')}}</label>
                                <select v-model="allow_override" class="form-control @error('allow_override') is-invalid @enderror" name="allow_override" id="allow_override" required>
                                    <option value="0" selected>{{trans_choice("core::general.no",1)}}</option>
                                    <option value="1">{{trans_choice("core::general.yes",1)}}</option>
                                </select>
                                @error('allow_override')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="active" class="control-label">{{trans('core::general.active')}}</label>
                                <select v-model="active" class="form-control @error('active') is-invalid @enderror" name="active" id="active" required>
                                    <option value="0" selected>{{trans_choice("core::general.no",1)}}</option>
                                    <option value="1">{{trans_choice("core::general.yes",1)}}</option>
                                </select>
                                @error('active')
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
                name: "{{old('name')}}",
                currency_id: parseInt("{{old('currency_id')}}"),
                loan_charge_option_id: parseInt("{{old('loan_charge_option_id')}}"),
                loan_charge_type_id: parseInt("{{old('loan_charge_type_id')}}"),
                amount: "{{old('amount')}}",
                active: "{{old('active',1)}}",
                is_penalty: "{{old('is_penalty',0)}}",
                allow_override: "{{old('allow_override',0)}}",
                charge_types: {!! json_encode($charge_types) !!},
                charge_options: {!! json_encode($charge_options) !!},
                currencies: {!! json_encode($currencies) !!},


            }
        })
    </script>
@endsection