@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.create',1) }} {{ trans_choice('loan::general.application',1) }}
@endsection
@section('content')
    <div class="box box-primary" id="app">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.create',1) }} {{ trans_choice('loan::general.application',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="post" action="{{ url('portal/loan/application/store') }}">
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
                <div class="form-group">
                    <label for="loan_product_id"
                           class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.product',1)}}</label>
                    <select class="form-control" name="loan_product_id" id="loan_product_id" v-on:change="change_product"
                            v-model="loan_product_id" required>
                        <option value=""></option>
                        @foreach($loan_products as $key)
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label">{{trans_choice('loan::general.amount',1)}}</label>
                    <input type="number" name="amount" id="amount" v-bind:max="max_amount" v-bind:min="min_amount"
                           class="form-control numeric" v-model="amount" required>
                </div>
                <div class="form-group">

                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label">{{trans_choice('core::general.note',2)}}</label>
                    <textarea type="text" name="notes" id="notes" class="form-control"
                              rows="3">{{ old('notes') }}</textarea>
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

                amount: '',
                loan_term: '',
                repayment_frequency: '',
                repayment_frequency_type: '',
                loan_product_id: '',
                interest_rate: '',
                max_amount: '',
                min_amount: '',
                loan_products: loan_products,

            },
            created: function () {
                //this.loan_charges=charges;

            },
            methods: {
                change_product(event) {
                    if (this.loan_product_id != '') {
                        var loan_product = loan_products[this.loan_product_id];
                        this.amount = loan_product.default_principal;
                        this.max_amount = loan_product.maximum_principal;
                        this.min_amount = loan_product.minimum_principal;
                    } else {
                        alert('Please select a product')
                    }
                },
                onSubmit() {

                }
            }
        });
    </script>
@endsection