@extends('core::layouts.master')
@section('title')
    {{trans_choice('loan::general.loan',1)}} {{trans_choice('report::general.report',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('loan::general.loan',1)}} {{trans_choice('report::general.report',2)}}
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
                        <a href="{{url('report/loan/collection_sheet')}}">{{trans_choice('loan::general.collection_sheet',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('loan::general.collection_sheet_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan/collection_sheet')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan/repayment')}}">{{trans_choice('loan::general.repayment',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('loan::general.repayment_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan/repayment')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan/expected_repayment')}}">{{trans_choice('loan::general.expected',2)}} {{trans_choice('loan::general.repayment',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('loan::general.expected_repayment_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan/expected_repayment')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan/arrears')}}">{{trans_choice('loan::general.arrears',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('loan::general.arrears_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan/arrears')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>

                <tr class="">
                    <td>
                        <a href="{{url('report/loan/disbursement')}}">{{trans_choice('loan::general.disbursement',1)}} {{trans_choice('report::general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('loan::general.disbursement_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan/disbursement')}}"><i
                                    class="fa fa-search"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/loan/portfolio_at_risk')}}">{{trans_choice('loan::general.portfolio_at_risk',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('loan::general.portfolio_at_risk_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan/portfolio_at_risk')}}"><i
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
