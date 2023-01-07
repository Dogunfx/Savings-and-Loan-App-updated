@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.product',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.product',1) }}</h3>
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
        <form method="post" action="{{ url('savings/product/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name"
                                       class="control-label">{{trans_choice('core::general.name',1)}} </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror" v-model="name" required>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="short_name"
                                       class="control-label">{{trans_choice('savings::general.short_name',1)}}</label>
                                <input type="text" name="short_name" value="{{ old('short_name') }}"
                                       id="short_name"
                                       class="form-control @error('short_name') is-invalid @enderror"
                                       v-model="short_name" required>
                                @error('short_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="description"
                                       class="control-label">{{trans_choice('core::general.description',1)}}</label>
                                <input type="text" name="description" value="{{ old('description') }}"
                                       id="description"
                                       class="form-control @error('description') is-invalid @enderror"
                                       v-model="description" required>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_id"
                                       class="control-label">{{trans_choice('core::general.currency',1)}}</label>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="decimals"
                                       class="control-label">{{trans_choice('savings::general.decimal_place',2)}}</label>
                                <input type="text" name="decimals" value="0" v-model="decimals"
                                       id="decimals"
                                       class="form-control numeric @error('decimals') is-invalid @enderror">
                                @error('decimals')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_interest_rate"
                                       class="control-label">{{trans_choice('savings::general.interest',1)}}</label>
                                <input type="text" name="default_interest_rate" v-model="default_interest_rate"
                                       id="default_interest_rate" required
                                       class="form-control @error('default_interest_rate') is-invalid @enderror numeric">
                                @error('default_interest_rate')
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
                                <label for="savings_category"
                                       class="control-label">{{trans_choice('savings::general.category',1)}}</label>
                                <select class="form-control @error('savings_category') is-invalid @enderror "
                                        name="savings_category" id="savings_category"
                                        v-model="savings_category"
                                        required>
                                    <option value=""></option>
                                    <option value="voluntary">{{trans_choice('savings::general.voluntary',1)}}</option>
                                    <option value="compulsory">{{trans_choice('savings::general.compulsory',1)}}</option>
                                </select>
                                @error('savings_category')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="auto_create"
                                       class="control-label">{{trans_choice('savings::general.auto_create',1)}}</label>
                                <select class="form-control @error('auto_create') is-invalid @enderror "
                                        name="auto_create" id="auto_create"
                                        v-model="auto_create"
                                        required>
                                    <option value=""></option>
                                    <option value="0" selected>{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                                </select>
                                @error('auto_create')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="compounding_period"
                                       class="control-label">{{trans_choice('savings::general.compounding_period',1)}}</label>
                                <select class="form-control  @error('compounding_period') is-invalid @enderror"
                                        name="compounding_period"
                                        v-model="compounding_period" id="compounding_period"
                                        required>
                                    <option value=""></option>
                                    <option value="daily">{{trans_choice('savings::general.daily',2)}}</option>
                                    <option value="weekly">{{trans_choice('savings::general.weekly',2)}}</option>
                                    <option value="monthly">{{trans_choice('savings::general.monthly',2)}}</option>
                                    <option value="biannual">{{trans_choice('savings::general.biannual',2)}}</option>
                                    <option value="annually">{{trans_choice('savings::general.annually',2)}}</option>
                                </select>
                                @error('compounding_period')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interest_posting_period_type"
                                       class="control-label">{{trans_choice('savings::general.interest_posting_period_type',1)}}</label>
                                <select class="form-control @error('interest_posting_period_type') is-invalid @enderror "
                                        name="interest_posting_period_type"
                                        v-model="interest_posting_period_type" id="interest_posting_period_type"
                                        required>
                                    <option value=""></option>
                                    <option value="monthly">{{trans_choice('savings::general.monthly',2)}}</option>
                                    <option value="biannual">{{trans_choice('savings::general.biannual',2)}}</option>
                                    <option value="annually">{{trans_choice('savings::general.annually',2)}}</option>
                                </select>
                                @error('interest_posting_period_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interest_calculation_type"
                                       class="control-label">{{trans_choice('savings::general.interest_calculation_type',1)}}</label>
                                <select class="form-control @error('interest_calculation_type') is-invalid @enderror "
                                        name="interest_calculation_type"
                                        v-model="interest_calculation_type" id="interest_calculation_type"
                                        required>
                                    <option value=""></option>
                                    <option value="daily_balance">{{trans_choice('savings::general.daily_balance',2)}}</option>
                                    <option value="average_balance">{{trans_choice('savings::general.average_balance',2)}}</option>
                                </select>
                                @error('interest_calculation_type')
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
                                <label for="lockin_period"
                                       class="control-label">{{trans_choice('savings::general.lockin_period',1)}}</label>
                                <input type="text" name="lockin_period"
                                       id="lockin_period" v-model="lockin_period"
                                       class="form-control @error('lockin_period') is-invalid @enderror numeric"
                                       required>
                                @error('lockin_period')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lockin_type"
                                       class="control-label">{{trans_choice('savings::general.lockin_type',1)}}</label>
                                <select class="form-control @error('lockin_type') is-invalid @enderror "
                                        name="lockin_type" v-model="lockin_type"
                                        id="lockin_type"
                                        required>
                                    <option value=""></option>
                                    <option value="days">{{trans_choice('savings::general.day',2)}}</option>
                                    <option value="weeks">{{trans_choice('savings::general.week',2)}}</option>
                                    <option value="months">{{trans_choice('savings::general.month',2)}}</option>
                                    <option value="years">{{trans_choice('savings::general.year',2)}}</option>
                                </select>
                                @error('lockin_type')
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
                                <label for="automatic_opening_balance"
                                       class="control-label">{{trans_choice('savings::general.automatic_opening_balance',1)}}</label>
                                <input type="text" name="automatic_opening_balance"
                                       id="automatic_opening_balance"
                                       class="form-control @error('automatic_opening_balance') is-invalid @enderror numeric"
                                       v-model="automatic_opening_balance" required>
                                @error('automatic_opening_balance')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="minimum_balance_for_interest_calculation"
                                       class="control-label">{{trans_choice('savings::general.minimum_balance_for_interest_calculation',1)}}</label>
                                <input type="text" name="minimum_balance_for_interest_calculation"
                                       id="minimum_balance_for_interest_calculation"
                                       class="form-control @error('minimum_balance_for_interest_calculation') is-invalid @enderror numeric"
                                       v-model="minimum_balance_for_interest_calculation"
                                       required>
                                @error('minimum_balance_for_interest_calculation')
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
                                <label for="allow_overdraft"
                                       class="control-label">{{trans_choice('savings::general.allow_overdraft',1)}}</label>
                                <select class="form-control  @error('allow_overdraft') is-invalid @enderror"
                                        name="allow_overdraft" v-model="allow_overdraft"
                                        id="allow_overdraft"
                                        required>
                                    <option value=""></option>
                                    <option value="0">{{trans_choice('core::general.no',2)}}</option>
                                    <option value="1">{{trans_choice('core::general.yes',2)}}</option>
                                </select>
                                @error('allow_overdraft')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4" v-if="allow_overdraft=='1'">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="overdraft_limit"
                                       class="control-label"> {{trans_choice('savings::general.overdraft_limit',1)}}</label>
                                <input type="text" name="overdraft_limit"
                                       id="overdraft_limit"
                                       class="form-control @error('overdraft_limit') is-invalid @enderror numeric"
                                       v-model="overdraft_limit"
                                       v-bind:required="allow_overdraft=='1'">
                                @error('overdraft_limit')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="overdraft_interest_rate"
                                       class="control-label">{{trans_choice('savings::general.overdraft_interest_rate',1)}}</label>
                                <input type="text" name="overdraft_interest_rate"
                                       id="overdraft_interest_rate"
                                       class="form-control @error('overdraft_interest_rate') is-invalid @enderror numeric"
                                       v-model="overdraft_interest_rate"
                                       v-bind:required="allow_overdraft=='1'">
                                @error('overdraft_interest_rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="minimum_overdraft_for_interest"
                                       class="control-label">{{trans_choice('savings::general.minimum_overdraft_for_interest',1)}}</label>
                                <input type="text" name="minimum_overdraft_for_interest"
                                       id="minimum_overdraft_for_interest"
                                       class="form-control @error('minimum_overdraft_for_interest') is-invalid @enderror numeric"
                                       v-model="minimum_overdraft_for_interest"
                                       v-bind:required="allow_overdraft=='1'">
                                @error('minimum_overdraft_for_interest')
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
                                <label for="charges"
                                       class="control-label">{{trans_choice('savings::general.charge',2)}}</label>
                                <v-select label="name" :options="available_charges"
                                          :reduce="charge => charge.id"
                                          v-model="selected_charges" multiple>
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('charges') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="charges[]"
                                       v-model="selected_charges">
                                @error('charges')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <h3>{{trans_choice('accounting::general.accounting',1)}}</h3>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="accounting_rule"
                                       class="control-label">{{trans_choice('savings::general.accounting_rule',1)}}</label>
                                <select class="form-control @error('accounting_rule') is-invalid @enderror "
                                        name="accounting_rule" v-model="accounting_rule"
                                        id="accounting_rule"
                                        required>
                                    <option value=""></option>
                                    <option value="none" selected>{{trans_choice('savings::general.none',1)}}</option>
                                    <option value="cash">{{trans_choice('savings::general.cash',1)}}</option>
                                </select>
                                @error('accounting_rule')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div id="accounting_rule_div" v-if="accounting_rule==='cash'">
                        <h4>{{trans_choice('accounting::general.asset',2)}}</h4>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="savings_reference_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.savings_reference',1)}}</label>
                                    <v-select label="name" :options="assets"
                                              :reduce="asset => asset.id"
                                              v-model="savings_reference_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('savings_reference_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    :required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="savings_reference_chart_of_account_id"
                                           v-model="savings_reference_chart_of_account_id">
                                    @error('savings_reference_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="overdraft_portfolio_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.overdraft_portfolio',1)}}</label>
                                    <v-select label="name" :options="assets"
                                              :reduce="asset => asset.id"
                                              v-model="overdraft_portfolio_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('overdraft_portfolio_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    :required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="overdraft_portfolio_chart_of_account_id"
                                           v-model="overdraft_portfolio_chart_of_account_id">
                                    @error('overdraft_portfolio_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <h4>{{trans_choice('accounting::general.liability',2)}}</h4>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="savings_control_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.savings_control',2)}}</label>
                                    <v-select label="name" :options="liabilities"
                                              :reduce="liability => liability.id"
                                              v-model="savings_control_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('savings_control_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    :required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="savings_control_chart_of_account_id"
                                           v-model="savings_control_chart_of_account_id">
                                    @error('savings_control_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <h4>{{trans_choice('accounting::general.expense',2)}}</h4>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="interest_on_savings_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.interest_on_savings',2)}}</label>
                                    <v-select label="name" :options="expenses"
                                              :reduce="expense => expense.id"
                                              v-model="interest_on_savings_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('interest_on_savings_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="interest_on_savings_chart_of_account_id"
                                           v-model="interest_on_savings_chart_of_account_id">
                                    @error('interest_on_savings_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="write_off_savings_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.write_off_savings',2)}}</label>
                                    <v-select label="name" :options="expenses"
                                              :reduce="expense => expense.id"
                                              v-model="write_off_savings_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('write_off_savings_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="write_off_savings_chart_of_account_id"
                                           v-model="write_off_savings_chart_of_account_id">
                                    @error('write_off_savings_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                        </div>
                        <h4>{{trans_choice('accounting::general.income',2)}}</h4>
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="income_from_fees_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.income_from_fees',2)}}</label>
                                    <v-select label="name" :options="income"
                                              :reduce="income => income.id"
                                              v-model="income_from_fees_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('income_from_fees_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="income_from_fees_chart_of_account_id"
                                           v-model="income_from_fees_chart_of_account_id">
                                    @error('income_from_fees_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="income_from_penalties_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.income_from_penalties',2)}}</label>
                                    <v-select label="name" :options="income"
                                              :reduce="income => income.id"
                                              v-model="income_from_penalties_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('income_from_penalties_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="income_from_penalties_chart_of_account_id"
                                           v-model="income_from_penalties_chart_of_account_id">
                                    @error('income_from_penalties_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="income_from_interest_overdraft_chart_of_account_id"
                                           class="control-label">{{trans_choice('savings::general.income_from_interest_overdraft',2)}}</label>
                                    <v-select label="name" :options="income"
                                              :reduce="income => income.id"
                                              v-model="income_from_interest_overdraft_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('income_from_interest_overdraft_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="income_from_interest_overdraft_chart_of_account_id"
                                           v-model="income_from_interest_overdraft_chart_of_account_id">
                                    @error('income_from_interest_overdraft_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="active"
                                       class="control-label">{{trans_choice('core::general.active',1)}}</label>
                                <select class="form-control " name="active" id="active" v-model="active"
                                        required>
                                    <option value=""></option>
                                    <option value="0">{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1" selected>{{trans_choice('core::general.yes',1)}}</option>
                                </select>
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
            el: '#app',
            data: {
                name: "{{old('name')}}",
                short_name: "{{old('short_name')}}",
                description: "{{old('description')}}",
                currency_id: parseInt("{{old('currency_id')}}"),
                decimals: "{{old('decimals',0)}}",
                savings_category: "{{old('savings_category')}}",
                auto_create: "{{old('auto_create',0)}}",
                minimum_interest_rate: "{{old('minimum_interest_rate')}}",
                default_interest_rate: "{{old('default_interest_rate')}}",
                maximum_interest_rate: "{{old('maximum_interest_rate')}}",
                interest_rate_type: "{{old('interest_rate_type')}}",
                compounding_period: "{{old('compounding_period','daily')}}",
                interest_posting_period_type: "{{old('interest_posting_period_type','monthly')}}",
                interest_calculation_type: "{{old('interest_calculation_type','daily_balance')}}",
                lockin_period: "{{old('lockin_period',0)}}",
                lockin_type: "{{old('lockin_type','days')}}",
                automatic_opening_balance: "{{old('automatic_opening_balance',0)}}",
                minimum_balance_for_interest_calculation: "{{old('minimum_balance_for_interest_calculation',0)}}",
                minimum_balance: "{{old('minimum_balance',0)}}",
                allow_overdraft: "{{old('allow_overdraft',0)}}",
                accounting_rule: "{{old('accounting_rule','none')}}",
                active: "{{old('active',1)}}",
                overdraft_limit: "{{old('overdraft_limit')}}",
                overdraft_interest_rate: "{{old('overdraft_interest_rate')}}",
                minimum_overdraft_for_interest: "{{old('minimum_overdraft_for_interest')}}",
                days_in_year: "{{old('days_in_year')}}",
                days_in_month: "{{old('days_in_month')}}",
                selected_charges: [],
                savings_reference_chart_of_account_id: parseInt("{{old('savings_reference_chart_of_account_id')}}"),
                savings_control_chart_of_account_id: parseInt("{{old('savings_control_chart_of_account_id')}}"),
                interest_on_savings_chart_of_account_id: parseInt("{{old('interest_on_savings_chart_of_account_id')}}"),
                write_off_savings_chart_of_account_id: parseInt("{{old('write_off_savings_chart_of_account_id')}}"),
                income_from_fees_chart_of_account_id: parseInt("{{old('income_from_fees_chart_of_account_id')}}"),
                income_from_penalties_chart_of_account_id: parseInt("{{old('income_from_penalties_chart_of_account_id')}}"),
                income_from_interest_overdraft_chart_of_account_id: parseInt("{{old('income_from_interest_overdraft_chart_of_account_id')}}"),
                currencies: {!! json_encode($currencies) !!},
                assets: {!! json_encode($assets) !!},
                liabilities: {!! json_encode($liabilities) !!},
                income: {!! json_encode($income) !!},
                expenses: {!! json_encode($expenses) !!},
                savings_charges: {!! json_encode($savings_charges) !!},
            },
            methods: {
                onSubmit() {

                }
            },
            computed: {
                available_charges: function () {
                    return this.savings_charges.filter(item => {
                        if (this.currency_id == item.currency_id) {
                            return true;
                        }
                        return false;
                    })

                }
            }
        })
    </script>
@endsection