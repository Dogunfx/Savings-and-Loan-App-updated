@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('core::general.type',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('core::general.type',1) }}
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
                                    href="{{url('asset/type')}}">{{ trans_choice('asset::general.asset',1) }} {{ trans_choice('core::general.type',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('core::general.type',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('asset/type/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name" class="control-label">{{trans_choice('core::general.name',1)}}</label>
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
                                <label for="chart_of_account_asset_id"
                                       class="control-label">{{trans_choice('asset::general.cash',1)}} {{trans_choice('asset::general.account',1)}}
                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                       data-title="{{trans_choice('asset::general.cash_account_hint',1)}}"></i>

                                </label>
                                <v-select label="name" :options="chart_of_accounts"
                                          :reduce="chart_of_account => chart_of_account.id"
                                          v-model="chart_of_account_asset_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('chart_of_account_asset_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="chart_of_account_asset_id"
                                       v-model="chart_of_account_asset_id">
                                @error('chart_of_account_asset_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="chart_of_account_fixed_asset_id"
                                       class="control-label">{{trans_choice('asset::general.fixed',1)}} {{trans_choice('asset::general.asset',1)}} {{trans_choice('asset::general.account',1)}}
                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                       data-title="{{trans_choice('asset::general.fixed_asset_account_hint',1)}}"></i>
                                </label>
                                <v-select label="name" :options="chart_of_accounts"
                                          :reduce="chart_of_account => chart_of_account.id"
                                          v-model="chart_of_account_fixed_asset_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('chart_of_account_asset_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="chart_of_account_fixed_asset_id"
                                       v-model="chart_of_account_fixed_asset_id">
                                @error('chart_of_account_fixed_asset_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="chart_of_account_expense_id"
                                       class="control-label">{{trans_choice('asset::general.expense',1)}} {{trans_choice('asset::general.account',1)}}
                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                       data-title="{{trans_choice('asset::general.expense_account_hint',1)}}"></i>
                                </label>
                                <v-select label="name" :options="chart_of_accounts"
                                          :reduce="chart_of_account => chart_of_account.id"
                                          v-model="chart_of_account_expense_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('chart_of_account_expense_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="chart_of_account_expense_id"
                                       v-model="chart_of_account_expense_id">
                                @error('chart_of_account_expense_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="chart_of_account_contra_asset_id"
                                       class="control-label">{{trans_choice('asset::general.accumulated',1)}} {{trans_choice('asset::general.depreciation',1)}} {{trans_choice('asset::general.account',1)}}
                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                       data-title="{{trans_choice('asset::general.accumulated_depreciation_account_hint',1)}}"></i>

                                </label>
                                <v-select label="name" :options="chart_of_accounts"
                                          :reduce="chart_of_account => chart_of_account.id"
                                          v-model="chart_of_account_contra_asset_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('chart_of_account_contra_asset_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="chart_of_account_contra_asset_id"
                                       v-model="chart_of_account_contra_asset_id">
                                @error('chart_of_account_contra_asset_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="chart_of_account_income_id"
                                       class="control-label">{{trans_choice('asset::general.income',1)}} {{trans_choice('asset::general.account',1)}}
                                    <i class="fa fa-question-circle" data-toggle="tooltip"
                                       data-title="{{trans_choice('asset::general.income_account_hint',1)}}"></i>
                                </label>
                                <v-select label="name" :options="chart_of_accounts"
                                          :reduce="chart_of_account => chart_of_account.id"
                                          v-model="chart_of_account_income_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('chart_of_account_income_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="chart_of_account_income_id"
                                       v-model="chart_of_account_income_id">
                                @error('chart_of_account_income_id')
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
            el: '#app',
            data: {
                name: "{{old('name')}}",
                type: "{{old('type')}}",
                chart_of_account_fixed_asset_id: parseInt("{{old('chart_of_account_fixed_asset_id')}}"),
                chart_of_account_asset_id: parseInt("{{old('chart_of_account_asset_id')}}"),
                chart_of_account_contra_asset_id: parseInt("{{old('chart_of_account_contra_asset_id')}}"),
                chart_of_account_expense_id: parseInt("{{old('chart_of_account_expense_id')}}"),
                chart_of_account_liability_id: parseInt("{{old('chart_of_account_liability_id')}}"),
                chart_of_account_income_id: parseInt("{{old('chart_of_account_income_id')}}"),
                notes: `{{old('notes')}}`,
                chart_of_accounts: {!! json_encode($chart_of_accounts) !!},
            }
        })
    </script>
@endsection