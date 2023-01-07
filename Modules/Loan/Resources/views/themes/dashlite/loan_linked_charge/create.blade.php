@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}</h3>
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
        <form method="post" action="{{ url('loan/'.$loan->id.'/charge/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="form-group">
                        <label for="loan_charge_id"
                               class="control-label">{{trans_choice('loan::general.charge',1)}}</label>
                        <select class="form-control  @error('loan_charge_id') is-invalid @enderror" name="loan_charge_id" id="loan_charge_id"
                                v-model="loan_charge_id" v-on:click="change_charge" required>
                            <option value=""></option>
                            <option v-for="(charge,index) in charges" v-bind:value="index">
                                @{{ charge.name }}
                            </option>
                        </select>
                        @error('loan_charge_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="amount" class="control-label">{{trans('core::general.amount')}}</label>
                        <input type="text" name="amount" value="{{ old('amount') }}" id="amount" v-model="amount"
                               class="form-control  @error('amount') is-invalid @enderror numeric" required>
                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="date" class="control-label">{{trans('core::general.date')}}</label>
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
                loan_charge_id: "{{ old('loan_charge_id') }}",
                amount: "{{ old('amount') }}",
                date: "{{ old('date',date("Y-m-d")) }}",
                charges: charges,

            },
            methods: {
                change_charge() {
                    this.amount = charges[this.loan_charge_id].amount;
                },
                onSubmit() {

                }
            }
        })
    </script>
@endsection