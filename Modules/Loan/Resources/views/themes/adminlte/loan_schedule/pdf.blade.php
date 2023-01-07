<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ trans_choice('loan::general.repayment',1) }}  {{ trans_choice('loan::general.schedule',1) }}</title>
    <style>
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
            display: table;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .pull-right {
            float: right !important;
        }

        span {
            font-weight: bold;
        }

        .col {
            margin-bottom: -99999px;
            padding-bottom: 99999px;
        }

        .col-wrap {
            overflow: hidden;
        }

        table.pretty {
            width: 100%;
            border-collapse: collapse;
        }

        table.pretty th, table.pretty td {
            border: 1px solid gainsboro;
            padding: 0.2em;
        }

        table.pretty caption {
            font-style: italic;
            font-weight: bold;
            margin-left: inherit;
            margin-right: inherit;
        }

        table.pretty thead tr th {
            border-bottom: 2px solid;
            font-weight: bold;
            text-align: center;
        }

        table.pretty thead tr th.empty {
            border: 0 none;
        }

        table.pretty tfoot tr th {
            border-bottom: 2px solid;
            border-top: 2px solid;
            font-weight: bold;
            text-align: center;
        }

        table.pretty tbody tr th {
            text-align: center;
        }

        table.pretty tbody tr td {
            border-top: 1px solid;
            text-align: center;
        }

        table.pretty tbody tr.odd td {
            background: none repeat scroll 0 0 #EBF4FB;
        }

        table.pretty tbody tr.even td {
            background: none repeat scroll 0 0 #BCEEEE;
        }

        table.pretty thead tr th.highlightcol {
            border-color: #2E6E9E #2E6E9E gainsboro;
            border-style: solid;
            border-width: 2px 2px 1px;
        }

        table.pretty tfoot tr th.highlightcol {
            border-left: 2px solid #2E6E9E;
            border-right: 2px solid #2E6E9E;
        }

        table.pretty thead tr th.lefthighlightcol, table.pretty tbody tr td.lefthighlightcol, table.pretty tfoot tr th.lefthighlightcol {
            border-left: 2px solid #2E6E9E;
        }

        table.pretty thead tr th.righthighlightcol, table.pretty tbody tr td.righthighlightcol, table.pretty tfoot tr th.righthighlightcol {
            border-right: 2px solid #2E6E9E;
        }

        table.pretty thead tr th.lefthighlightcolheader, table.pretty tbody tr td.lefthighlightcolheader, table.pretty tfoot tr th.lefthighlightcolheader {
            border-left: 2px solid #2E6E9E;
        }

        table.pretty thead tr th.righthighlightcolheader, table.pretty tbody tr td.righthighlightcolheader, table.pretty tfoot tr th.righthighlightcolheader {
            border-right: 2px solid #2E6E9E;
        }

        .strikethrough {
            text-decoration: line-through;
            color: red;
        }

        .month, .year {
            margin: 2px;
        }

        caption, th {
            text-align: left;
        }


        .font-11 {
            font-size: 11px;
        }

        .style-0 {
            empty-cells: show;
            table-layout: fixed;
            width: 1315pt
        }

        .style-1 {
            color: white;
            padding-left: 10pt;
            font-size: 14pt;
            font-family: "Arial";
            font-weight: bold;
            font-style: normal;
            text-decoration: none;
            text-align: left;
            word-spacing: 0pt;
            letter-spacing: 0pt;
            background-color: #339933
        }


        .opacity-75 {
            opacity: 0.75;
            filter: alpha(opacity=75);
        }

        .opacity-50 {
            opacity: 0.5;
            filter: alpha(opacity=50);
        }

        .opacity-25 {
            opacity: 0.25;
            filter: alpha(opacity=25);
        }

        .opacity-0 {
            opacity: 0;
            filter: alpha(opacity=0);
        }

        .no-edge-top {
            top: 0;
        }

        .no-edge-bottom {
            bottom: 0;
        }

        .no-edge-left {
            left: 0;
        }

        .no-edge-right {
            right: 0;
        }
    </style>
</head>
<body>
<div>
    @if(!empty($company_logo))
        <img src="{{asset('storage/uploads/'.$company_logo)}}" alt="logo" class="text-center"/>
    @endif
    <h3 class="text-center">{{$company_name}}</h3>
    <h4 class="text-center">{{ trans_choice('loan::general.repayment',1) }}  {{ trans_choice('loan::general.schedule',2) }}</h4>
</div>
<div style="clear: both">
    <div style="float: left; width: 50%">
        <table class="table">
            <tbody>
            <tr>
                <td><span>{{ trans_choice('client::general.client',1) }}</span></td>
                <td>
                    @if(!empty($loan->client))
                        {{$loan->client->first_name}} {{$loan->client->last_name}}
                    @endif
                </td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.loan',1) }} #</span></td>
                <td>{{$loan->id}}</td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.disbursed',1) }}</span></td>
                <td>{{$loan->disbursed_on_date}}</td>
            </tr>
            <tr>
                <td>
                    <span>{{ trans_choice('loan::general.maturity',1) }} {{ trans_choice('core::general.date',1) }}</span>
                </td>
                <td>{{$loan->expected_maturity_date}}</td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.repayment',1) }}</span></td>
                <td>
                    {{trans_choice('loan::general.every',1)}} {{$loan->repayment_frequency}}
                    @if($loan->repayment_frequency_type=='days')
                        {{trans_choice('loan::general.day',2)}}
                    @endif
                    @if($loan->repayment_frequency_type=='weeks')
                        {{trans_choice('loan::general.week',2)}}
                    @endif
                    @if($loan->repayment_frequency_type=='months')
                        {{trans_choice('loan::general.month',2)}}
                    @endif
                    @if($loan->repayment_frequency_type=='years')
                        {{trans_choice('loan::general.year',2)}}
                    @endif
                </td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.principal',1) }}</span></td>
                <td>{{number_format($loan->principal,$loan->decimals)}}</td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.interest',1) }} %</span></td>
                <td>
                    {{round($loan->interest_rate,2)}}
                    {{trans_choice('loan::general.per',1)}}
                    @if($loan->interest_rate_type=='month')
                        {{trans_choice('loan::general.month',1)}}
                    @endif
                    @if($loan->interest_rate_type=='year')
                        {{trans_choice('loan::general.year',1)}}
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="float: left; width: 50%;display: none">
        <table class="table">
            <tbody>
            <tr>
                <td><span>{{ trans_choice('loan::general.interest',1) }} </span></td>
                <td id="interest"></td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.fee',2) }}</span></td>
                <td id="fees"></td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.penalty',2) }}</span></td>
                <td id="penalties"></td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.due',1) }}</span></td>
                <td id="due"></td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.paid',2) }}</span></td>
                <td id="paid"></td>
            </tr>
            <tr>
                <td><span>{{ trans_choice('loan::general.balance',2) }}</span></td>
                <td id="balance"></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
    </div>
</div>
<div style="clear: both;margin-top: 20px">
    <table class="pretty displayschedule" id="repaymentschedule" style="margin-top: 20px;">
        <colgroup span="3"></colgroup>
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
            <th class="empty" scope="colgroup" colspan="5">&nbsp;</th>
            <th class="highlightcol" scope="colgroup"
                colspan="3">{{trans_choice('loan::general.loan_amount_and_balance',1)}}</th>
            <th class="highlightcol" scope="colgroup"
                colspan="3">{{trans_choice('loan::general.total_cost_of_loan',1)}}</th>
            <th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
        </tr>
        <tr>
            <th scope="col">#</th>
            <th scope="col">{{trans_choice('core::general.date',1)}}</th>
            <th scope="col"># {{trans_choice('loan::general.day',2)}}</th>
            <th scope="col">{{trans_choice('loan::general.paid',1)}} {{trans_choice('core::general.by',1)}}</th>
            <th scope="col"></th>
            <th class="lefthighlightcolheader"
                scope="col">{{trans_choice('loan::general.disbursement',1)}}</th>
            <th scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.due',1)}}</th>
            <th class="righthighlightcolheader"
                scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.balance',1)}}</th>

            <th class="lefthighlightcolheader"
                scope="col">{{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.due',1)}}</th>
            <th scope="col">{{trans_choice('loan::general.fee',2)}}</th>
            <th class="righthighlightcolheader"
                scope="col">{{trans_choice('loan::general.penalty',2)}}

            </th>
            <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.due',1)}}</th>
            <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.paid',1)}}</th>
            <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.outstanding',1)}}</th>
        </tr>
        </thead>
        <tbody>

        <tr>
            <td scope="row"></td>
            <td>{{$loan->disbursed_on_date}}</td>
            <td></td>
            <td><span style="color: #eb2442;"></span></td>
            <td>&nbsp;</td>
            <td class="lefthighlightcolheader">{{number_format($loan->principal,$loan->decimals)}}</td>
            <td></td>
            <td class="righthighlightcolheader">{{number_format($loan->principal,$loan->decimals)}}</td>
            <td class="lefthighlightcolheader"></td>
            <td>{{number_format($loan->disbursement_charges,$loan->decimals)}}</td>
            <td class="righthighlightcolheader"></td>
            <td>{{number_format($loan->disbursement_charges,$loan->decimals)}}</td>
            <td>{{number_format($loan->disbursement_charges,$loan->decimals)}}</td>
            <td></td>
        </tr>
        <?php
        $count = 1;
        $total_days = 0;
        $total_principal = 0;
        $total_interest = 0;
        $total_fees = 0 + $loan->disbursement_charges;
        $total_penalties = 0;
        $total_due = 0;
        $total_paid = 0 + $loan->disbursement_charges;
        $total_outstanding = 0;
        $balance = $loan->principal
        ?>
        @foreach($loan->repayment_schedules as $key)
            <?php
            $days = \Carbon\Carbon::parse($key->due_date)->diffInDays(\Illuminate\Support\Carbon::parse($key->from_date));
            $total_days = $total_days + $days;
            $balance = $balance - $key->principal;
            $principal = $key->principal - $key->principal_waived_derived - $key->principal_written_off_derived;
            $interest = $key->interest - $key->interest_waived_derived - $key->interest_written_off_derived;
            $fees = $key->fees - $key->fees_waived_derived - $key->fees_written_off_derived;
            $penalties = $key->penalties - $key->penalties_waived_derived - $key->penalties_written_off_derived;
            $due = $principal + $interest + $fees + $penalties;
            $paid = $key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived;
            $outstanding = $due - $paid;
            $total_principal = $total_principal + $principal;
            $total_interest = $total_interest + $interest;
            $total_fees = $total_fees + $fees;
            $total_penalties = $total_penalties + $penalties;
            $total_due = $total_due + $due;
            $total_paid = $total_paid + $paid;
            $total_outstanding = $total_outstanding + $outstanding;

            ?>
            <tr>
                <td scope="row">{{$count}}</td>
                <td>{{$key->due_date}}</td>
                <td>{{$days}}</td>
                <td>
                    @if($outstanding<=0)
                        <span style="@if(\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))color: #eb2442; @endif">{{$key->paid_by_date}}</span>
                    @elseif($outstanding>0 && \Illuminate\Support\Carbon::now()->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                        <span style="color: #eb2442;">{{trans_choice('loan::general.overdue',1)}}</span>
                    @endif
                </td>
                <td>
                    @if($outstanding<=0)
                        @if(\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                            <i class="fa fa-question-circle"></i>
                        @else
                            <i class="fa fa-check-circle"></i>
                        @endif
                    @endif
                </td>
                <td class="lefthighlightcolheader"></td>
                <td>{{number_format($principal,$loan->decimals)}}</td>
                <td class="righthighlightcolheader">{{number_format($balance,$loan->decimals)}}</td>
                <td class="lefthighlightcolheader">
                    {{number_format($interest,$loan->decimals)}}
                </td>
                <td>{{number_format($fees,$loan->decimals)}}</td>
                <td class="righthighlightcolheader">{{number_format($penalties,$loan->decimals)}}</td>
                <td>{{number_format($due,$loan->decimals)}}</td>
                <td>{{number_format($paid,$loan->decimals)}}</td>
                <td>{{number_format($outstanding,$loan->decimals)}}</td>
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
            <th></th>
            <th></th>
            <th class="lefthighlightcolheader">{{number_format($loan->principal,$loan->decimals)}}</th>
            <th>{{number_format($total_principal,$loan->decimals)}}</th>
            <th class="righthighlightcolheader">&nbsp;</th>
            <th class="lefthighlightcolheader">{{number_format($total_interest,$loan->decimals)}}</th>
            <th>{{number_format($total_fees,$loan->decimals)}}</th>
            <th class="righthighlightcolheader">{{number_format($total_penalties,$loan->decimals)}}</th>
            <th>{{number_format($total_due,$loan->decimals)}}</th>
            <th>{{number_format($total_paid,$loan->decimals)}}</th>
            <th>{{number_format($total_outstanding,$loan->decimals)}}</th>
        </tr>
        </tfoot>
    </table>
</div>
</body>
<script>
    (function () {
        document.getElementById("interest").innerHTML = "{{number_format($total_interest,$loan->decimals)}}";
        document.getElementById("fees").innerHTML = "{{number_format($total_fees,$loan->decimals)}}";
        document.getElementById("penalties").innerHTML = "{{number_format($total_penalties,$loan->decimals)}}";
        document.getElementById("due").innerHTML = "{{number_format($total_due,$loan->decimals)}}";
        document.getElementById("paid").innerHTML = "{{number_format($total_paid,$loan->decimals)}}";
        document.getElementById("balance").innerHTML = "{{number_format($total_outstanding,$loan->decimals)}}";
    })();
</script>
</html>
