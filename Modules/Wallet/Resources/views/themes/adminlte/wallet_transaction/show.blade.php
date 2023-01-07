@extends('core::layouts.master')
@section('title')
    {{ trans_choice('wallet::general.transaction',1) }}  {{ trans_choice('core::general.detail',2) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('wallet::general.transaction',1) }}  {{ trans_choice('core::general.detail',2) }}
                        <a href="{{url('wallet/transaction/'.$wallet_transaction->id.'/pdf')}}" target="_blank"
                           class="btn btn-info btn-sm">{{ trans_choice('core::general.pdf',1) }}</a>
                        <a href="{{url('wallet/transaction/'.$wallet_transaction->id.'/print')}}" target="_blank"
                           class="btn btn-info btn-sm">{{ trans_choice('core::general.print',1) }}</a>
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
                                    href="{{url('wallet/'.$wallet_transaction->wallet_id.'/show')}}">{{ trans_choice('wallet::general.wallet',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('wallet::general.transaction',1) }}  {{ trans_choice('core::general.detail',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card-body p-0">

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
    </section>
@endsection
@section('scripts')

@endsection