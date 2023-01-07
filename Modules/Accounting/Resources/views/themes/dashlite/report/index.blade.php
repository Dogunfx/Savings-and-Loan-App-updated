@extends('core::layouts.master')
@section('title')
    {{trans_choice('accounting::general.accounting',1)}} {{trans_choice('report::general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('accounting::general.accounting',1)}} {{trans_choice('report::general.report',2)}}
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
                        <a href="{{url('report/accounting/balance_sheet')}}">{{trans_choice('accounting::general.balance_sheet',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('accounting::general.balance_sheet_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/accounting/balance_sheet')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/accounting/trial_balance')}}">{{trans_choice('accounting::general.trial_balance',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('accounting::general.trial_balance_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/accounting/trial_balance')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/accounting/income_statement')}}">{{trans_choice('accounting::general.income_statement',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('accounting::general.income_statement_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/accounting/income_statement')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/accounting/cash_flow')}}">{{trans_choice('accounting::general.cash_flow',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('accounting::general.cash_flow_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/accounting/cash_flow')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr class="">
                    <td>
                        <a href="{{url('report/accounting/ledger')}}">{{trans_choice('accounting::general.ledger',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('accounting::general.ledger_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/accounting/ledger')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/accounting/journal')}}">{{trans_choice('accounting::general.journal',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('accounting::general.journal_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/accounting/journal')}}"><i
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
