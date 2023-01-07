@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.product',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.product',1) }}</h3>
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
        <form method="post" action="{{ url('loan/product/'.$loan_product->id.'/update') }}">
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
                                       class="control-label">{{trans_choice('loan::general.short_name',1)}}</label>
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
                                <label for="fund_id"
                                       class="control-label">{{trans_choice('loan::general.fund',1)}}</label>
                                <v-select label="name" :options="funds"
                                          :reduce="fund => fund.id"
                                          v-model="fund_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('fund_id') is-invalid @enderror"
                                                :required="!fund_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="fund_id"
                                       v-model="fund_id">
                                @error('fund_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
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
                                       class="control-label">{{trans_choice('loan::general.decimal_place',2)}}</label>
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
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="default_principal"
                                       class="control-label">{{trans_choice('loan::general.default',1)}} {{trans_choice('loan::general.principal',1)}}</label>
                                <input type="text" name="default_principal" value="{{ old('default_principal') }}"
                                       id="default_principal"
                                       class="form-control numeric @error('default_principal') is-invalid @enderror"
                                       v-model="default_principal" required>
                                @error('default_principal')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="minimum_principal"
                                       class="control-label">{{trans_choice('loan::general.minimum',1)}} {{trans_choice('loan::general.principal',1)}}</label>
                                <input type="text" name="minimum_principal" value="{{ old('minimum_principal') }}"
                                       id="minimum_principal"
                                       class="form-control numeric @error('minimum_principal') is-invalid @enderror"
                                       v-model="minimum_principal" required>
                                @error('minimum_principal')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="maximum_principal"
                                       class="control-label">{{trans_choice('loan::general.maximum',1)}} {{trans_choice('loan::general.principal',1)}}</label>
                                <input type="text" name="maximum_principal" value="{{ old('maximum_principal') }}"
                                       id="maximum_principal"
                                       class="form-control numeric @error('maximum_principal') is-invalid @enderror"
                                       v-model="maximum_principal" required>
                                @error('maximum_principal')
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
                                <label for="default_loan_term"
                                       class="control-label">{{trans_choice('loan::general.default',1)}} {{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</label>
                                <input type="text" name="default_loan_term" value="{{ old('default_loan_term') }}"
                                       id="default_loan_term"
                                       class="form-control numeric @error('default_loan_term') is-invalid @enderror"
                                       v-model="default_loan_term" required>
                                @error('default_loan_term')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="minimum_loan_term"
                                       class="control-label">{{trans_choice('loan::general.minimum',1)}} {{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</label>
                                <input type="text" name="minimum_loan_term" value="{{ old('minimum_loan_term') }}"
                                       id="minimum_loan_term"
                                       class="form-control numeric @error('minimum_loan_term') is-invalid @enderror"
                                       v-model="minimum_loan_term" required>
                                @error('minimum_loan_term')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="maximum_loan_term"
                                       class="control-label">{{trans_choice('loan::general.maximum',1)}} {{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</label>
                                <input type="text" name="maximum_loan_term" value="{{ old('maximum_loan_term') }}"
                                       id="maximum_loan_term"
                                       class="form-control numeric @error('maximum_loan_term') is-invalid @enderror"
                                       v-model="maximum_loan_term" required>
                                @error('maximum_loan_term')
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
                                <label for="repayment_frequency"
                                       class="control-label">{{trans_choice('loan::general.repayment',1)}} {{trans_choice('loan::general.frequency',1)}}</label>
                                <input type="text" name="repayment_frequency" value="{{ old('repayment_frequency') }}"
                                       id="repayment_frequency" v-model="repayment_frequency"
                                       class="form-control numeric @error('repayment_frequency') is-invalid @enderror"
                                       required>
                                @error('repayment_frequency')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repayment_frequency_type"
                                       class="control-label">{{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control  @error('repayment_frequency_type') is-invalid @enderror"
                                        name="repayment_frequency_type"
                                        v-model="repayment_frequency_type" id="repayment_frequency_type"
                                        required>
                                    <option value=""></option>
                                    <option value="days">{{trans_choice('loan::general.day',2)}}</option>
                                    <option value="weeks">{{trans_choice('loan::general.week',2)}}</option>
                                    <option value="months">{{trans_choice('loan::general.month',2)}}</option>
                                </select>
                                @error('repayment_frequency_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="default_interest_rate"
                                       class="control-label">{{trans_choice('loan::general.default',1)}} {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}</label>
                                <input type="text" name="default_interest_rate"
                                       value="{{ old('default_interest_rate') }}"
                                       id="default_interest_rate" v-model="default_interest_rate"
                                       class="form-control numeric @error('default_interest_rate') is-invalid @enderror"
                                       required>
                                @error('default_interest_rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="minimum_interest_rate"
                                       class="control-label">{{trans_choice('loan::general.minimum',1)}} {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}</label>
                                <input type="text" name="minimum_interest_rate"
                                       value="{{ old('minimum_interest_rate') }}"
                                       id="minimum_interest_rate" v-model="minimum_interest_rate"
                                       class="form-control numeric @error('minimum_interest_rate') is-invalid @enderror"
                                       required>
                                @error('minimum_interest_rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="maximum_interest_rate"
                                       class="control-label">{{trans_choice('loan::general.maximum',1)}} {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}</label>
                                <input type="text" name="maximum_interest_rate"
                                       value="{{ old('maximum_interest_rate') }}"
                                       id="maximum_interest_rate" v-model="maximum_interest_rate"
                                       class="form-control numeric @error('maximum_interest_rate') is-invalid @enderror"
                                       required>
                                @error('maximum_interest_rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="interest_rate_type"
                                       class="control-label">{{trans_choice('loan::general.per',1)}}</label>
                                <select class="form-control  @error('interest_rate_type') is-invalid @enderror"
                                        name="interest_rate_type" v-model="interest_rate_type"
                                        id="interest_rate_type"
                                        required>
                                    <option value=""></option>
                                    <option value="month">{{trans_choice('loan::general.month',1)}}</option>
                                    <option value="year">{{trans_choice('loan::general.year',1)}}</option>
                                </select>
                                @error('interest_rate_type')
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
                                <label for="grace_on_principal_paid"
                                       class="control-label">{{trans_choice('loan::general.grace_on_principal_paid',1)}}</label>
                                <input type="text" name="grace_on_principal_paid" value="0"
                                       id="grace_on_principal_paid" v-model="grace_on_principal_paid"
                                       class="form-control numeric @error('grace_on_principal_paid') is-invalid @enderror"
                                       required>
                                @error('grace_on_principal_paid')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grace_on_interest_paid"
                                       class="control-label">{{trans_choice('loan::general.grace_on_interest_paid',1)}}</label>
                                <input type="text" name="grace_on_interest_paid" value="0"
                                       id="grace_on_interest_paid" v-model="grace_on_interest_paid"
                                       class="form-control numeric @error('grace_on_interest_paid') is-invalid @enderror"
                                       required>
                                @error('grace_on_interest_paid')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="grace_on_interest_charged"
                                       class="control-label">{{trans_choice('loan::general.grace_on_interest_charged',1)}}</label>
                                <input type="text" name="grace_on_interest_charged" value="0"
                                       id="grace_on_interest_charged" v-model="grace_on_interest_charged"
                                       class="form-control numeric @error('grace_on_interest_charged') is-invalid @enderror"
                                       required>
                                @error('grace_on_interest_charged')
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
                                <label for="interest_methodology"
                                       class="control-label">{{trans_choice('loan::general.interest_methodology',1)}}</label>
                                <select class="form-control  @error('interest_methodology') is-invalid @enderror"
                                        name="interest_methodology" v-model="interest_methodology"
                                        id="interest_methodology"
                                        required>
                                    <option value=""></option>
                                    <option value="flat">{{trans_choice('loan::general.flat',1)}}</option>
                                    <option value="declining_balance">{{trans_choice('loan::general.declining_balance',1)}}</option>
                                </select>
                                @error('interest_methodology')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amortization_method"
                                       class="control-label">{{trans_choice('loan::general.amortization_method',1)}}</label>
                                <select class="form-control  @error('amortization_method') is-invalid @enderror"
                                        name="amortization_method" v-model="amortization_method"
                                        id="amortization_method"
                                        required>
                                    <option value=""></option>
                                    <option value="equal_installments">{{trans_choice('loan::general.equal_installments',1)}}</option>
                                    <option value="equal_principal_payments">{{trans_choice('loan::general.equal_principal_payments',1)}}</option>
                                </select>
                                @error('amortization_method')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loan_transaction_processing_strategy_id"
                                       class="control-label">{{trans_choice('loan::general.loan_transaction_processing_strategy',1)}}</label>
                                <v-select label="name" :options="loan_transaction_processing_strategies"
                                          :reduce="loan_transaction_processing_strategy => loan_transaction_processing_strategy.id"
                                          v-model="loan_transaction_processing_strategy_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('loan_transaction_processing_strategy_id') is-invalid @enderror"
                                                :required="!loan_transaction_processing_strategy_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="loan_transaction_processing_strategy_id"
                                       v-model="loan_transaction_processing_strategy_id">
                                @error('loan_transaction_processing_strategy_id')
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
                                       class="control-label">{{trans_choice('loan::general.charge',2)}}</label>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="credit_checks"
                                       class="control-label">{{trans_choice('loan::general.credit',1)}} {{trans_choice('loan::general.check',2)}}</label>

                                <v-select label="translated_name" :options="credit_checks"
                                          :reduce="credit_check => credit_check.id"
                                          v-model="selected_credit_checks" multiple>
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('credit_checks') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="credit_checks[]"
                                       v-model="selected_credit_checks">
                                @error('credit_checks')
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
                                       class="control-label">{{trans_choice('loan::general.accounting_rule',1)}}</label>
                                <select class="form-control  @error('accounting_rule') is-invalid @enderror"
                                        name="accounting_rule" v-model="accounting_rule"
                                        id="accounting_rule"
                                        required>
                                    <option value=""></option>
                                    <option value="none" selected>{{trans_choice('loan::general.none',1)}}</option>
                                    <option value="cash">{{trans_choice('loan::general.cash',1)}}</option>
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
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fund_source_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.fund_source',1)}}</label>
                                    <v-select label="name" :options="assets"
                                              :reduce="asset => asset.id"
                                              v-model="fund_source_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('fund_source_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="fund_source_chart_of_account_id"
                                           v-model="fund_source_chart_of_account_id">
                                    @error('fund_source_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="loan_portfolio_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.loan_portfolio',1)}}</label>
                                    <v-select label="name" :options="assets"
                                              :reduce="asset => asset.id"
                                              v-model="loan_portfolio_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('loan_portfolio_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="loan_portfolio_chart_of_account_id"
                                           v-model="loan_portfolio_chart_of_account_id">
                                    @error('loan_portfolio_chart_of_account_id')
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
                                    <label for="overpayments_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.overpayment',2)}}</label>
                                    <v-select label="name" :options="liabilities"
                                              :reduce="liability => liability.id"
                                              v-model="overpayments_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('overpayments_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="overpayments_chart_of_account_id"
                                           v-model="overpayments_chart_of_account_id">
                                    @error('overpayments_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="suspended_income_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.suspended_income',1)}}</label>

                                    <v-select label="name" :options="assets"
                                              :reduce="asset => asset.id"
                                              v-model="suspended_income_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('suspended_income_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="suspended_income_chart_of_account_id"
                                           v-model="suspended_income_chart_of_account_id">
                                    @error('suspended_income_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row gy-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="income_from_interest_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.income_from_interest',2)}}</label>
                                    <v-select label="name" :options="income"
                                              :reduce="income => income.id"
                                              v-model="income_from_interest_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('income_from_interest_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="income_from_interest_chart_of_account_id"
                                           v-model="income_from_interest_chart_of_account_id">
                                    @error('income_from_interest_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="income_from_penalties_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.income_from_penalties',2)}}</label>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="income_from_fees_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.income_from_fees',2)}}</label>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="income_from_recovery_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.income_from_recovery',2)}}</label>
                                    <v-select label="name" :options="income"
                                              :reduce="income => income.id"
                                              v-model="income_from_recovery_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('income_from_recovery_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="income_from_recovery_chart_of_account_id"
                                           v-model="income_from_recovery_chart_of_account_id">
                                    @error('income_from_recovery_chart_of_account_id')
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
                                    <label for="losses_written_off_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.losses_written_off',2)}}</label>
                                    <v-select label="name" :options="expenses"
                                              :reduce="expense => expense.id"
                                              v-model="losses_written_off_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('losses_written_off_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="losses_written_off_chart_of_account_id"
                                           v-model="losses_written_off_chart_of_account_id">
                                    @error('losses_written_off_chart_of_account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="interest_written_off_chart_of_account_id"
                                           class="control-label">{{trans_choice('loan::general.interest_written_off',2)}}</label>
                                    <v-select label="name" :options="income"
                                              :reduce="income => income.id"
                                              v-model="interest_written_off_chart_of_account_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('interest_written_off_chart_of_account_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="accounting_rule==='cash'"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="interest_written_off_chart_of_account_id"
                                           v-model="interest_written_off_chart_of_account_id">
                                    @error('interest_written_off_chart_of_account_id')
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
                                <label for="auto_disburse"
                                       class="control-label">{{trans_choice('loan::general.auto_disburse',1)}}</label>
                                <select class="form-control  @error('auto_disburse') is-invalid @enderror"
                                        name="auto_disburse" id="auto_disburse"
                                        v-model="auto_disburse"
                                        required>
                                    <option value=""></option>
                                    <option value="0" selected>{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                                </select>
                                @error('auto_disburse')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="active"
                                       class="control-label">{{trans_choice('core::general.active',1)}}</label>
                                <select class="form-control  @error('active') is-invalid @enderror" name="active"
                                        id="active" v-model="active"
                                        required>
                                    <option value=""></option>
                                    <option value="0">{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1" selected>{{trans_choice('core::general.yes',1)}}</option>
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
            el: '#app',
            data: {
                name: "{{old('name',$loan_product->name)}}",
                short_name: "{{old('short_name',$loan_product->short_name)}}",
                description: "{{old('description',$loan_product->description)}}",
                fund_id: parseInt("{{old('fund_id',$loan_product->fund_id)}}"),
                currency_id: parseInt("{{old('currency_id',$loan_product->currency_id)}}"),
                decimals: "{{old('decimals',$loan_product->decimals)}}",
                minimum_principal: "{{old('minimum_principal',$loan_product->minimum_principal)}}",
                default_principal: "{{old('default_principal',$loan_product->default_principal)}}",
                maximum_principal: "{{old('maximum_principal',$loan_product->maximum_principal)}}",
                minimum_loan_term: "{{old('minimum_loan_term',$loan_product->minimum_loan_term)}}",
                default_loan_term: "{{old('default_loan_term',$loan_product->default_loan_term)}}",
                maximum_loan_term: "{{old('maximum_loan_term',$loan_product->maximum_loan_term)}}",
                repayment_frequency: "{{old('repayment_frequency',$loan_product->repayment_frequency)}}",
                repayment_frequency_type: "{{old('repayment_frequency_type',$loan_product->repayment_frequency_type)}}",
                minimum_interest_rate: "{{old('minimum_interest_rate',$loan_product->minimum_interest_rate)}}",
                default_interest_rate: "{{old('default_interest_rate',$loan_product->default_interest_rate)}}",
                maximum_interest_rate: "{{old('maximum_interest_rate',$loan_product->maximum_interest_rate)}}",
                interest_rate_type: "{{old('interest_rate_type',$loan_product->interest_rate_type)}}",
                grace_on_principal_paid: "{{old('grace_on_principal_paid',$loan_product->grace_on_principal_paid)}}",
                grace_on_interest_paid: "{{old('grace_on_interest_paid',$loan_product->grace_on_interest_paid)}}",
                grace_on_interest_charged: "{{old('grace_on_interest_charged',$loan_product->grace_on_interest_charged)}}",
                interest_methodology: "{{old('interest_methodology',$loan_product->interest_methodology)}}",
                amortization_method: "{{old('amortization_method',$loan_product->amortization_method)}}",
                auto_disburse: "{{old('auto_disburse',$loan_product->auto_disburse)}}",
                selected_credit_checks: [],
                selected_charges: {!! json_encode($selected_charges) !!},
                accounting_rule: "{{old('accounting_rule',$loan_product->accounting_rule)}}",
                active: "{{old('active',$loan_product->active)}}",
                loan_transaction_processing_strategy_id: parseInt("{{old('loan_transaction_processing_strategy_id',$loan_product->loan_transaction_processing_strategy_id)}}"),
                fund_source_chart_of_account_id: parseInt("{{old('fund_source_chart_of_account_id',$loan_product->fund_source_chart_of_account_id)}}"),
                loan_portfolio_chart_of_account_id: parseInt("{{old('loan_portfolio_chart_of_account_id',$loan_product->loan_portfolio_chart_of_account_id)}}"),
                interest_receivable_chart_of_account_id: parseInt("{{old('interest_receivable_chart_of_account_id',$loan_product->interest_receivable_chart_of_account_id)}}"),
                penalties_receivable_chart_of_account_id: parseInt("{{old('penalties_receivable_chart_of_account_id',$loan_product->penalties_receivable_chart_of_account_id)}}"),
                fees_receivable_chart_of_account_id: parseInt("{{old('fees_receivable_chart_of_account_id',$loan_product->fees_receivable_chart_of_account_id)}}"),
                fees_chart_of_account_id: parseInt("{{old('fees_chart_of_account_id',$loan_product->fees_chart_of_account_id)}}"),
                overpayments_chart_of_account_id: parseInt("{{old('overpayments_chart_of_account_id',$loan_product->overpayments_chart_of_account_id)}}"),
                suspended_income_chart_of_account_id: parseInt("{{old('suspended_income_chart_of_account_id',$loan_product->suspended_income_chart_of_account_id)}}"),
                income_from_interest_chart_of_account_id: parseInt("{{old('income_from_interest_chart_of_account_id',$loan_product->income_from_interest_chart_of_account_id)}}"),
                income_from_penalties_chart_of_account_id: parseInt("{{old('income_from_penalties_chart_of_account_id',$loan_product->income_from_penalties_chart_of_account_id)}}"),
                income_from_fees_chart_of_account_id: parseInt("{{old('income_from_fees_chart_of_account_id',$loan_product->income_from_fees_chart_of_account_id)}}"),
                income_from_recovery_chart_of_account_id: parseInt("{{old('income_from_recovery_chart_of_account_id',$loan_product->income_from_recovery_chart_of_account_id)}}"),
                losses_written_off_chart_of_account_id: parseInt("{{old('losses_written_off_chart_of_account_id',$loan_product->losses_written_off_chart_of_account_id)}}"),
                interest_written_off_chart_of_account_id: parseInt("{{old('interest_written_off_chart_of_account_id',$loan_product->interest_written_off_chart_of_account_id)}}"),
                instalment_multiple_of: "{{old('instalment_multiple_of',$loan_product->instalment_multiple_of)}}",
                funds: {!! json_encode($funds) !!},
                currencies: {!! json_encode($currencies) !!},
                credit_checks: {!! json_encode($credit_checks) !!},
                loan_transaction_processing_strategies: {!! json_encode($loan_transaction_processing_strategies) !!},
                assets: {!! json_encode($assets) !!},
                liabilities: {!! json_encode($liabilities) !!},
                income: {!! json_encode($income) !!},
                expenses: {!! json_encode($expenses) !!},
                loan_charges: {!! json_encode($loan_charges) !!},
            },
            methods: {
                onSubmit() {

                }
            },
            computed: {
                available_charges: function () {
                    return this.loan_charges.filter(item => {
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