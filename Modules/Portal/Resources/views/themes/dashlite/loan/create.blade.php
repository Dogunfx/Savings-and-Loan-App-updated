@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.loan',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="get" action="{{ url('loan/create_client_loan') }}">
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
                    <label for="client_id"
                           class="control-label">{{trans_choice('client::general.client',1)}}</label>
                    <select class="form-control select2" name="client_id" id="client_id" required>
                        <option value=""></option>
                        @foreach($clients as $key)
                            <option value="{{$key->id}}">{{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                (#{{$key->id}})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan_product_id"
                           class="control-label">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.product',1)}}</label>
                    <select class="form-control select2" name="loan_product_id" id="loan_product_id" required>
                        <option value=""></option>
                        @foreach($loan_products as $key)
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">

                <button type="submit"
                        class="btn btn-primary pull-right">{{trans_choice('core::general.next',1)}}</button>

            </div>
        </form>
    </div>
@endsection
@section('scripts')

@endsection