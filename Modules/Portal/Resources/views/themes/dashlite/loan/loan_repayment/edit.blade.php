@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.repayment',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.repayment',1) }}</h6>

            <div class="box-tools pull-right">

            </div>
        </div>
        <form method="post" action="{{url('loan/repayment/'.$loan_transaction->id.'/update')}}" class="">
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
                    <label class="control-label"
                           for="amount">{{trans_choice('loan::general.amount',1)}}</label>
                    <input type="text" name="amount" class="form-control numeric"
                           placeholder=""
                           value="{{$loan_transaction->amount}}" id="amount" required>
                </div>
                <div class="form-group">
                    <label class="control-label"
                           for="date"> {{trans_choice('core::general.date',1)}}</label>
                    <input type="text" name="date" class="form-control date-picker"
                           placeholder=""
                           value="{{$loan_transaction->submitted_on}}" id="date" required>
                </div>
                <div id="payment_details">
                    <div class="form-group">
                        <label class="control-label"
                               for="payment_type_id">{{trans_choice('core::general.payment',1)}} {{trans_choice('core::general.type',1)}}</label>
                        <select class="form-control select2" name="payment_type_id" id="payment_type_id" required>
                            <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
                            @foreach($payment_types as $key)
                                <option value="{{$key->id}}"
                                        @if($loan_transaction->payment_detail->payment_type_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="account_number">{{trans_choice('core::general.account',1)}}#</label>
                        <input type="text" name="account_number" class="form-control"
                               placeholder=""
                               value="{{$loan_transaction->payment_detail->account_number}}" id="account_number">
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="cheque_number">{{trans_choice('core::general.cheque',1)}}#</label>
                        <input type="text" name="cheque_number" class="form-control"
                               placeholder=""
                               value="{{$loan_transaction->payment_detail->cheque_number}}" id="cheque_number">
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="routing_code">{{trans_choice('core::general.routing_code',1)}}</label>
                        <input type="text" name="routing_code" class="form-control"
                               placeholder=""
                               value="{{$loan_transaction->payment_detail->routing_code}}" id="routing_code">
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="receipt">{{trans_choice('core::general.receipt',1)}}#</label>
                        <input type="text" name="receipt" class="form-control"
                               placeholder=""
                               value="{{$loan_transaction->payment_detail->receipt}}" id="receipt">
                    </div>
                    <div class="form-group">
                        <label class="control-label"
                               for="bank_name">{{trans_choice('core::general.bank',1)}}#</label>
                        <input type="text" name="bank_name" class="form-control"
                               placeholder=""
                               value="{{$loan_transaction->payment_detail->bank_name}}" id="bank_name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="description">{{trans_choice('general.description',1)}}</label>
                    <textarea name="description" class="form-control" id="description">{{$loan_transaction->payment_detail->description}}</textarea>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit"
                        class="btn btn-primary pull-right">{{ trans_choice('general.save',1) }}</button>
            </div>
        </form>
    </div>
    <!-- /.box -->
@endsection

