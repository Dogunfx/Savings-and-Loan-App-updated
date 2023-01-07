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
                                    href="{{url('loan/application')}}">{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.application',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active"> {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/application/'.$loan_application->id.'/store_approve') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <input type="hidden" name="loan_product_id" value="{{$loan_product->id}}"/>
                    <input type="hidden" name="client_id" value="{{$client->id}}"/>
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
                    <div v-if="stage==1">
                        <h3>{{trans_choice('loan::general.term',2)}}</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="applied_amount"
                                           class="control-label">{{trans_choice('loan::general.principal',1)}}</label>
                                    <input type="number" name="applied_amount"
                                           max="{{ $loan_product->maximum_principal }}"
                                           min="{{ $loan_product->minimum_principal }}"
                                           id="applied_amount"
                                           class="form-control numeric" v-model="applied_amount" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fund_id"
                                           class="control-label">{{trans_choice('loan::general.fund',1)}}</label>
                                    <select class="form-control select2" name="fund_id" id="fund_id" v-model="fund_id"
                                            required>
                                        <option value=""></option>
                                        @foreach($funds as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_term"
                                           class="control-label">{{trans_choice('loan::general.loan',1)}}  {{trans_choice('loan::general.term',1)}}</label>
                                    <input type="text" name="loan_term" max="{{ $loan_product->maximum_loan_term  }}"
                                           min="{{ $loan_product->minimum_loan_term }}"
                                           id="loan_term"
                                           class="form-control numeric" v-model="loan_term" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="repayment_frequency"
                                           class="control-label">{{trans_choice('loan::general.repayment',1)}} {{trans_choice('loan::general.frequency',1)}}</label>
                                    <input type="text" name="repayment_frequency"
                                           id="repayment_frequency" v-model="repayment_frequency"
                                           class="form-control numeric" required>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="interest_rate"
                                           class="control-label">
                                        {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.rate',1)}}
                                        @if($loan_product->interest_rate_type=='month')
                                            (% {{trans_choice('loan::general.per',1)}} {{trans_choice('loan::general.month',1)}}
                                            )
                                        @endif
                                        @if($loan_product->interest_rate_type=='year')
                                            (% {{trans_choice('loan::general.per',1)}} {{trans_choice('loan::general.year',1)}}
                                            )
                                        @endif
                                    </label>
                                    <input type="number" name="interest_rate"
                                           max="{{ $loan_product->maximum_interest_rate  }}"
                                           min="{{ $loan_product->minimum_interest_rate}}"
                                           id="interest_rate" v-model="interest_rate"
                                           class="form-control numeric" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_disbursement_date"
                                           class="control-label">{{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.disbursement',1)}} {{trans_choice('core::general.date',1)}}</label>
                                    <input type="text" name="expected_disbursement_date"
                                           id="expected_disbursement_date"
                                           class="form-control date-picker" v-model="expected_disbursement_date"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="stage==1">
                        <h3>{{trans_choice('core::general.setting',2)}}</h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_officer_id"
                                           class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                                    <select class="form-control select2" name="loan_officer_id" id="loan_officer_id"
                                            v-model="loan_officer_id"
                                            required>
                                        <option value=""></option>
                                        @foreach($users as $key)
                                            <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_purpose_id"
                                           class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.purpose',1)}}</label>
                                    <select class="form-control select2" name="loan_purpose_id" id="loan_purpose_id"
                                            v-model="loan_purpose_id"
                                            required>
                                        <option value=""></option>
                                        @foreach($loan_purposes as $key)
                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="expected_first_payment_date"
                                           class="control-label">{{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.first_payment_date',1)}}</label>
                                    <input type="text" name="expected_first_payment_date"
                                           id="expected_first_payment_date"
                                           class="form-control  date-picker" v-model="expected_first_payment_date"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="stage==1">
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
                                <td>@{{ charge.charge.loan_charge_option_id }}</td>
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
                                <td>@{{ charge.charge.loan_charge_type_id }}</td>
                                <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="loan_charges"
                                           class="control-label">{{trans_choice('loan::general.charge',2)}}</label>
                                    <select class="form-control" name="loan_charges"
                                            id="loan_charges" v-model="selected_charge">
                                        <option value=""></option>
                                        <option v-for="charge in loan_charges" v-bind:value="charge.charge.id">
                                            @{{charge.charge.name }}
                                        </option>
                                    </select>
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
                    <div v-if="stage==5">
                        <h3>{{trans_choice('loan::general.overview',1)}}</h3>
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
                loan_product: {!! $loan_product !!},
                applied_amount: '{{$loan_application->amount}}',
                loan_term: '{{$loan_product->default_loan_term}}',
                repayment_frequency: '{{$loan_product->repayment_frequency}}',
                repayment_frequency_type: '{{$loan_product->repayment_frequency_type}}',
                fund_id: '{{$loan_product->fund_id}}',
                interest_rate: '{{$loan_product->default_interest_rate}}',
                expected_disbursement_date: '{{date("Y-m-d")}}',
                loan_officer_id: '{{$client->loan_officer_id}}',
                expected_first_payment_date: '{{\Illuminate\Support\Carbon::today()->addMonths(1)->format("Y-m-d")}}',
                loan_purpose_id: '',
                loan_charges: charges,
                selected_charge: '',
                selected_charges: [],
                loan_products: loan_products,
                funds: funds,
                loan_purposes: loan_purposes,
                users: users,

            },
            created: function () {
                //this.loan_charges=charges;

            },
            methods: {
                add_charge(event) {
                    if (this.selected_charge != '') {
                        this.selected_charges.push(original_charges[this.selected_charge]);
                        delete charges[this.selected_charge];
                        this.selected_charge = '';
                    } else {
                        alert('Please select a charge')
                    }
                },
                remove_charge(event) {
                    var id = event.currentTarget.getAttribute('data-id');
                    this.selected_charges.splice(id, 1);
                    //charges.push(original_charges[id]);
                },
                onSubmit() {

                }
            }
        });
    </script>
@endsection