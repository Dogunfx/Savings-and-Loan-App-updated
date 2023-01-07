@extends('core::layouts.master')
@section('title')
    {{ trans_choice('wallet::general.transaction',1) }}  {{ trans_choice('core::general.detail',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('wallet::general.transaction',1) }}  {{ trans_choice('core::general.detail',2) }}</h6>

            <div class="box-tools">
                <a href="{{url('wallet/transaction/'.$wallet_transaction->id.'/pdf')}}" target="_blank"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.pdf',1) }}</a>
                <a href="{{url('wallet/transaction/'.$wallet_transaction->id.'/print')}}" target="_blank"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.print',1) }}</a>
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>

        <div class="box-body">

            <table class="table  table-bordered table-hover table-striped" id="">
                <tbody>
                <tr>
                    <td>{{ trans_choice('core::general.id',1) }}</td>
                    <td>{{$wallet_transaction->id}}</td>
                </tr>
                <tr>
                    <td>{{ trans_choice('core::general.type',1) }}</td>
                    <td>
                        @if($wallet_transaction->transaction_type=='deposit')
                            {{ trans_choice('wallet::general.deposit', 1) }}
                        @endif
                        @if($wallet_transaction->transaction_type=='withdrawal')
                            {{ trans_choice('wallet::general.withdrawal', 1) }}
                        @endif
                        @if($wallet_transaction->transaction_type=='savings_transfer')
                            {{ trans_choice('savings::general.savings', 1) }} {{ trans_choice('wallet::general.transfer', 1) }}
                        @endif
                        @if($wallet_transaction->transaction_type=='loan_transfer')
                            {{ trans_choice('loan::general.loan', 1) }} {{ trans_choice('wallet::general.transfer', 1) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>{{ trans_choice('core::general.date',1) }}</td>
                    <td>{{$wallet_transaction->submitted_on}}</td>
                </tr>
                <tr>
                    <td>{{ trans_choice('core::general.amount',1) }}</td>
                    <td>
                        {{number_format($wallet_transaction->amount,$wallet_transaction->wallet->decimals)}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.detail',2) }}</b>
                    </td>
                </tr>
                @if(!empty($wallet_transaction->payment_detail))
                    <tr>
                        <td>{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.type',1) }}</td>
                        <td>
                            @if(!empty($wallet_transaction->payment_detail->payment_type))
                                {{$wallet_transaction->payment_detail->payment_type->name}}
                            @endif
                        </td>
                    </tr>
                    @if(!empty($wallet_transaction->payment_detail->account_number))
                        <tr>
                            <td>{{ trans_choice('core::general.account',1) }}#</td>
                            <td>
                                {{$wallet_transaction->payment_detail->account_number}}
                            </td>
                        </tr>
                    @endif
                    @if(!empty($wallet_transaction->payment_detail->cheque_number))
                        <tr>
                            <td>{{ trans_choice('core::general.cheque',1) }}#</td>
                            <td>
                                {{$wallet_transaction->payment_detail->cheque_number}}
                            </td>
                        </tr>
                    @endif
                    @if(!empty($wallet_transaction->payment_detail->routing_code))
                        <tr>
                            <td>{{ trans_choice('core::general.routing_code',1) }}</td>
                            <td>
                                {{$wallet_transaction->payment_detail->routing_code}}
                            </td>
                        </tr>
                    @endif
                    @if(!empty($wallet_transaction->payment_detail->receipt))
                        <tr>
                            <td>{{ trans_choice('core::general.receipt',1) }}#</td>
                            <td>
                                {{$wallet_transaction->payment_detail->receipt}}
                            </td>
                        </tr>
                    @endif
                    @if(!empty($wallet_transaction->payment_detail->bank_name))
                        <tr>
                            <td>{{ trans_choice('core::general.bank',1) }}#</td>
                            <td>
                                {{$wallet_transaction->payment_detail->bank_name}}
                            </td>
                        </tr>
                    @endif
                    @if(!empty($wallet_transaction->payment_detail->description))
                        <tr>
                            <td>{{ trans_choice('core::general.description',1) }}</td>
                            <td>
                                {{$wallet_transaction->payment_detail->description}}
                            </td>
                        </tr>
                    @endif
                @endif
                </tbody>
            </table>
        </div>

    </div>
@endsection
@section('scripts')

@endsection