@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('wallet::general.savings_transfer',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"> {{ trans_choice('core::general.add',1) }} {{ trans_choice('wallet::general.savings_transfer',1) }}</h3>
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
        <form method="post" action="{{url('wallet/'.$wallet->id.'/transfer/savings/store')}}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="form-group">
                        <label class="control-label"
                               for="savings_id">{{trans_choice('savings::general.savings',1)}}</label>
                        <select class="form-control" name="savings_id" id="savings_id" v-model="savings_id" required>
                            <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
                            @foreach($wallet->client->savings as $key)
                                <option value="{{$key->id}}">{{trans_choice('loan::general.loan',1)}} #{{$key->id}}</option>
                            @endforeach

                        </select>
                        @error('savings_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="amount">{{trans_choice('loan::general.amount',1)}}</label>
                        <input type="text" name="amount" id="amount" v-model="amount"
                               class="form-control numeric @error('amount') is-invalid @enderror"  required>
                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="date"> {{trans_choice('core::general.date',1)}}</label>
                        <flat-pickr
                                v-model="date"
                                class="form-control  @error('date') is-invalid @enderror"
                                name="date" id="date" required>
                        </flat-pickr>
                        @error('date')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div id="payment_details">
                        <div class="form-group">
                            <label class="control-label"
                                   for="payment_type_id">{{trans_choice('core::general.payment',1)}} {{trans_choice('core::general.type',1)}}</label>
                            <v-select label="name" :options="payment_types"
                                      :reduce="payment_type => payment_type.id"
                                      v-model="payment_type_id">
                                <template #search="{attributes, events}">
                                    <input
                                            autocomplete="off"
                                            :required="!payment_type_id"
                                            class="vs__search @error('payment_type_id') is-invalid @enderror"
                                            v-bind="attributes"
                                            v-on="events"
                                    />
                                </template>
                            </v-select>
                            <input type="hidden" name="payment_type_id"
                                   v-model="payment_type_id">
                            @error('payment_type_id')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="account_number">{{trans_choice('core::general.account',1)}}#</label>
                            <input type="text" name="account_number" id="account_number" v-model="account_number"
                                   class="form-control @error('account_number') is-invalid @enderror" >
                            @error('account_number')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="cheque_number">{{trans_choice('core::general.cheque',1)}}#</label>
                            <input type="text" name="cheque_number" id="cheque_number" v-model="cheque_number"
                                   class="form-control @error('cheque_number') is-invalid @enderror" >
                            @error('cheque_number')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="routing_code">{{trans_choice('core::general.routing_code',1)}}</label>
                            <input type="text" name="routing_code" id="routing_code" v-model="routing_code"
                                   class="form-control  @error('routing_code') is-invalid @enderror" >
                            @error('routing_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="receipt">{{trans_choice('core::general.receipt',1)}}#</label>
                            <input type="text" name="receipt" id="receipt" v-model="receipt"
                                   class="form-control  @error('receipt') is-invalid @enderror" >
                            @error('receipt')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="bank_name">{{trans_choice('core::general.bank',1)}}#</label>
                            <input type="text" name="bank_name" id="bank_name" v-model="bank_name"
                                   class="form-control @error('bank_name') is-invalid @enderror" >
                            @error('bank_name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">{{trans_choice('general.description',1)}}</label>
                        <textarea type="text" name="description" v-model="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror">
                                </textarea>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.box -->
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                savings_id: "{{old('savings_id')}}",
                amount: "{{old('amount')}}",
                date: "{{old('date',date('Y-m-d'))}}",
                payment_type_id: parseInt("{{old('payment_type_id')}}"),
                account_number: "{{old('account_number')}}",
                cheque_number: "{{old('cheque_number')}}",
                routing_code: "{{old('routing_code')}}",
                receipt: "{{old('receipt')}}",
                bank_name: "{{old('bank_name')}}",
                description: `{{old('description')}}`,
                payment_types: {!! json_encode($payment_types) !!},

            },
            created: function () {

            },
            methods: {
                onSubmit() {

                }
            }
        });
    </script>
@endsection
