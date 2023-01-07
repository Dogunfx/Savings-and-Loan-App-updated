@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('income::general.income',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('income::general.income',1) }}
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
                                    href="{{url('income')}}">{{ trans_choice('income::general.income',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('income::general.income',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('income/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
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
                    <div class="form-group">
                        <label for="income_type_id"
                               class="control-label">{{trans_choice('income::general.income',1)}} {{trans_choice('income::general.type',1)}}
                        </label>
                        <v-select label="name" :options="income_types"
                                  :reduce="income_type => income_type.id"
                                  @input="change_income_type()"
                                  v-model="income_type_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('income_type_id') is-invalid @enderror"
                                        :required="!income_type_id"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="income_type_id"
                               v-model="income_type_id">
                        @error('income_type_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
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
                    <div class="form-group">
                        <label for="amount" class="control-label">{{trans_choice('income::general.amount',1)}}</label>
                        <input type="text" name="amount" v-model="amount"
                               id="amount"
                               class="form-control numeric @error('amount') is-invalid @enderror" required>
                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="date"
                               class="control-label">{{trans_choice('core::general.date',1)}}</label>
                        <flat-pickr v-model="date" name="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    required></flat-pickr>
                        @error('date')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="income_chart_of_account_id"
                               class="control-label">{{trans_choice('accounting::general.income',1)}} {{trans_choice('accounting::general.account',1)}}

                        </label>
                        <v-select label="name" :options="incomes"
                                  :reduce="income => income.id"
                                  v-model="income_chart_of_account_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('income_chart_of_account_id') is-invalid @enderror"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="income_chart_of_account_id"
                               v-model="income_chart_of_account_id">
                        @error('income_chart_of_account_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="asset_chart_of_account_id"
                               class="control-label">{{trans_choice('accounting::general.asset',1)}} {{trans_choice('accounting::general.account',1)}}

                        </label>
                        <v-select label="name" :options="assets"
                                  :reduce="asset => asset.id"
                                  v-model="asset_chart_of_account_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('asset_chart_of_account_id') is-invalid @enderror"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="asset_chart_of_account_id"
                               v-model="asset_chart_of_account_id">
                        @error('asset_chart_of_account_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description"
                               class="control-label">{{trans_choice('core::general.description',1)}}</label>
                        <textarea type="text" name="description" v-model="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror">
                        </textarea>
                        @error('amount')
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
                income_type_id: "{{old('income_type_id')}}",
                currency_id: "{{old('currency_id')}}",
                branch_id: "{{old('branch_id')}}",
                amount: "{{old('amount')}}",
                date: "{{old('date',date('Y-m-d'))}}",
                recurring: "{{old('recurring',0)}}",
                recur_frequency: "{{old('recur_frequency',1)}}",
                recur_start_date: "{{old('recur_start_date')}}",
                recur_end_date: "{{old('recur_end_date')}}",
                recur_next_date: "{{old('recur_next_date')}}",
                recur_type: "{{old('recur_type','month')}}",
                description: "{{old('description')}}",
                income_chart_of_account_id: "{{old('income_chart_of_account_id')}}",
                asset_chart_of_account_id: "{{old('asset_chart_of_account_id')}}",
                income_types: {!! json_encode($income_types) !!},
                incomes: {!! json_encode($incomes) !!},
                assets: {!! json_encode($assets) !!},
                currencies: {!! json_encode($currencies) !!},
                branches: {!! json_encode($branches) !!},
            },
            methods: {
                change_income_type() {
                    this.income_types.forEach(item => {
                        if (item.id == this.income_type_id) {
                            if (!this.income_chart_of_account_id) {
                                this.income_chart_of_account_id = item.income_chart_of_account_id;
                            }
                            if (!this.asset_chart_of_account_id) {
                                this.asset_chart_of_account_id = item.asset_chart_of_account_id;
                            }
                        }
                    });
                }
            }
        })
    </script>
@endsection