@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.savings',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('savings::general.savings',1) }}</h3>
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
        <form method="post" action="{{ url('savings/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
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
                                                id="client_id"
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="savings_product_id"
                                       class="control-label">{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.product',1)}}</label>
                                <v-select label="name" :options="savings_products"
                                          :reduce="savings_product => savings_product.id"
                                          v-on:input="change_product"
                                          v-model="savings_product_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('savings_product_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                id="savings_product_id"
                                                v-bind:required="!savings_product_id"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="savings_product_id"
                                       v-model="savings_product_id">
                                @error('savings_product_id')
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
                                <label for="savings_officer_id"
                                       class="control-label"> {{trans_choice('savings::general.savings_officer',1)}}</label>
                                <v-select label="full_name" :options="users"
                                          :reduce="user => user.id"
                                          v-model="savings_officer_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('savings_officer_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-bind:required="!savings_officer_id"
                                                id="savings_officer_id"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="savings_officer_id"
                                       v-model="savings_officer_id">
                                @error('savings_officer_id')
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
                                <label for="interest_rate"
                                       class="control-label">{{trans_choice('savings::general.interest_rate',1)}}</label>
                                <input type="text" name="interest_rate" v-model="interest_rate"
                                       id="interest_rate" required
                                       class="form-control @error('interest_rate') is-invalid @enderror numeric">
                                @error('interest_rate')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="automatic_opening_balance"
                                       class="control-label">{{trans_choice('savings::general.automatic_opening_balance',1)}}</label>
                                <input type="text" name="automatic_opening_balance"
                                       id="automatic_opening_balance"
                                       class="form-control @error('automatic_opening_balance') is-invalid @enderror numeric" v-model="automatic_opening_balance" required>
                                @error('automatic_opening_balance')
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
                                       class="control-label">{{trans_choice('savings::general.lockin_type',1)}}</label>
                                <select class="form-control @error('lockin_type') is-invalid @enderror " name="lockin_type" v-model="lockin_type"
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
                    <h3>{{trans_choice('savings::general.charge',2)}}</h3>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{trans_choice('core::general.name',1)}}</th>
                            <th>{{trans_choice('core::general.type',1)}}</th>
                            <th>{{trans_choice('core::general.amount',1)}}</th>
                            <th>{{trans_choice('savings::general.collected_on',1)}}</th>
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
                            <td>
                                <span v-if="charge.savings_charge_type_id==1">{{trans_choice('savings::general.savings_activation', 1)}}</span>
                                <span v-if="charge.savings_charge_type_id==2">{{trans_choice('savings::general.specified_due_date', 1)}}</span>
                                <span v-if="charge.savings_charge_type_id==3">{{trans_choice('savings::general.withdrawal_fee', 1)}}</span>
                                <span v-if="charge.savings_charge_type_id==4">{{trans_choice('savings::general.annual_fee', 1) }}</span>
                                <span v-if="charge.savings_charge_type_id==5">{{trans_choice('savings::general.monthly_fee', 1)}}</span>
                                <span v-if="charge.savings_charge_type_id==6">{{trans_choice('savings::general.inactivity_fee', 1)}}</span>
                                <span v-if="charge.savings_charge_type_id==7">{{trans_choice('savings::general.quarterly_fee', 1)}}</span>
                            </td>
                            <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="savings_charges"
                                       class="control-label">{{trans_choice('savings::general.charge',2)}}</label>
                                <select class="form-control" name="savings_charges"
                                        id="savings_charges" v-model="selected_charge">
                                    <option value=""></option>
                                    <option v-for="(charge,index) in savings_product_charges" v-bind:value="index">
                                        @{{charge.charge.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label"></label>
                            <button type="button" v-on:click="add_charge"
                                    class="btn btn-info"
                                    style="margin-top:20px">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.to',1)}} {{trans_choice('savings::general.product',1)}}</button>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="submitted_on_date"
                                       class="control-label">{{trans_choice('savings::general.submitted_on',1)}}</label>
                                <flat-pickr
                                        v-model="submitted_on_date"
                                        class="form-control  @error('submitted_on_date') is-invalid @enderror"
                                        name="submitted_on_date" id="submitted_on_date" required>
                                </flat-pickr>
                                @error('submitted_on_date')
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
                client_id: parseInt("{{old('client_id')}}"),
                savings_officer_id: parseInt("{{old('savings_officer_id')}}"),
                savings_product_id: parseInt("{{old('savings_product_id')}}"),
                interest_rate: "{{old('interest_rate')}}",
                lockin_period: "{{old('lockin_period')}}",
                lockin_type: "{{old('lockin_type')}}",
                automatic_opening_balance: "{{old('automatic_opening_balance')}}",
                submitted_on_date: "{{old('submitted_on_date',date("Y-m-d"))}}",
                overdraft_limit: "{{old('overdraft_limit')}}",
                overdraft_interest_rate: "{{old('overdraft_interest_rate')}}",
                minimum_overdraft_for_interest: "{{old('minimum_overdraft_for_interest')}}",
                savings_products: savings_products,
                savings_product_charges: [],
                savings_charges: savings_charges,
                clients: clients,
                users: users,
                selected_charge: '',
                savings_product: '',
                selected_charges: []
            },
            methods: {
                change_client() {
                    this.savings_officer_id = "";
                    if (this.client_id != "") {
                        this.clients.forEach(item => {
                            if (item.id == this.client_id) {
                                this.savings_officer_id = item.loan_officer_id;
                            }
                        })
                    }
                },
                change_product() {
                    if (this.savings_product_id != "") {
                        this.savings_products.forEach(item => {
                            if (item.id == this.savings_product_id) {
                                this.savings_product = item;
                                this.interest_rate = item.default_interest_rate;
                                this.automatic_opening_balance = item.automatic_opening_balance;
                                this.lockin_period = item.lockin_period;
                                this.lockin_type = item.lockin_type;
                                this.savings_product_charges = item.charges
                            }
                        })
                    }

                },
                onSubmit() {

                },
                add_charge(event) {
                    this.selected_charges.push(savings_charges[this.selected_charge]);
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