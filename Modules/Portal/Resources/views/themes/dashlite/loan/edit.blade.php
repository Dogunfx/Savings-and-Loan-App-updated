@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.product',1) }}
@endsection
@section('content')
    <div class="box box-primary" id="app">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.product',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="post" action="{{ url('loan/product/'.$loan_product->id.'/update') }}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                {{csrf_field()}}
                @if (count($errors) > 0)
                    <div class="form-group has-feedback">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name" class="control-label">{{trans_choice('core::general.name',1)}} </label>
                            <input type="text" name="name" value="{{$loan_product->name }}"
                                   id="name"
                                   class="form-control" v-model="name" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="short_name"
                                   class="control-label">{{trans_choice('loan::general.short_name',1)}}</label>
                            <input type="text" name="short_name" value="{{ old('short_name') }}"
                                   id="short_name"
                                   class="form-control" v-model="short_name" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="description"
                                   class="control-label">{{trans_choice('core::general.description',1)}}</label>
                            <input type="text" name="description" value="{{ old('description') }}"
                                   id="description"
                                   class="form-control" v-model="description" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fund_id"
                                   class="control-label">{{trans_choice('loan::general.fund',1)}}</label>
                            <select class="form-control select2" name="fund_id" id="fund_id" v-model="fund_id" required>
                                <option value=""></option>
                                @foreach($funds as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency_id"
                                   class="control-label">{{trans_choice('core::general.currency',1)}}</label>
                            <select class="form-control" name="currency_id" id="currency_id"
                                    v-model="currency_id" v-on:change="change_currency"
                                    required>
                                <option value=""></option>
                                @foreach($currencies as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="decimals"
                                   class="control-label">{{trans_choice('loan::general.decimal_place',2)}}</label>
                            <input type="text" name="decimals" value="0" v-model="decimals"
                                   id="decimals"
                                   class="form-control numeric">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="default_principal"
                                   class="control-label">{{trans_choice('loan::general.default',1)}} {{trans_choice('loan::general.principal',1)}}</label>
                            <input type="text" name="default_principal" value="{{ old('default_principal') }}"
                                   id="default_principal"
                                   class="form-control numeric" v-model="default_principal" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_principal"
                                   class="control-label">{{trans_choice('loan::general.minimum',1)}} {{trans_choice('loan::general.principal',1)}}</label>
                            <input type="text" name="minimum_principal" value="{{ old('minimum_principal') }}"
                                   id="minimum_principal"
                                   class="form-control numeric" v-model="minimum_principal" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maximum_principal"
                                   class="control-label">{{trans_choice('loan::general.maximum',1)}} {{trans_choice('loan::general.principal',1)}}</label>
                            <input type="text" name="maximum_principal" value="{{ old('maximum_principal') }}"
                                   id="maximum_principal"
                                   class="form-control numeric" v-model="maximum_principal" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="default_loan_term"
                                   class="control-label">{{trans_choice('loan::general.default',1)}} {{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</label>
                            <input type="text" name="default_loan_term" value="{{ old('default_loan_term') }}"
                                   id="default_loan_term"
                                   class="form-control numeric" v-model="default_loan_term" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_loan_term"
                                   class="control-label">{{trans_choice('loan::general.minimum',1)}} {{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</label>
                            <input type="text" name="minimum_loan_term" value="{{ old('minimum_loan_term') }}"
                                   id="minimum_loan_term"
                                   class="form-control numeric" v-model="minimum_loan_term" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maximum_loan_term"
                                   class="control-label">{{trans_choice('loan::general.maximum',1)}} {{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</label>
                            <input type="text" name="maximum_loan_term" value="{{ old('maximum_loan_term') }}"
                                   id="maximum_loan_term"
                                   class="form-control numeric" v-model="maximum_loan_term" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repayment_frequency"
                                   class="control-label">{{trans_choice('loan::general.repayment',1)}} {{trans_choice('loan::general.frequency',1)}}</label>
                            <input type="text" name="repayment_frequency" value="{{ old('repayment_frequency') }}"
                                   id="repayment_frequency" v-model="repayment_frequency"
                                   class="form-control numeric">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repayment_frequency_type"
                                   class="control-label">{{trans_choice('core::general.type',1)}}</label>
                            <select class="form-control " name="repayment_frequency_type"
                                    v-model="repayment_frequency_type" id="repayment_frequency_type"
                                    required>
                                <option value=""></option>
                                <option value="days">{{trans_choice('loan::general.day',2)}}</option>
                                <option value="weeks">{{trans_choice('loan::general.week',2)}}</option>
                                <option value="months">{{trans_choice('loan::general.month',2)}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="default_interest_rate"
                                   class="control-label">{{trans_choice('loan::general.default',1)}} {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}</label>
                            <input type="text" name="default_interest_rate" value="{{ old('default_interest_rate') }}"
                                   id="default_interest_rate" v-model="default_interest_rate"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="minimum_interest_rate"
                                   class="control-label">{{trans_choice('loan::general.minimum',1)}} {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}</label>
                            <input type="text" name="minimum_interest_rate" value="{{ old('minimum_interest_rate') }}"
                                   id="minimum_interest_rate" v-model="minimum_interest_rate"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="maximum_interest_rate"
                                   class="control-label">{{trans_choice('loan::general.maximum',1)}} {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}</label>
                            <input type="text" name="maximum_interest_rate" value="{{ old('maximum_interest_rate') }}"
                                   id="maximum_interest_rate" v-model="maximum_interest_rate"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="interest_rate_type"
                                   class="control-label">{{trans_choice('loan::general.per',1)}}</label>
                            <select class="form-control " name="interest_rate_type" v-model="interest_rate_type"
                                    id="interest_rate_type"
                                    required>
                                <option value=""></option>
                                <option value="month">{{trans_choice('loan::general.month',1)}}</option>
                                <option value="year">{{trans_choice('loan::general.year',1)}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="grace_on_principal_paid"
                                   class="control-label">{{trans_choice('loan::general.grace_on_principal_paid',1)}}</label>
                            <input type="text" name="grace_on_principal_paid" value="0"
                                   id="grace_on_principal_paid" v-model="grace_on_principal_paid"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="grace_on_interest_paid"
                                   class="control-label">{{trans_choice('loan::general.grace_on_interest_paid',1)}}</label>
                            <input type="text" name="grace_on_interest_paid" value="0"
                                   id="grace_on_interest_paid" v-model="grace_on_interest_paid"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="grace_on_interest_charged"
                                   class="control-label">{{trans_choice('loan::general.grace_on_interest_charged',1)}}</label>
                            <input type="text" name="grace_on_interest_charged" value="0"
                                   id="grace_on_interest_charged" v-model="grace_on_interest_charged"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="interest_methodology"
                                   class="control-label">{{trans_choice('loan::general.interest_methodology',1)}}</label>
                            <select class="form-control " name="interest_methodology" v-model="interest_methodology"
                                    id="interest_methodology"
                                    required>
                                <option value=""></option>
                                <option value="flat">{{trans_choice('loan::general.flat',1)}}</option>
                                <option value="declining_balance">{{trans_choice('loan::general.declining_balance',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amortization_method"
                                   class="control-label">{{trans_choice('loan::general.amortization_method',1)}}</label>
                            <select class="form-control " name="amortization_method" v-model="amortization_method"
                                    id="amortization_method"
                                    required>
                                <option value=""></option>
                                <option value="equal_installments">{{trans_choice('loan::general.equal_installments',1)}}</option>
                                <option value="equal_principal_payments">{{trans_choice('loan::general.equal_principal_payments',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="loan_transaction_processing_strategy_id"
                                   class="control-label">{{trans_choice('loan::general.loan_transaction_processing_strategy',1)}}</label>
                            <select class="form-control" name="loan_transaction_processing_strategy_id"
                                    id="loan_transaction_processing_strategy_id"
                                    v-model="loan_transaction_processing_strategy_id" required>
                                <option value=""></option>
                                @foreach($loan_transaction_processing_strategies as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="charges"
                                   class="control-label">{{trans_choice('loan::general.charge',2)}}</label>
                            <select class="form-control select2" name="charges[]"
                                    id="charges" v-model="charges" multiple>
                                <option value=""></option>
                                @foreach($loan_charges as $key)
                                    @if($key->currency_id==$loan_product->currency_id)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endif
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="credit_checks"
                                   class="control-label">{{trans_choice('loan::general.credit',1)}} {{trans_choice('loan::general.check',2)}}</label>
                            <select class="form-control select2" name="credit_checks[]"
                                    id="credit_checks" v-model="credit_checks" multiple>
                                @foreach($credit_checks as $key)
                                    <option value="{{$key->id}}">
                                        {{$key->translated_name}}
                                        @if($key->security_level=='block')
                                            ({{ trans_choice('loan::general.block',1) }}
                                            )
                                        @endif
                                        @if($key->security_level=='pass')
                                            ({{ trans_choice('loan::general.pass',1) }})
                                        @endif
                                        @if($key->security_level=='warning')
                                            ({{ trans_choice('loan::general.warning',1) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <h3>{{trans_choice('accounting::general.accounting',1)}}</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="accounting_rule"
                                   class="control-label">{{trans_choice('loan::general.accounting_rule',1)}}</label>
                            <select class="form-control " name="accounting_rule" v-model="accounting_rule"
                                    id="accounting_rule"
                                    required>
                                <option value=""></option>
                                <option value="none" selected>{{trans_choice('loan::general.none',1)}}</option>
                                <option value="cash">{{trans_choice('loan::general.cash',1)}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="accounting_rule_div" v-if="accounting_rule==='cash'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fund_source_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.fund_source',1)}}</label>
                                <select class="form-control select2" name="fund_source_chart_of_account_id"
                                        v-model="fund_source_chart_of_account_id"
                                        id="fund_source_chart_of_account_id" v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($assets as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loan_portfolio_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.loan_portfolio',1)}}</label>
                                <select class="form-control select2" name="loan_portfolio_chart_of_account_id"
                                        v-model="loan_portfolio_chart_of_account_id"
                                        id="loan_portfolio_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($assets as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="overpayments_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.overpayment',2)}}</label>
                                <select class="form-control select2" name="overpayments_chart_of_account_id"
                                        v-model="overpayments_chart_of_account_id"
                                        id="overpayments_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($liabilities as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="suspended_income_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.suspended_income',1)}}</label>
                                <select class="form-control select2" name="suspended_income_chart_of_account_id"
                                        v-model="suspended_income_chart_of_account_id"
                                        id="suspended_income_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($assets as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="income_from_interest_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.income_from_interest',2)}}</label>
                                <select class="form-control select2" name="income_from_interest_chart_of_account_id"
                                        v-model="income_from_interest_chart_of_account_id"
                                        id="income_from_interest_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($income as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="income_from_penalties_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.income_from_penalties',2)}}</label>
                                <select class="form-control select2" name="income_from_penalties_chart_of_account_id"
                                        v-model="income_from_penalties_chart_of_account_id"
                                        id="income_from_penalties_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($income as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="income_from_fees_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.income_from_fees',2)}}</label>
                                <select class="form-control select2" name="income_from_fees_chart_of_account_id"
                                        v-model="income_from_fees_chart_of_account_id"
                                        id="income_from_fees_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($income as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="income_from_recovery_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.income_from_recovery',2)}}</label>
                                <select class="form-control select2" name="income_from_recovery_chart_of_account_id"
                                        id="income_from_recovery_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($income as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="losses_written_off_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.losses_written_off',2)}}</label>
                                <select class="form-control select2" name="losses_written_off_chart_of_account_id"
                                        v-model="losses_written_off_chart_of_account_id"
                                        id="losses_written_off_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($expenses as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interest_written_off_chart_of_account_id"
                                       class="control-label">{{trans_choice('loan::general.interest_written_off',2)}}</label>
                                <select class="form-control select2" name="interest_written_off_chart_of_account_id"
                                        v-model="interest_written_off_chart_of_account_id"
                                        id="interest_written_off_chart_of_account_id"
                                        v-bind:required="accounting_rule==='cash'">
                                    <option value=""></option>
                                    @foreach($income as $key)
                                        <option value="{{$key->id}}">{{$key->name}}({{$key->gl_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="auto_disburse"
                                   class="control-label">{{trans_choice('loan::general.auto_disburse',1)}}</label>
                            <select class="form-control " name="auto_disburse" id="auto_disburse"
                                    v-model="auto_disburse"
                                    required>
                                <option value=""></option>
                                <option value="0" selected>{{trans_choice('core::general.no',1)}}</option>
                                <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                            </select>
                        </div>
                    </div>
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
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>

            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                name: '{{$loan_product->name }}',
                short_name: '{{$loan_product->short_name }}',
                description: '{{$loan_product->description }}',
                fund_id: '{{$loan_product->fund_id }}',
                currency_id: '{{$loan_product->currency_id }}',
                decimals: '{{$loan_product->decimals }}',
                minimum_principal: '{{$loan_product->minimum_principal }}',
                default_principal: '{{$loan_product->default_principal }}',
                maximum_principal: '{{$loan_product->maximum_principal }}',
                minimum_loan_term: '{{$loan_product->minimum_loan_term }}',
                default_loan_term: '{{$loan_product->default_loan_term }}',
                maximum_loan_term: '{{$loan_product->maximum_loan_term }}',
                repayment_frequency: '{{$loan_product->repayment_frequency }}',
                repayment_frequency_type: '{{$loan_product->repayment_frequency_type }}',
                minimum_interest_rate: '{{$loan_product->minimum_interest_rate }}',
                default_interest_rate: '{{$loan_product->default_interest_rate }}',
                maximum_interest_rate: '{{$loan_product->maximum_interest_rate }}',
                interest_rate_type: '{{$loan_product->interest_rate_type }}',
                grace_on_principal_paid: '{{$loan_product->grace_on_principal_paid }}',
                grace_on_interest_paid: '{{$loan_product->grace_on_interest_paid }}',
                grace_on_interest_charged: '{{$loan_product->grace_on_interest_charged }}',
                interest_methodology: '{{$loan_product->interest_methodology }}',
                amortization_method: '{{$loan_product->amortization_method }}',
                auto_disburse: '{{$loan_product->auto_disburse }}',
                credit_checks: '{{$loan_product->credit_checks }}',
                charges:selected_charges,
                accounting_rule: '{{$loan_product->accounting_rule }}',
                active: '{{$loan_product->active }}',
                loan_transaction_processing_strategy_id: '{{$loan_product->loan_transaction_processing_strategy_id }}',
                fund_source_chart_of_account_id: '{{$loan_product->fund_source_chart_of_account_id }}',
                loan_portfolio_chart_of_account_id: '{{$loan_product->loan_portfolio_chart_of_account_id }}',
                interest_receivable_chart_of_account_id: '{{$loan_product->interest_receivable_chart_of_account_id }}',
                penalties_receivable_chart_of_account_id: '{{$loan_product->penalties_receivable_chart_of_account_id }}',
                fees_receivable_chart_of_account_id: '{{$loan_product->fees_receivable_chart_of_account_id }}',
                fees_chart_of_account_id: '{{$loan_product->fees_chart_of_account_id }}',
                overpayments_chart_of_account_id: '{{$loan_product->overpayments_chart_of_account_id }}',
                suspended_income_chart_of_account_id: '{{$loan_product->suspended_income_chart_of_account_id }}',
                income_from_interest_chart_of_account_id: '{{$loan_product->income_from_interest_chart_of_account_id }}',
                income_from_penalties_chart_of_account_id: '{{$loan_product->income_from_penalties_chart_of_account_id }}',
                income_from_fees_chart_of_account_id: '{{$loan_product->income_from_fees_chart_of_account_id }}',
                income_from_recovery_chart_of_account_id: '{{$loan_product->income_from_recovery_chart_of_account_id }}',
                losses_written_off_chart_of_account_id: '{{$loan_product->losses_written_off_chart_of_account_id }}',
                interest_written_off_chart_of_account_id: '{{$loan_product->interest_written_off_chart_of_account_id }}',
                instalment_multiple_of: '{{$loan_product->instalment_multiple_of }}',

            },
            methods: {
                change_currency() {
                    var charge_options = '';
                    for (let i = 0; i < loan_charges.length; i++) {
                        if (loan_charges[i].id == this.currency_id) {
                            charge_options += "<option value='" + loan_charges[i].id + "'>" + loan_charges[i].name + "</option>";
                        }
                    }
                    $("#charges").html(charge_options);
                },
                onSubmit() {

                }
            }
        })
    </script>
@endsection