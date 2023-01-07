@extends('core::layouts.master')
@section('title')
    {{trans_choice('savings::general.savings',1)}} {{trans_choice('report::general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('savings::general.savings',1)}} {{trans_choice('report::general.report',2)}}
            </h6>

            <div class="box-tools pull-right hidden-print">
            </div>
        </div>
        <div class="box-body">
            <table id="" class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('core::general.name',1)}}</th>
                    <th>{{trans_choice('core::general.description',1)}}</th>
                    <th>{{trans_choice('core::general.action',1)}}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <a href="{{url('report/savings/transaction')}}">{{trans_choice('savings::general.transaction',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('savings::general.savings_transaction_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings/transaction')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/savings/balance')}}">{{trans_choice('savings::general.balance',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('savings::general.savings_balance_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings/balance')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/savings/account')}}">{{trans_choice('savings::general.savings',2)}} {{trans_choice('savings::general.account',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('savings::general.savings_account_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings/account')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>

                </tbody>
            </table>

        </div>
        <!-- /.box-body -->

    </div>
@endsection
@section('footer-scripts')

@endsection
