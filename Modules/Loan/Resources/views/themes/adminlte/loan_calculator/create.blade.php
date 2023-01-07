@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.calculator',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.calculator',1) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.calculator',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/calculator') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <input name="loan_product_id" v-model="loan_product_id" type="hidden">
                    <div class="form-group">
                        <label for="loan_product"
                               class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.product',1)}}</label>
                        <v-select label="name" :options="loan_products"
                                  :reduce="loan_product => loan_product.id"
                                  v-on:input="change_loan_product"
                                  v-model="loan_product_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('loan_product_id') is-invalid @enderror"
                                        v-bind="attributes"
                                        v-bind:required="!loan_product_id"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="loan_product_id"
                               v-model="loan_product_id">
                        @error('loan_product_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="applied_amount"
                                       class="control-label">{{trans_choice('loan::general.principal',1)}}</label>
                                <input type="text" name="applied_amount"
                                       id="applied_amount"
                                       class="form-control @error('applied_amount') is-invalid @enderror numeric"
                                       v-model="applied_amount" required>
                                @error('applied_amount')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="loan_term"
                                       class="control-label">{{trans_choice('loan::general.loan',1)}}  {{trans_choice('loan::general.term',1)}}</label>
                                <input type="text" name="loan_term"
                                       id="loan_term"
                                       class="form-control @error('loan_term') is-invalid @enderror numeric"
                                       v-model="loan_term" required>
                                @error('loan_term')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="repayment_frequency"
                                       class="control-label">{{trans_choice('loan::general.repayment',1)}} {{trans_choice('loan::general.frequency',1)}}</label>
                                <input type="text" name="repayment_frequency"
                                       id="repayment_frequency" v-model="repayment_frequency"
                                       class="form-control @error('repayment_frequency') is-invalid @enderror numeric"
                                       required>
                                @error('repayment_frequency')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
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
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interest_rate"
                                       class="control-label">
                                    {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}

                                </label>
                                <input type="text" name="interest_rate"
                                       id="interest_rate" v-model="interest_rate"
                                       class="form-control @error('interest_rate') is-invalid @enderror text" required>
                                @error('interest_rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expected_disbursement_date"
                                       class="control-label">{{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.disbursement',1)}} {{trans_choice('core::general.date',1)}}</label>
                                <flat-pickr
                                        v-model="expected_disbursement_date"
                                        class="form-control  @error('expected_disbursement_date') is-invalid @enderror"
                                        name="expected_disbursement_date" id="expected_disbursement_date" required>
                                </flat-pickr>
                                @error('expected_disbursement_date')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expected_first_payment_date"
                                       class="control-label">{{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.first_payment_date',1)}}</label>
                                <flat-pickr
                                        v-model="expected_first_payment_date"
                                        class="form-control  @error('expected_first_payment_date') is-invalid @enderror"
                                        name="expected_first_payment_date" id="expected_first_payment_date" required>
                                </flat-pickr>
                                @error('expected_first_payment_date')
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
                            class="btn btn-primary  float-right">{{trans_choice('loan::general.calculate',1)}}</button>
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
                loan_product: "{{old('loan_product')}}",
                loan_product_id: parseInt("{{old('loan_product_id')}}"),
                applied_amount: "{{old('applied_amount')}}",
                loan_term: "{{old('loan_term')}}",
                repayment_frequency: "{{old('repayment_frequency')}}",
                repayment_frequency_type: "{{old('repayment_frequency_type')}}",
                interest_rate: "{{old('interest_rate')}}",
                expected_disbursement_date: "{{old('expected_disbursement_date',date("Y-m-d"))}}",
                expected_first_payment_date: "{{old('expected_first_payment_date',\Illuminate\Support\Carbon::today()->addMonths(1)->format("Y-m-d"))}}",
                loan_products: loan_products,

            },
            created: function () {
                //this.loan_charges=charges;

            },
            methods: {
                change_loan_product() {

                    if (this.loan_product_id != "") {
                        this.loan_products.forEach(item => {
                            if (item.id == this.loan_product_id) {
                                this.loan_product = item;
                                this.applied_amount = this.loan_product.default_principal;
                                this.loan_term = this.loan_product.default_loan_term;
                                this.repayment_frequency = this.loan_product.repayment_frequency;
                                this.repayment_frequency_type = this.loan_product.repayment_frequency_type;
                                this.interest_rate = this.loan_product.default_interest_rate;
                            }
                        })
                    }

                },
                calculate() {

                }
            }
        });
    </script>
@endsection