@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.loan',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.loan',1) }}</h3>
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
        <form method="post" action="{{ url('loan/'.$loan->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <input type="hidden" name="loan_product_id" value="{{$loan_product->id}}"/>
                    <input type="hidden" name="client_id" value="{{$client->id}}"/>
                    <div v-if="stage==1">
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
                                <div class="form-group">
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
                        <div v-if="stage==1">
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
                            @foreach($custom_fields as $custom_field)
                                <?php
                                $field = custom_field_build_form_field($custom_field, $loan->id);
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
                                    <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i>
                                    </td>
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
                        <div v-if="stage==5">
                            <h3>{{trans_choice('loan::general.overview',1)}}</h3>
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
                stage: 1,
                loan_product: {!! $loan_product !!},
                applied_amount: "{{old('applied_amount',$loan->applied_amount)}}",
                loan_term: "{{old('loan_term',$loan->loan_term)}}",
                repayment_frequency: "{{old('repayment_frequency',$loan->repayment_frequency)}}",
                repayment_frequency_type: "{{old('repayment_frequency_type',$loan->repayment_frequency_type)}}",
                fund_id: parseInt("{{old('fund_id',$loan->fund_id)}}"),
                interest_rate: "{{old('interest_rate',$loan->interest_rate)}}",
                expected_disbursement_date: "{{old('expected_disbursement_date',$loan->expected_disbursement_date)}}",
                loan_officer_id: "{{old('loan_officer_id',$loan->loan_officer_id)}}",
                expected_first_payment_date: "{{old('expected_first_payment_date',$loan->expected_first_payment_date)}}",
                loan_purpose_id: parseInt("{{old('loan_purpose_id',$loan->loan_purpose_id)}}"),
                loan_charges: charges,
                funds: funds,
                loan_purposes: loan_purposes,
                selected_charge: "",
                selected_charges: charges_list

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