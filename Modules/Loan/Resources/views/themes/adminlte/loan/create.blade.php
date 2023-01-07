@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}
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
                                    href="{{url('loan')}}">{{ trans_choice('loan::general.loan',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_type"
                                       class="control-label">{{trans_choice('client::general.client',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control @error('client_type') is-invalid @enderror"
                                        name="client_type" id="client_type" v-model="client_type"
                                        required>
                                    <option value=""></option>
                                    <option value="client">{{trans_choice('client::general.client',1)}}</option>
                                </select>
                                @error('client_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" v-if="client_type=='client'">
                                <label for="client_id"
                                       class="control-label">{{trans_choice('client::general.client',1)}}</label>
                                <v-select label="name_id" :options="clients"
                                          :reduce="client => client.id"
                                          v-on:input="change_client"
                                          v-model="client_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('client_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-bind:required="!client_id"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="client_id"
                                       v-model="client_id">
                                @error('client_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan_product_id"
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
                        </div>
                    </div>
                    <div v-show="loan_product">
                        <h3>{{trans_choice('loan::general.term',2)}}</h3>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="applied_amount"
                                           class="control-label">{{trans_choice('loan::general.principal',1)}}</label>
                                    <input type="number" name="applied_amount"
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
                            <div class="col-md-6">
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
                                                    v-bind="attributes"
                                                    v-bind:required="!fund_id"
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
                        </div>
                        <div class="row gy-4">
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
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group" v-if="loan_product">
                                    <label for="interest_rate"
                                           class="control-label">
                                        {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}
                                        <span v-if="loan_product.interest_rate_type=='month'">
                                        (% {{trans_choice('loan::general.per',1)}} {{trans_choice('loan::general.month',1)}})
                                            </span>

                                        <span v-if="loan_product.interest_rate_type=='year'">
                                        (% {{trans_choice('loan::general.per',1)}} {{trans_choice('loan::general.year',1)}}
                                        )
                                        </span>
                                    </label>
                                    <input type="text" name="interest_rate"
                                           id="interest_rate" v-model="interest_rate"
                                           class="form-control @error('interest_rate') is-invalid @enderror text"
                                           required>
                                    @error('interest_rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
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
                        </div>
                        @foreach($custom_fields as $custom_field)
                            <?php
                            $field = custom_field_build_form_field($custom_field);
                            ?>
                            <div class="row gy-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @if($custom_field->type=='radio')
                                            <label class="control-label"
                                                   for="field_{{$custom_field->id}}">{{$field['label']}}</label>
                                            {!! $field['html'] !!}
                                        @else
                                            <label class="control-label"
                                                   for="field_{{$custom_field->id}}">{{$field['label']}}</label>
                                            {!! $field['html'] !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <h3>{{trans_choice('core::general.setting',2)}}</h3>
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_officer_id"
                                           class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                                    <v-select label="full_name" :options="users"
                                              :reduce="user => user.id"
                                              v-model="loan_officer_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('loan_officer_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="!loan_officer_id"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="loan_officer_id"
                                           v-model="loan_officer_id">
                                    @error('loan_officer_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_purpose_id"
                                           class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.purpose',1)}}</label>
                                    <v-select label="name" :options="loan_purposes"
                                              :reduce="loan_purpose => loan_purpose.id"
                                              v-model="loan_purpose_id">
                                        <template #search="{attributes, events}">
                                            <input
                                                    autocomplete="off"
                                                    class="vs__search @error('loan_purpose_id') is-invalid @enderror"
                                                    v-bind="attributes"
                                                    v-bind:required="!loan_purpose_id"
                                                    v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="loan_purpose_id"
                                           v-model="loan_purpose_id">
                                    @error('loan_purpose_id')
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
                                            name="expected_first_payment_date" id="expected_first_payment_date"
                                            required>
                                    </flat-pickr>
                                    @error('expected_first_payment_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <h3>{{trans_choice('loan::general.charge',2)}}</h3>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>{{trans_choice('core::general.name',1)}}</th>
                                <th>{{trans_choice('core::general.type',1)}}</th>
                                <th>{{trans_choice('core::general.amount',1)}}</th>
                                <th>{{trans_choice('loan::general.collected_on',1)}}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="charges_table_body">
                            <tr v-for="(charge,index) in selected_charges" v-bind:id="charge.charge.id">
                                <td>@{{ charge.charge.name }}</td>
                                <td>
                                    <span v-if="charge.charge.loan_charge_option_id==1">{{trans_choice('loan::general.flat', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_option_id==2">{{trans_choice('loan::general.principal_due_on_installment', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_option_id==3">{{trans_choice('loan::general.principal_interest_due_on_installment', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_option_id==4">{{trans_choice('loan::general.interest_due_on_installment', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_option_id==5">{{trans_choice('loan::general.total_outstanding_loan_principal', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_option_id==6">{{trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_option_id==7">{{trans_choice('loan::general.original_loan_principal', 1)}}</span>
                                </td>
                                <td>
                                <span v-if="charge.charge.allow_override=='0'">
                                    <input v-bind:name="'charges['+charge.charge.id+']'" type="hidden"
                                           v-bind:value="charge.charge.amount">
                                    @{{ charge.charge.amount }}
                                </span>
                                    <span v-if="charge.charge.allow_override=='1'">
                                    <input v-bind:name="'charges['+charge.charge.id+']'" type="number"
                                           class="form-control numeric" v-bind:value="charge.charge.amount" required>
                                </span>
                                </td>
                                <td>
                                    <span v-if="charge.charge.loan_charge_type_id==1">{{trans_choice('loan::general.disbursement', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==2">{{trans_choice('loan::general.specified_due_date', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==3">{{trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==4">{{trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==5">{{trans_choice('loan::general.disbursement_paid_with_repayment', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==6">{{trans_choice('loan::general.loan_rescheduling_fee', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==7">{{trans_choice('loan::general.overdue_on_loan_maturity', 1)}}</span>
                                    <span v-if="charge.charge.loan_charge_type_id==8">{{trans_choice('loan::general.last_installment_fee', 1)}}</span>

                                </td>
                                <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="loan_charges"
                                           class="control-label">{{trans_choice('loan::general.charge',2)}}</label>
                                    <select class="form-control @error('loan_charges') is-invalid @enderror"
                                            name="loan_charges"
                                            id="loan_charges" v-model="selected_charge">
                                        <option value=""></option>
                                        <option v-for="(charge,index) in loan_product_charges" v-bind:value="index">
                                            @{{charge.charge.name }}
                                        </option>
                                    </select>
                                    @error('loan_charges')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label"></label>
                                <button type="button" v-on:click="add_charge"
                                        class="btn btn-info"
                                        style="margin-top:20px">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.to',1)}} {{trans_choice('loan::general.product',1)}}</button>
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
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                stage: 1,
                client_type: "{{old('client_type')}}",
                loan_product: "{{old('loan_product')}}",
                loan_product_id: parseInt("{{old('loan_product_id')}}"),
                client_id: parseInt("{{old('client_id')}}"),
                group_id: parseInt("{{old('group_id')}}"),
                applied_amount: "{{old('applied_amount')}}",
                loan_term: "{{old('loan_term')}}",
                repayment_frequency: "{{old('repayment_frequency')}}",
                repayment_frequency_type: "{{old('repayment_frequency_type')}}",
                fund_id: parseInt("{{old('fund_id')}}"),
                interest_rate: "{{old('interest_rate')}}",
                expected_disbursement_date: "{{old('expected_disbursement_date',date("Y-m-d"))}}",
                loan_officer_id: parseInt("{{old('loan_officer_id')}}"),
                expected_first_payment_date: "{{old('expected_first_payment_date',\Illuminate\Support\Carbon::today()->addMonths(1)->format("Y-m-d"))}}",
                loan_purpose_id: parseInt("{{old('loan_purpose_id')}}"),
                loan_charges: loan_charges,
                loan_product_charges: [],
                loan_products: loan_products,
                clients: clients,
                funds: funds,
                loan_purposes: loan_purposes,
                users: users,
                selected_charge: "",
                selected_charges: []

            },
            created: function () {


            },
            methods: {
                add_charge(event) {

                    this.selected_charges.push(this.loan_product_charges[this.selected_charge]);
                    //delete charges[this.selected_charge];
                    this.selected_charge = "";

                },
                remove_charge(event) {
                    var id = event.currentTarget.getAttribute('data-id');
                    this.selected_charges.splice(id, 1);
                    //charges.push(original_charges[id]);
                },
                change_loan_product() {
                    if (this.loan_product_id != "") {
                        this.loan_products.forEach(item => {
                            if (item.id == this.loan_product_id) {
                                this.loan_product = item;
                                this.applied_amount = this.loan_product.default_principal;
                                this.loan_term = this.loan_product.default_loan_term;
                                this.repayment_frequency = this.loan_product.repayment_frequency;
                                this.repayment_frequency_type = this.loan_product.repayment_frequency_type;
                                this.fund_id = this.loan_product.fund_id;
                                this.interest_rate = this.loan_product.default_interest_rate;
                                this.loan_product_charges = this.loan_product.charges;
                            }
                        })
                    }
                },
                change_client() {
                    this.loan_officer_id = "";
                    if (this.client_id != "") {
                        this.clients.forEach(item => {
                            if (item.id == this.client_id) {
                                this.loan_officer_id = item.loan_officer_id;
                            }
                        })
                    }
                }
            }
        });
    </script>
@endsection