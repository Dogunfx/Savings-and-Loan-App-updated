@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('share::general.share',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('savings::general.savings',1) }}</h3>
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
        <form method="post" action="{{ url('share/'.$share->id.'/update') }}">
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
                                <label for="share_product_id"
                                       class="control-label">{{trans_choice('share::general.share',1)}} {{trans_choice('share::general.product',1)}}</label>
                                <v-select label="name" :options="share_products"
                                          :reduce="share_product => share_product.id"
                                          v-on:input="change_product"
                                          v-model="share_product_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('share_product_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                id="share_product_id"
                                                v-bind:required="!share_product_id"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="share_product_id"
                                       v-model="share_product_id">
                                @error('share_product_id')
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="savings_id"
                                       class="control-label"> {{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.account',1)}}</label>
                                <select class="form-control" name="savings_id" id="savings_id"
                                        v-model="savings_id"
                                        required>
                                    <option value=""></option>
                                    <option v-for="savings in selected_savings" v-bind:value="savings.id">
                                        #@{{ savings.id }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="minimum_active_period"
                                       class="control-label">{{trans_choice('share::general.minimum_active_period',1)}}</label>
                                <input type="text" name="minimum_active_period"
                                       id="minimum_active_period" v-model="minimum_active_period"
                                       class="form-control @error('minimum_active_period') is-invalid @enderror numeric"
                                       required>
                                @error('minimum_active_period')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="minimum_active_period_type"
                                       class="control-label">{{trans_choice('share::general.minimum_active_period_type',1)}}</label>
                                <select class="form-control @error('minimum_active_period_type') is-invalid @enderror "
                                        name="minimum_active_period_type" v-model="minimum_active_period_type"
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
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lockin_period"
                                       class="control-label">{{trans_choice('share::general.lockin_period',1)}}</label>
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
                                       class="control-label">{{trans_choice('share::general.lockin_type',1)}}</label>
                                <select class="form-control @error('lockin_type') is-invalid @enderror "
                                        name="lockin_type" v-model="lockin_type"
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
                    <h3>{{trans_choice('share::general.charge',2)}}</h3>
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
                            <td>@{{ charge.share_charge_option_id }}</td>
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
                                <span v-if="charge.share_charge_type_id==1">{{trans_choice('share::general.share_account_activation', 1)}}</span>
                                <span v-if="charge.share_charge_type_id==2">{{trans_choice('share::general.share_purchase', 1)}}</span>
                                <span v-if="charge.share_charge_type_id==3">{{trans_choice('share::general.share_redeem', 1)}}</span>
                            </td>
                            <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loan_charges"
                                       class="control-label">{{trans_choice('share::general.charge',2)}}</label>
                                <select class="form-control" name="loan_charges"
                                        id="loan_charges" v-model="selected_charge">
                                    <option value=""></option>
                                    <option v-for="(charge,index) in share_product_charges" v-bind:value="index">
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
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="submitted_on_date"
                                       class="control-label">{{trans_choice('share::general.submitted_on',1)}}</label>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="application_date"
                                       class="control-label">{{trans_choice('share::general.application_date',1)}}</label>
                                <flat-pickr
                                        v-model="application_date"
                                        class="form-control  @error('application_date') is-invalid @enderror"
                                        name="application_date" id="application_date" required>
                                </flat-pickr>
                                @error('application_date')
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
                client_id:  parseInt("{{old('client_id',$share->client_id)}}"),
                share_officer_id: parseInt("{{old('share_officer_id',$share->share_officer_id)}}"),
                share_product_id: parseInt("{{old('share_product_id',$share->share_product_id)}}"),
                savings_id: parseInt("{{old('savings_id',$share->savings_id)}}"),
                total_shares: "{{old('total_shares',$share->total_shares)}}",
                lockin_period: "{{old('lockin_period',$share->lockin_period)}}",
                lockin_type: "{{old('lockin_type',$share->lockin_type)}}",
                external_id:"{{old('external_id',$share->external_id)}}",
                charges: '',
                submitted_on_date:"{{old('submitted_on_date',$share->submitted_on_date)}}",
                application_date: "{{old('application_date',$share->application_date)}}",
                minimum_active_period: "{{old('minimum_active_period',$share->minimum_active_period)}}",
                minimum_active_period_type: "{{old('minimum_active_period_type',$share->minimum_active_period_type)}}",
                share_products: share_products,
                share_product: {!!json_encode( $share->share_product )!!},
                savings: savings,
                share_product_charges: {!! json_encode($share->share_product->charges) !!},
                share_charges: share_charges,
                selected_savings: {!! json_encode($share->client->savings->where('status','active')) !!},
                clients: clients,
                selected_charge: '',
                selected_charges: {!! json_encode($charges_list) !!}
            },
            methods: {
                change_client() {
                    this.selected_savings = [];
                    if (this.client_id != "") {
                        this.clients.forEach(item => {
                            if (item.id == this.client_id) {
                                if (this.share_product) {
                                    this.savings.forEach((savings) => {
                                        if (savings.client_id == this.client_id && this.share_product.currency_id == savings.currency_id) {
                                            this.selected_savings.push(savings);
                                        }
                                    });
                                }
                            }
                        })
                    }
                },
                change_product() {
                    if (this.share_product_id != "") {
                        this.share_products.forEach(item => {
                            if (item.id == this.share_product_id) {
                                this.share_product = item;
                                this.total_shares = item.default_shares_per_client;
                                this.automatic_opening_balance = item.automatic_opening_balance;
                                this.lockin_period = item.lockin_period;
                                this.lockin_type = item.lockin_type;
                                this.minimum_active_period = item.minimum_active_period;
                                this.minimum_active_period_type = item.minimum_active_period_type;
                                this.share_product_charges = item.charges
                                this.selected_savings = [];
                                this.savings.forEach((savings) => {
                                    if (savings.client_id == this.client_id && savings.currency_id == item.currency_id) {
                                        this.selected_savings.push(savings);
                                    }
                                });
                            }
                        })
                    }
                },
                onSubmit() {

                },
                add_charge(event) {
                    if (share_charges[this.selected_charge]) {
                        this.selected_charges.push(share_charges[this.selected_charge]);
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