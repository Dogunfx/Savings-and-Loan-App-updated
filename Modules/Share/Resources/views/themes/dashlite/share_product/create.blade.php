@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('share::general.product',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('share::general.product',1) }}</h3>
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
        <form method="post" action="{{ url('share/product/store') }}">
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
                            <label for="total_shares"
                                   class="control-label">{{trans_choice('share::general.total_shares',1)}}</label>
                            <input type="text" name="total_shares" v-model="total_shares"
                                   id="total_shares" required
                                   class="form-control @error('total_shares') is-invalid @enderror numeric">
                            @error('total_shares')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="shares_to_be_issued"
                                   class="control-label">{{trans_choice('share::general.shares_to_be_issued',1)}}</label>
                            <input type="text" name="shares_to_be_issued" v-model="shares_to_be_issued"
                                   id="shares_to_be_issued" required
                                   class="form-control @error('shares_to_be_issued') is-invalid @enderror numeric">
                            @error('shares_to_be_issued')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nominal_price"
                                   class="control-label">{{trans_choice('share::general.nominal_price',1)}}</label>
                            <input type="text" name="nominal_price" v-model="nominal_price"
                                   id="nominal_price" required
                                   class="form-control @error('nominal_price') is-invalid @enderror numeric">
                            @error('nominal_price')
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
                            <label for="minimum_shares_per_client"
                                   class="control-label">{{trans_choice('share::general.minimum_shares_per_client',1)}}</label>
                            <input type="text" name="minimum_shares_per_client" v-model="minimum_shares_per_client"
                                   id="minimum_shares_per_client" required
                                   class="form-control @error('minimum_shares_per_client') is-invalid @enderror numeric">
                            @error('minimum_shares_per_client')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="default_shares_per_client"
                                   class="control-label">{{trans_choice('share::general.default_shares_per_client',1)}}</label>
                            <input type="text" name="default_shares_per_client" v-model="default_shares_per_client"
                                   id="default_shares_per_client" required
                                   class="form-control @error('default_shares_per_client') is-invalid @enderror numeric">
                            @error('default_shares_per_client')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maximum_shares_per_client"
                                   class="control-label">{{trans_choice('share::general.maximum_shares_per_client',1)}}</label>
                            <input type="text" name="maximum_shares_per_client" v-model="maximum_shares_per_client"
                                   id="maximum_shares_per_client" required
                                   class="form-control @error('maximum_shares_per_client') is-invalid @enderror numeric">
                            @error('maximum_shares_per_client')
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
                                   class="control-label">{{trans_choice('share::general.lockin_period',1)}}</label>
                            <input type="text" name="lockin_period"
                                   id="lockin_period" v-model="lockin_period"
                                   class="form-control @error('lockin_period') is-invalid @enderror numeric" required>
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
                                   class="control-label">{{trans_choice('share::general.lockin_type',1)}}</label>
                            <select class="form-control @error('lockin_type') is-invalid @enderror " name="lockin_type" v-model="lockin_type"
                                    id="lockin_type"
                                    required>
                                <option value=""></option>
                                <option value="days">{{trans_choice('share::general.day',2)}}</option>
                                <option value="weeks">{{trans_choice('share::general.week',2)}}</option>
                                <option value="months">{{trans_choice('share::general.month',2)}}</option>
                                <option value="years">{{trans_choice('share::general.year',2)}}</option>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_active_period"
                                   class="control-label">{{trans_choice('share::general.minimum_active_period',1)}}</label>
                            <input type="text" name="minimum_active_period"
                                   id="minimum_active_period" v-model="minimum_active_period"
                                   class="form-control @error('minimum_active_period') is-invalid @enderror numeric" required>
                            @error('minimum_active_period')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="minimum_active_period_type"
                                   class="control-label">{{trans_choice('share::general.minimum_active_period_type',1)}}</label>
                            <select class="form-control @error('minimum_active_period_type') is-invalid @enderror " name="minimum_active_period_type" v-model="minimum_active_period_type"
                                    id="minimum_active_period_type"
                                    required>
                                <option value=""></option>
                                <option value="days">{{trans_choice('share::general.day',2)}}</option>
                                <option value="weeks">{{trans_choice('share::general.week',2)}}</option>
                                <option value="months">{{trans_choice('share::general.month',2)}}</option>
                                <option value="years">{{trans_choice('share::general.year',2)}}</option>
                            </select>
                            @error('minimum_active_period_type')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="allow_dividends_for_inactive_clients"
                                   class="control-label">{{trans_choice('share::general.allow_dividends_for_inactive_clients',1)}}</label>
                            <select class="form-control @error('allow_dividends_for_inactive_clients') is-invalid @enderror " name="allow_dividends_for_inactive_clients" v-model="allow_dividends_for_inactive_clients"
                                    id="allow_dividends_for_inactive_clients"
                                    required>
                                <option value="0">{{trans_choice('core::general.no',1)}}</option>
                                <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                            </select>
                            @error('allow_dividends_for_inactive_clients')
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
                                   class="control-label">{{trans_choice('share::general.charge',2)}}</label>
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
                                   class="control-label">{{trans_choice('share::general.accounting_rule',1)}}</label>
                            <select class="form-control @error('accounting_rule') is-invalid @enderror " name="accounting_rule" v-model="accounting_rule"
                                    id="accounting_rule"
                                    required>
                                <option value=""></option>
                                <option value="none" selected>{{trans_choice('share::general.none',1)}}</option>
                                <option value="cash">{{trans_choice('share::general.cash',1)}}</option>
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
                                <label for="share_reference_chart_of_account_id"
                                       class="control-label">{{trans_choice('share::general.share_reference',1)}}</label>
                                <v-select label="name" :options="assets"
                                          :reduce="asset => asset.id"
                                          v-model="share_reference_chart_of_account_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('share_reference_chart_of_account_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                :required="accounting_rule==='cash'"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="share_reference_chart_of_account_id"
                                       v-model="share_reference_chart_of_account_id">
                                @error('share_reference_chart_of_account_id')
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
                                <label for="share_suspense_control_chart_of_account_id"
                                       class="control-label">{{trans_choice('share::general.share_suspense_control',2)}}</label>
                                <v-select label="name" :options="liabilities"
                                          :reduce="liability => liability.id"
                                          v-model="share_suspense_control_chart_of_account_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('share_suspense_control_chart_of_account_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                :required="accounting_rule==='cash'"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="share_suspense_control_chart_of_account_id"
                                       v-model="share_suspense_control_chart_of_account_id">
                                @error('share_suspense_control_chart_of_account_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <h4>{{trans_choice('accounting::general.equity',2)}}</h4>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="equity_chart_of_account_id"
                                       class="control-label">{{trans_choice('accounting::general.equity',2)}}</label>
                                <v-select label="name" :options="equities"
                                          :reduce="equity => equity.id"
                                          v-model="equity_chart_of_account_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('equity_chart_of_account_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                :required="accounting_rule==='cash'"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="equity_chart_of_account_id"
                                       v-model="equity_chart_of_account_id">
                                @error('equity_chart_of_account_id')
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
                                       class="control-label">{{trans_choice('share::general.income_from_fees',2)}}</label>
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
                    </div>
                </div>
                <div class="row gy-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="active"
                                   class="control-label">{{trans_choice('core::general.active',1)}}</label>
                            <select class="form-control @error('active') is-invalid @enderror " name="active" id="active" v-model="active"
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
            </div>
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
                total_shares: "{{old('total_shares')}}",
                shares_to_be_issued: "{{old('shares_to_be_issued')}}",
                nominal_price: "{{old('nominal_price')}}",
                capital_value: "{{old('capital_value',0)}}",
                minimum_shares_per_client: "{{old('minimum_shares_per_client')}}",
                default_shares_per_client: "{{old('default_shares_per_client')}}",
                maximum_shares_per_client: "{{old('maximum_shares_per_client')}}",
                maximum_interest_rate: "{{old('maximum_interest_rate')}}",
                minimum_active_period: "{{old('minimum_active_period')}}",
                minimum_active_period_type: "{{old('minimum_active_period_type')}}",
                allow_dividends_for_inactive_clients: "{{old('allow_dividends_for_inactive_clients',0)}}",
                lockin_period: "{{old('lockin_period',0)}}",
                lockin_type: "{{old('lockin_type','days')}}",
                share_reference_chart_of_account_id:  parseInt("{{old('share_reference_chart_of_account_id')}}"),
                share_suspense_control_chart_of_account_id:  parseInt("{{old('share_suspense_control_chart_of_account_id')}}"),
                equity_chart_of_account_id:  parseInt("{{old('equity_chart_of_account_id')}}"),
                income_from_fees_chart_of_account_id: parseInt( "{{old('income_from_fees_chart_of_account_id')}}"),
                selected_charges: [],
                accounting_rule:"{{old('accounting_rule','none')}}",
                active: "{{old('active',1)}}",
                currencies: {!! json_encode($currencies) !!},
                assets: {!! json_encode($assets) !!},
                liabilities: {!! json_encode($liabilities) !!},
                income: {!! json_encode($income) !!},
                expenses: {!! json_encode($expenses) !!},
                equities: {!! json_encode($equities) !!},
                share_charges: {!! json_encode($share_charges) !!},
            },
            methods: {
                change_currency() {
                    var charge_options = '';
                    for (let i = 0; i < share_charges.length; i++) {
                        if (share_charges[i].id == this.currency_id) {
                            charge_options += "<option value='" + share_charges[i].id + "'>" + share_charges[i].name + "</option>";
                        }
                    }
                    $("#charges").html(charge_options);
                },
                onSubmit() {

                }
            }, computed: {
                available_charges: function () {
                    return this.share_charges.filter(item => {
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