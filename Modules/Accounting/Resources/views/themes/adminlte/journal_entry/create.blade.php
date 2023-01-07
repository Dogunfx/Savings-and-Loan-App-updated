@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('accounting::general.journal',1) }} {{ trans_choice('core::general.entry',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('accounting::general.journal',1) }} {{ trans_choice('core::general.entry',1) }}
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
                                    href="{{url('accounting/journal_entry')}}">{{ trans_choice('accounting::general.journal',1) }} {{ trans_choice('core::general.entry',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('accounting::general.journal',1) }} {{ trans_choice('core::general.entry',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{url('accounting/journal_entry/store')}}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="branch_id"
                                       class="control-label">{{trans_choice('core::general.branch',1)}}</label>
                                <select class="form-control @error('branch_id') is-invalid @enderror" name="branch_id"
                                        id="branch_id" v-model="branch_id" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($branches as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="currency_id"
                                       class="control-label">{{trans_choice('core::general.currency',1)}}</label>
                                <select class="form-control @error('currency_id') is-invalid @enderror"
                                        name="currency_id"
                                        id="currency_id" v-model="currency_id" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($currencies as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="amount"
                                       class="control-label">{{trans_choice('core::general.amount',1)}}</label>
                                <input type="text" name="amount" v-model="amount"
                                       id="amount"
                                       class="form-control numeric @error('amount') is-invalid @enderror" required>
                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="debit"
                                       class="control-label">{{trans_choice('accounting::general.debit',1)}}</label>
                                <select class="form-control @error('debit') is-invalid @enderror" name="debit"
                                        id="debit" v-model="debit" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($chart_of_accounts as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('debit')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="credit"
                                       class="control-label">{{trans_choice('accounting::general.credit',1)}}</label>
                                <select class="form-control @error('credit') is-invalid @enderror" name="credit"
                                        id="credit" v-model="credit" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($chart_of_accounts as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('credit')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="date"
                                       class="control-label">{{trans_choice('core::general.date',1)}}</label>
                                <flat-pickr
                                        v-model="date"
                                        class="form-control  @error('date') is-invalid @enderror"
                                        name="date" required>
                                </flat-pickr>
                                @error('date')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reference"
                                       class="control-label">{{trans_choice('accounting::general.reference',1)}}</label>
                                <input type="text" name="reference" v-model="reference"
                                       id="reference"
                                       class="form-control @error('reference') is-invalid @enderror">
                                @error('reference')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="payment_type_id"
                                       class="control-label">{{trans_choice('core::general.payment',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control @error('payment_type_id') is-invalid @enderror"
                                        name="payment_type_id"
                                        id="payment_type_id" v-model="payment_type_id" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($payment_types as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('payment_type_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="account_number"
                                       class="control-label">{{trans_choice('core::general.account',1)}}#</label>
                                <input type="text" name="account_number" v-model="account_number"
                                       id="account_number"
                                       class="form-control @error('account_number') is-invalid @enderror">
                                @error('account_number')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="routing_code"
                                       class="control-label">{{trans_choice('core::general.routing_code',1)}}</label>
                                <input type="text" name="routing_code" v-model="routing_code"
                                       id="routing_code"
                                       class="form-control @error('routing_code') is-invalid @enderror">
                                @error('routing_code')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="receipt"
                                       class="control-label">{{trans_choice('core::general.receipt',1)}}</label>
                                <input type="text" name="receipt" v-model="receipt"
                                       id="receipt"
                                       class="form-control @error('receipt') is-invalid @enderror">
                                @error('receipt')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="bank_name"
                                       class="control-label">{{trans_choice('core::general.bank',1)}}</label>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes"
                                       class="control-label">{{trans_choice('core::general.note',2)}}</label>
                                <textarea type="text" name="notes" v-model="notes"
                                          id="notes"
                                          class="form-control @error('notes') is-invalid @enderror">
                                </textarea>
                                @error('notes')
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
            </div><!-- .card-preview -->
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                branch_id: "{{old('branch_id')}}",
                currency_id: "{{old('currency_id')}}",
                debit: "{{old('debit')}}",
                credit: "{{old('credit')}}",
                amount: "{{old('amount')}}",
                reference: "{{old('reference')}}",
                date: "{{old('date',date('Y-m-d'))}}",
                payment_type_id: "{{old('payment_type_id')}}",
                routing_code: "{{old('routing_code')}}",
                account_number: "{{old('account_number')}}",
                receipt: "{{old('receipt')}}",
                bank_name: "{{old('bank_name')}}",
                notes: "{{old('notes')}}",
            }
        })
    </script>
@endsection
