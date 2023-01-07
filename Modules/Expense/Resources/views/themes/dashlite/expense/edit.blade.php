@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('expense::general.expense',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">  {{ trans_choice('core::general.edit',1) }} {{ trans_choice('expense::general.expense',1) }}</h3>
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
        <form method="post" action="{{ url('expense/'.$expense->id.'/update') }}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">

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
                        <label for="expense_type_id"
                               class="control-label">{{trans_choice('expense::general.expense',1)}} {{trans_choice('expense::general.type',1)}}
                        </label>
                        <v-select label="name" :options="expense_types"
                                  :reduce="expense_type => expense_type.id"
                                  @input="change_expense_type()"
                                  v-model="expense_type_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('expense_type_id') is-invalid @enderror"
                                        :required="!expense_type_id"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="expense_type_id"
                               v-model="expense_type_id">
                        @error('expense_type_id')
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
                        <label for="amount" class="control-label">{{trans_choice('expense::general.amount',1)}}</label>
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
                        <label for="expense_chart_of_account_id"
                               class="control-label">{{trans_choice('accounting::general.expense',1)}} {{trans_choice('accounting::general.account',1)}}

                        </label>
                        <v-select label="name" :options="expenses"
                                  :reduce="expense => expense.id"
                                  v-model="expense_chart_of_account_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('expense_chart_of_account_id') is-invalid @enderror"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="expense_chart_of_account_id"
                               v-model="expense_chart_of_account_id">
                        @error('expense_chart_of_account_id')
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
                        <label for="recurring"
                               class="control-label">{{trans_choice('expense::general.recurring',1)}}</label>
                        <select class="form-control @error('recurring') is-invalid @enderror" name="recurring"
                                id="recurring"
                                v-model="recurring" required>
                            <option value="0">{{trans_choice('core::general.no',1)}}</option>
                            <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                        </select>
                        @error('recurring')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="row" v-show="recurring==1">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_frequency"
                                       class="control-label">{{trans_choice('expense::general.recur_frequency',1)}}</label>
                                <input type="number" name="recur_frequency" id="recur_frequency"
                                       class="form-control @error('recur_frequency') is-invalid @enderror numeric"
                                       v-model="recur_frequency"
                                       v-bind:required="recurring==1">
                                @error('tenant_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_type"
                                       class="control-label">{{trans_choice('expense::general.recur_type',1)}}</label>
                                <select class="form-control @error('recur_type') is-invalid @enderror"
                                        name="recur_type"
                                        id="recur_type"
                                        v-model="recur_type">
                                    <option value="day">{{trans_choice('expense::general.day',1)}}</option>
                                    <option value="week">{{trans_choice('expense::general.week',1)}}</option>
                                    <option value="month">{{trans_choice('expense::general.month',1)}}</option>
                                    <option value="year">{{trans_choice('expense::general.year',1)}}</option>
                                </select>
                                @error('recur_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_start_date"
                                       class="control-label">{{trans_choice('expense::general.recur_start_date',1)}}</label>
                                <flat-pickr v-model="recur_start_date" name="recur_start_date" id="recur_start_date"
                                            class="form-control  @error('recur_start_date') is-invalid @enderror"
                                            v-bind:required="recurring==1"></flat-pickr>
                                @error('recur_start_date')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_end_date"
                                       class="control-label">{{trans_choice('expense::general.recur_end_date',1)}}</label>
                                <flat-pickr v-model="recur_end_date" name="recur_end_date" id="recur_end_date"
                                            class="form-control  @error('recur_end_date') is-invalid @enderror"></flat-pickr>
                                @error('recur_end_date')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="description"
                               class="control-label">{{trans_choice('core::general.description',1)}}</label>
                        <textarea type="text" name="description" v-model="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror">
                        </textarea>
                        @error('description')
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
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                expense_type_id: parseInt("{{old('expense_type_id',$expense->expense_type_id)}}"),
                currency_id: parseInt("{{old('currency_id',$expense->currency_id)}}"),
                branch_id: parseInt("{{old('branch_id',$expense->branch_id)}}"),
                amount: "{{old('amount',$expense->amount)}}",
                date: "{{old('date',$expense->date)}}",
                recurring: "{{old('recurring',$expense->recurring)}}",
                recur_frequency: "{{old('recur_frequency',$expense->recur_frequency)}}",
                recur_start_date: "{{old('recur_start_date',$expense->recur_start_date)}}",
                recur_end_date: "{{old('recur_end_date',$expense->recur_end_date)}}",
                recur_next_date: "{{old('recur_next_date',$expense->recur_next_date)}}",
                recur_type: "{{old('recur_type',$expense->recur_type)}}",
                description: "{{old('description',$expense->description)}}",
                expense_chart_of_account_id: parseInt("{{old('expense_chart_of_account_id',$expense->expense_chart_of_account_id)}}"),
                asset_chart_of_account_id: parseInt("{{old('asset_chart_of_account_id',$expense->asset_chart_of_account_id)}}"),
                expense_types: {!! json_encode($expense_types) !!},
                expenses: {!! json_encode($expenses) !!},
                assets: {!! json_encode($assets) !!},
                currencies: {!! json_encode($currencies) !!},
                branches: {!! json_encode($branches) !!},
            },
            methods: {
                change_expense_type() {
                    this.expense_types.forEach(item => {
                        if (item.id == this.expense_type_id) {
                            if (!this.expense_chart_of_account_id) {
                                this.expense_chart_of_account_id = item.expense_chart_of_account_id;
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