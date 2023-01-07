@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('savings::general.charge',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('savings::general.charge',1) }}</h3>
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
        <form method="post" action="{{ url('savings/charge/'.$savings_charge->id.'/update') }}">
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
                                <label for="savings_charge_type_id"
                                       class="control-label">{{trans_choice('savings::general.charge',1)}} {{trans_choice('core::general.type',1)}}
                                </label>
                                <v-select label="name" :options="charge_types"
                                          :reduce="charge_type => charge_type.id"
                                          v-model="savings_charge_type_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('savings_charge_type_id') is-invalid @enderror"
                                                :required="!savings_charge_type_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="savings_charge_type_id"
                                       v-model="savings_charge_type_id">
                                @error('savings_charge_type_id')
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
                                <label for="savings_charge_option_id"
                                       class="control-label">{{trans_choice('savings::general.charge',1)}} {{trans_choice('core::general.option',1)}}
                                </label>
                                <v-select label="name" :options="charge_options"
                                          :reduce="charge_option => charge_option.id"
                                          v-model="savings_charge_option_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('savings_charge_type_id') is-invalid @enderror"
                                                :required="!savings_charge_option_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="savings_charge_option_id"
                                       v-model="savings_charge_option_id">
                                @error('savings_charge_option_id')
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
                                <label for="allow_override" class="control-label">{{trans('savings::general.override')}}</label>
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
                name: "{{old('name',$savings_charge->name)}}",
                currency_id: parseInt("{{old('currency_id',$savings_charge->currency_id)}}"),
                savings_charge_option_id: parseInt("{{old('savings_charge_option_id',$savings_charge->savings_charge_option_id)}}"),
                savings_charge_type_id: parseInt("{{old('savings_charge_type_id',$savings_charge->savings_charge_type_id)}}"),
                amount: "{{old('amount',$savings_charge->amount)}}",
                active: "{{old('active',$savings_charge->active)}}",
                allow_override: "{{old('allow_override',$savings_charge->allow_override)}}",
                charge_types: {!! json_encode($charge_types) !!},
                charge_options: {!! json_encode($charge_options) !!},
                currencies: {!! json_encode($currencies) !!},


            }
        })
    </script>
@endsection