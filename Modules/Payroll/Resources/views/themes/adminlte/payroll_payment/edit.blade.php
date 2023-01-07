@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.payment',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }}  {{ trans_choice('core::general.payment',1) }}
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
                                    href="{{url('payroll/'.$payroll_payment->payroll_id.'/show')}}">{{ trans_choice('payroll::general.payroll',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }}  {{ trans_choice('core::general.payment',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{url('payroll/payment/'.$payroll_payment->id.'/update')}}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="form-group">
                        <label class="control-label"
                               for="amount">{{trans_choice('payroll::general.amount',1)}}</label>
                        <input type="text" name="amount" v-model="amount"
                               id="amount"
                               class="form-control numeric @error('amount') is-invalid @enderror" required>
                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="date"
                               class="control-label">{{trans_choice('core::general.date',1)}}</label>
                        <flat-pickr v-model="date" name="date" id="date"
                                    class="form-control  @error('date') is-invalid @enderror" required></flat-pickr>
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
                                            class="vs__search @error('payment_type_id') is-invalid @enderror"
                                            :required="!payment_type_id"
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
                            <input type="text" name="account_number" v-model="account_number"
                                   id="account_number"
                                   class="form-control @error('account_number') is-invalid @enderror">
                            @error('account_number')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="cheque_number">{{trans_choice('core::general.cheque',1)}}#</label>
                            <input type="text" name="cheque_number" v-model="cheque_number"
                                   id="cheque_number"
                                   class="form-control @error('cheque_number') is-invalid @enderror">
                            @error('cheque_number')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="routing_code">{{trans_choice('core::general.routing_code',1)}}</label>
                            <input type="text" name="routing_code" v-model="routing_code"
                                   id="routing_code"
                                   class="form-control @error('routing_code') is-invalid @enderror">
                            @error('routing_code')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="receipt">{{trans_choice('core::general.receipt',1)}}#</label>
                            <input type="text" name="receipt" v-model="receipt"
                                   id="receipt"
                                   class="form-control @error('receipt') is-invalid @enderror">
                            @error('receipt')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label"
                                   for="bank_name">{{trans_choice('core::general.bank',1)}}#</label>
                            <input type="text" name="bank_name" v-model="bank_name"
                                   id="bank_name"
                                   class="form-control @error('bank_name') is-invalid @enderror">
                            @error('bank_name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description"
                               class="control-label">{{trans_choice('core::general.description',1)}}</label>
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
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                amount: "{{old('amount',$payroll_payment->amount)}}",
                date: "{{old('date',$payroll_payment->submitted_on)}}",
                payment_type_id: parseInt("{{old('payment_type_id',$payroll_payment->payment_detail->payment_type_id)}}"),
                account_number: "{{old('account_number',$payroll_payment->payment_detail->account_number)}}",
                cheque_number: "{{old('cheque_number',$payroll_payment->payment_detail->cheque_number)}}",
                routing_code: "{{old('routing_code',$payroll_payment->payment_detail->routing_code)}}",
                description: `{{old('description',$payroll_payment->payment_detail->description)}}`,
                receipt: "{{old('receipt',$payroll_payment->payment_detail->receipt)}}",
                bank_name: "{{old('bank_name',$payroll_payment->payment_detail->bank_name)}}",
                payment_types: {!! json_encode($payment_types) !!},
            },
            methods: {}
        })
    </script>
@endsection
