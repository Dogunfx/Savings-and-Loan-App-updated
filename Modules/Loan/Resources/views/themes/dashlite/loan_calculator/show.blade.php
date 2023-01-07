@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.calculator',1) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.calculator',1) }}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <a href="#" onclick="window.print()"
                       class="btn btn-info btn-sm">{{ trans_choice('core::general.print',1) }}</a>
                    <a href="#" onclick="window.history.back()"
                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                    </a>

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="pretty displayschedule" id="repaymentschedule" style="margin-top: 20px;">
                    <colgroup span="2"></colgroup>
                    <colgroup span="3">
                        <col class="lefthighlightcol">
                        <col>
                        <col class="righthighlightcol">
                    </colgroup>
                    <colgroup span="3">
                        <col class="lefthighlightcol">
                        <col>
                        <col class="righthighlightcol">
                    </colgroup>
                    <colgroup span="3"></colgroup>
                    <thead>
                    <tr>
                        <th class="empty" scope="colgroup" colspan="3">&nbsp;</th>
                        <th class="highlightcol" scope="colgroup"
                            colspan="3">{{trans_choice('loan::general.loan_amount_and_balance',1)}}</th>
                        <th class="highlightcol" scope="colgroup"
                            colspan="3">{{trans_choice('loan::general.total_cost_of_loan',1)}}</th>
                    </tr>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{trans_choice('core::general.date',1)}}</th>
                        <th scope="col"># {{trans_choice('loan::general.day',2)}}</th>
                        <th class="lefthighlightcolheader"
                            scope="col">{{trans_choice('loan::general.disbursement',1)}}</th>
                        <th scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.due',1)}}</th>
                        <th class="righthighlightcolheader"
                            scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.balance',1)}}</th>

                        <th class="lefthighlightcolheader"
                            scope="col">{{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.due',1)}}</th>
                        <th scope="col">{{trans_choice('loan::general.fee',2)}}</th>
                        <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.due',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td scope="row"></td>
                        <td>{{$loan_details["disbursement_date"]}}</td>
                        <td></td>
                        <td class="lefthighlightcolheader">{{number_format($loan_details["principal"],$loan_details["decimals"])}}</td>
                        <td></td>
                        <td class="righthighlightcolheader">{{number_format($loan_details["principal"],$loan_details["decimals"])}}</td>
                        <td class="lefthighlightcolheader"></td>
                        <td>{{number_format($loan_details["disbursement_fees"],$loan_details["decimals"])}}</td>
                        <td>{{number_format($loan_details["disbursement_fees"],$loan_details["decimals"])}}</td>
                    </tr>
                    <?php
                    $count = 1;
                    $total_days = 0;
                    $total_principal = 0;
                    $total_interest = 0;
                    $total_fees = 0;
                    $total_penalties = 0;
                    $total_due = 0;
                    $balance = $loan_details["principal"]
                    ?>
                    @foreach($schedules as $key)
                        <?php
                        $days = \Carbon\Carbon::parse($key['due_date'])->diffInDays(\Illuminate\Support\Carbon::parse($key['from_date']));
                        $total_days = $total_days + $days;
                        $balance = $balance - $key['principal'];
                        $principal = $key['principal'];
                        $interest = $key['interest'];
                        $fees = $key['fees'];
                        $due = $principal + $interest + $fees;

                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;
                        $total_due = $total_due + $due;

                        ?>
                        <tr>
                            <td scope="row">{{$count}}</td>
                            <td>{{$key['due_date']}}</td>
                            <td>{{$days}}</td>

                            <td class="lefthighlightcolheader"></td>
                            <td>{{number_format($principal,$loan_details["decimals"])}}</td>
                            <td class="righthighlightcolheader">{{number_format($balance,$loan_details["decimals"])}}</td>
                            <td class="lefthighlightcolheader">
                                {{number_format($interest,$loan_details["decimals"])}}
                            </td>
                            <td>{{number_format($fees,$loan_details["decimals"])}}</td>
                            <td>{{number_format($due,$loan_details["decimals"])}}</td>
                        </tr>
                        <?php
                        $count++;
                        ?>
                    @endforeach
                    </tbody>
                    <tfoot class="ui-widget-header">
                    <tr>
                        <th colspan="2">{{trans_choice('loan::general.total',1)}}</th>
                        <th>{{$total_days}}</th>
                        <th class="lefthighlightcolheader">{{number_format($loan_details["principal"],$loan_details["decimals"])}}</th>
                        <th>{{number_format($total_principal,$loan_details["decimals"])}}</th>
                        <th class="righthighlightcolheader">&nbsp;</th>
                        <th class="lefthighlightcolheader">{{number_format($total_interest,$loan_details["decimals"])}}</th>
                        <th>{{number_format( $loan_details["total_fees"],$loan_details["decimals"])}}</th>
                        <th>{{number_format($total_due,$loan_details["decimals"])}}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
@endsection
@section('scripts')

@endsection
