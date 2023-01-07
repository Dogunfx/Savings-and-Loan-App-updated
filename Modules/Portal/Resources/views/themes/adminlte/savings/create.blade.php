@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.savings',1) }}
@endsection
@section('content')
    <div class="box box-primary" id="app">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.savings',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="post" action="{{ url('savings/store') }}">
            {{csrf_field()}}
            <div class="box-body">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id"
                                   class="control-label">{{trans_choice('client::general.client',1)}}</label>
                            <select class="form-control" name="client_id" id="client_id" v-model="client_id"
                                    v-on:change="change_client"
                                    required>
                                <option value=""></option>
                                @foreach($clients as $key)
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                        (#{{$key->id}})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="savings_product_id"
                                   class="control-label">{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.product',1)}}</label>
                            <select class="form-control" name="savings_product_id" v-model="savings_product_id"
                                    id="savings_product_id"
                                    v-on:change="change_product" required>
                                <option value=""></option>
                                @foreach($savings_products as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="savings_officer_id"
                                   class="control-label"> {{trans_choice('savings::general.savings_officer',1)}}</label>
                            <select class="form-control" name="savings_officer_id" id="savings_officer_id"
                                    v-model="savings_officer_id"
                                    required>
                                <option value=""></option>
                                @foreach($users as $key)
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="interest_rate"
                                   class="control-label">{{trans_choice('savings::general.interest_rate',1)}}</label>
                            <input type="text" name="interest_rate" v-model="interest_rate"
                                   id="interest_rate" required
                                   class="form-control numeric">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="automatic_opening_balance"
                                   class="control-label">{{trans_choice('savings::general.automatic_opening_balance',1)}}</label>
                            <input type="text" name="automatic_opening_balance"
                                   id="automatic_opening_balance"
                                   class="form-control numeric" v-model="automatic_opening_balance" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lockin_period"
                                   class="control-label">{{trans_choice('savings::general.lockin_period',1)}}</label>
                            <input type="text" name="lockin_period"
                                   id="lockin_period" v-model="lockin_period"
                                   class="form-control numeric" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lockin_type"
                                   class="control-label">{{trans_choice('savings::general.lockin_type',1)}}</label>
                            <select class="form-control " name="lockin_type" v-model="lockin_type"
                                    id="lockin_type"
                                    required>
                                <option value=""></option>
                                <option value="days">{{trans_choice('savings::general.day',2)}}</option>
                                <option value="weeks">{{trans_choice('savings::general.week',2)}}</option>
                                <option value="months">{{trans_choice('savings::general.month',2)}}</option>
                                <option value="years">{{trans_choice('savings::general.year',2)}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <h3>{{trans_choice('savings::general.charge',2)}}</h3>
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
                    <tr v-for="(charge,index) in selected_charges" v-bind:id="charge.id">
                        <td>@{{ charge.name }}</td>
                        <td>@{{ charge.savings_charge_option_id }}</td>
                        <td>
                                <span v-if="charge.allow_override=='0'">
                                    <input v-bind:name="'charges['+charge.id+']'" type="hidden"
                                           v-bind:value="charge.amount">
                                    @{{ charge.amount }}
                                </span>
                            <span v-if="charge.allow_override=='1'">
                                    <input v-bind:name="'charges['+charge.id+']'" type="number"
                                           class="form-control numeric" v-bind:value="charge.amount" required>
                                </span>
                        </td>
                        <td>@{{ charge.savings_charge_type_id }}</td>
                        <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i></td>
                    </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="loan_charges"
                                   class="control-label">{{trans_choice('savings::general.charge',2)}}</label>
                            <select class="form-control" name="loan_charges"
                                    id="loan_charges" v-model="selected_charge">
                                <option value=""></option>
                                <option v-for="charge in savings_product_charges" v-bind:value="charge.charge.id">
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="submitted_on_date"
                                   class="control-label">{{trans_choice('savings::general.submitted_on',1)}}</label>
                            <input type="text" name="submitted_on_date"
                                   id="submitted_on_date" v-model="submitted_on_date"
                                   class="form-control date-picker" required>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">

                <button type="submit"
                        class="btn btn-primary pull-right">{{trans_choice('core::general.save',1)}}</button>

            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                client_id: '',
                savings_officer_id: '',
                savings_product_id: '',
                interest_rate: '',
                lockin_period: '',
                lockin_type: '',
                automatic_opening_balance: '',
                charges: '',
                submitted_on_date: '{{date("Y-m-d")}}',
                overdraft_limit: '',
                overdraft_interest_rate: '',
                minimum_overdraft_for_interest: '',
                savings_products: savings_products,
                savings_product_charges: [],
                savings_charges: savings_charges,
                clients: clients,
                selected_charge: '',
                selected_charges: []
            },
            methods: {
                change_client() {
                    var client = clients[this.client_id];
                    this.savings_officer_id = client.loan_officer_id;
                },
                change_product() {
                    var savings_product = savings_products[this.savings_product_id];
                    this.interest_rate = savings_product.default_interest_rate;
                    this.automatic_opening_balance = savings_product.automatic_opening_balance;
                    this.lockin_period = savings_product.lockin_period;
                    this.lockin_type = savings_product.lockin_type;
                    this.savings_product_charges=savings_product.charges

                },
                onSubmit() {

                },
                add_charge(event) {
                    if (this.selected_charge != '') {
                        this.selected_charges.push(savings_charges[this.selected_charge]);
                        //delete charges[this.selected_charge];
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
            }
        })
    </script>
@endsection