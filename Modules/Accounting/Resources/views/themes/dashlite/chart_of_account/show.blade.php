@extends('core::layouts.master')
@section('title')
    {{ $chart_of_account->name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h6 class="box-title">{{ $chart_of_account->name }}</h6>

                    <div class="box-tools">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table">
                        <tr>
                            <td>{{ trans_choice('accounting::general.gl_code',2) }}</td>
                            <td>{{$chart_of_account->gl_code}}</td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('core::general.account',1) }} {{ trans_choice('core::general.type',1) }}</td>
                            <td>
                                @if($chart_of_account->account_type=='asset')
                                    {{trans_choice('accounting::general.asset',1)}}
                                @endif
                                @if($chart_of_account->account_type=='expense')
                                    {{trans_choice('accounting::general.expense',1)}}
                                @endif
                                @if($chart_of_account->account_type=='equity')
                                    {{trans_choice('accounting::general.equity',1)}}
                                @endif
                                @if($chart_of_account->account_type=='liability')
                                    {{trans_choice('accounting::general.liability',1)}}
                                @endif
                                @if($chart_of_account->account_type=='income')
                                    {{trans_choice('accounting::general.income',1)}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('accounting::general.manual_entries_allowed',1) }}</td>
                            <td>
                                @if($chart_of_account->allow_manual==1)
                                    {{trans_choice('core::general.yes',1)}}
                                @else
                                    {{trans_choice('core::general.no',1)}}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>{{ trans_choice('core::general.active',1) }} </td>
                            <td>
                                @if($chart_of_account->active==1)
                                    {{ trans_choice('core::general.yes',1) }}
                                @else
                                    {{ trans_choice('core::general.no',1) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('core::general.note',2) }} </td>
                            <td>{!! $chart_of_account->notes !!}</td>
                        </tr>
                    </table>

                </div>
                <!-- /.box-body -->
                <div class="box-footer">

                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
@endsection