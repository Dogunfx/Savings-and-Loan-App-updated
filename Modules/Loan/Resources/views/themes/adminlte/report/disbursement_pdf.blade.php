<style>
    body {
        font-size: 9px;
    }

    .table {
        width: 100%;
        border: 1px solid #ccc;
        border-collapse: collapse;
    }

    .table th, td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ccc;
    }

    .light-heading th {
        background-color: #eeeeee
    }

    .green-heading th {
        background-color: #4CAF50;
        color: white;
    }

    .text-center {
        text-align: center;
    }

    .table-striped tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .text-danger {
        color: #a94442;
    }

    .text-success {
        color: #3c763d;
    }

</style>
<h3 class="text-center">{{\Modules\Setting\Entities\Setting::where('setting_key','core.company_name')->first()->setting_value}}</h3>
<h3 class="text-center"> {{trans_choice('loan::general.disbursement',2)}}</h3>
<table class="table table-bordered table-striped table-condensed table-hover">
    <thead>
    <tr>
        <th colspan="6">
            @if(!empty($data->first()) && !empty($branch_id))
                {{trans_choice('core::general.branch',1)}}:

                {{$data->first()->branch}}
            @endif
        </th>
        <th colspan="9">

        </th>
        <th colspan="4">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
        <th colspan="4">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr>
        <th colspan="8"></th>
        <th colspan="5"></th>
        <th colspan="5">{{ trans_choice('loan::general.outstanding',1) }}</th>
        <th colspan="5"></th>
    </tr>
    <tr class="green-heading">
        <th>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</th>
        <th>{{trans_choice('core::general.branch',1)}}</th>
        <th>{{trans_choice('client::general.client',1)}}</th>
        <th>{{trans_choice('core::general.mobile',1)}}</th>
        <th>{{trans_choice('loan::general.loan',1)}}#</th>
        <th>{{trans_choice('loan::general.product',1)}}</th>
        <th>{{trans_choice('loan::general.disbursed',1)}} {{trans_choice('core::general.date',1)}}</th>
        <th>{{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.maturity',1)}} {{trans_choice('core::general.date',1)}}</th>
        <th>{{trans_choice('loan::general.principal',1)}}</th>
        <th>{{ trans_choice('loan::general.interest',1) }}</th>
        <th>{{trans_choice('loan::general.fee',2)}}</th>
        <th>{{ trans_choice('loan::general.penalty',2) }}</th>
        <th>{{ trans_choice('loan::general.total',1) }}</th>
        <th>{{trans_choice('loan::general.principal',1)}}</th>
        <th>{{ trans_choice('loan::general.interest',1) }}</th>
        <th>{{trans_choice('loan::general.fee',2)}}</th>
        <th>{{ trans_choice('loan::general.penalty',2) }}</th>
        <th>{{ trans_choice('loan::general.total',1) }}</th>
        <th>{{ trans_choice('loan::general.fund',1) }}</th>
        <th>{{ trans_choice('loan::general.purpose',1) }}</th>
        <th>{{ trans_choice('loan::general.status',1) }}</th>
        <th>{{ trans_choice('loan::general.arrears',1) }} {{ trans_choice('loan::general.amount',1) }}</th>
        <th>{{ trans_choice('loan::general.day',2) }} {{ trans_choice('core::general.in',1) }} {{ trans_choice('loan::general.arrears',1) }}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_principal = 0;
    $total_principal_outstanding = 0;
    $total_principal_overdue = 0;
    $total_interest = 0;
    $total_interest_outstanding = 0;
    $total_interest_overdue = 0;
    $total_fees = 0;
    $total_fees_outstanding = 0;
    $total_fees_overdue = 0;
    $total_penalties = 0;
    $total_penalties_outstanding = 0;
    $total_penalties_overdue = 0;
    $total_arrears_amount = 0;
    ?>
    @foreach($data as $key)
        <?php
        $total_principal = $total_principal + $key->repayment_schedules->sum('principal');
        $total_interest = $total_interest + $key->repayment_schedules->sum('interest');
        $total_fees = $total_fees + $key->repayment_schedules->sum('fees');
        $total_penalties = $total_penalties + $key->repayment_schedules->sum('penalties');
        $principal_outstanding = $key->repayment_schedules->sum('principal') - $key->repayment_schedules->sum('principal_written_off_derived') - $key->repayment_schedules->sum('principal_repaid_derived');
        $interest_outstanding = $key->repayment_schedules->sum('interest') - $key->repayment_schedules->sum('interest_waived_derived') - $key->repayment_schedules->sum('interest_repaid_derived') - $key->repayment_schedules->sum('interest_written_off_derived');
        $fees_outstanding = $key->repayment_schedules->sum('fees') + $key->disbursement_charges - $key->repayment_schedules->sum('fees_waived_derived') - $key->repayment_schedules->sum('fees_repaid_derived') + $key->disbursement_charges - $key->repayment_schedules->sum('fees_written_off_derived');
        $penalties_outstanding = $key->repayment_schedules->sum('penalties') - $key->repayment_schedules->sum('penalties_waived_derived') - $key->repayment_schedules->sum('penalties_repaid_derived') - $key->repayment_schedules->sum('penalties_written_off_derived');
        $total_principal_outstanding = $total_principal_outstanding + $principal_outstanding;
        $total_interest_outstanding = $total_interest_outstanding + $interest_outstanding;
        $total_fees_outstanding = $total_fees_outstanding + $fees_outstanding;
        $total_penalties_outstanding = $total_penalties_outstanding + $penalties_outstanding;
        //arrears
        $principal_overdue = 0;
        $interest_overdue = 0;
        $fees_overdue = 0;
        $penalties_overdue = 0;
        $arrears_days = 0;
        $past_schedules = $key->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->count();
        $trp = 0;

        $arrears_last_schedule = $key->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
        if (!empty($arrears_last_schedule)) {
            $overdue_schedules = $key->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);

            $principal_overdue = $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived');
            $interest_overdue = $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived');
            $fees_overdue = $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived');
            $penalties_overdue = $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');

            $total_principal_overdue = $total_principal_overdue + $principal_overdue;
            $total_interest_overdue = $total_interest_overdue + $interest_overdue;
            $total_fees_overdue = $total_fees_overdue + $fees_overdue;
            $total_penalties_overdue = $total_penalties_overdue + $penalties_overdue;
            $arrears_days = $arrears_days + \Illuminate\Support\Carbon::today()->diffInDays(\Illuminate\Support\Carbon::parse($overdue_schedules->sortBy('due_date')->first()->due_date));

        }
        $total_overdue = $principal_overdue + $interest_overdue + $fees_overdue + $penalties_overdue;
        $balance = $principal_outstanding + $interest_outstanding + $penalties_outstanding + $fees_outstanding;
        ?>
        <tr>
            <td>{{ $key->loan_officer }}</td>
            <td>{{ $key->branch }}</td>
            <td>
                {{$key->client}}
            </td>
            <td>{{ $key->mobile }}</td>
            <td>{{ $key->id }}</td>
            <td>{{ $key->loan_product }}</td>
            <td>{{ $key->disbursed_on_date }}</td>
            <td>{{ $key->expected_maturity_date }}</td>
            <td>{{ number_format($key->repayment_schedules->sum('principal'),2) }}</td>
            <td>{{ number_format($key->repayment_schedules->sum('interest'),2) }}</td>
            <td>{{ number_format($key->repayment_schedules->sum('fees'),2) }}</td>
            <td>{{ number_format($key->repayment_schedules->sum('penalties'),2) }}</td>
            <td>{{ number_format($key->repayment_schedules->sum('principal')+$key->repayment_schedules->sum('interest')+$key->repayment_schedules->sum('fees')+$key->repayment_schedules->sum('penalties'),2) }}</td>
            <td>{{ number_format( $principal_outstanding,2) }}</td>
            <td>{{ number_format( $interest_outstanding,2) }}</td>
            <td>{{ number_format( $fees_outstanding,2) }}</td>
            <td>{{ number_format( $penalties_outstanding,2) }}</td>
            <td>{{ number_format($principal_outstanding+$interest_outstanding+$fees_outstanding+ $penalties_outstanding,2) }}</td>
            <td>{{ $key->fund }}</td>
            <td>{{ $key->loan_purpose }}</td>
            <td>
                @if($key->status=='active')
                    {{trans_choice('loan::general.active',1)}}
                @endif
                @if($key->status=='closed')
                    {{trans_choice('loan::general.closed',1)}}
                @endif
                @if($key->status=='rescheduled')
                    {{trans_choice('loan::general.rescheduled',1)}}
                @endif
                @if($key->status=='written_off')
                    {{trans_choice('loan::general.written_off',1)}}
                @endif
            </td>
            <td>{{ number_format( $total_overdue,2) }}</td>
            <td>{{$arrears_days}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8"><b>{{trans_choice('core::general.total',1)}}</b></td>
        <td>{{number_format($total_principal,2)}}</td>
        <td>{{number_format($total_interest,2)}}</td>
        <td>{{number_format($total_fees,2)}}</td>
        <td>{{number_format($total_penalties,2)}}</td>
        <td>{{number_format($total_principal+$total_interest+$total_fees+$total_penalties,2)}}</td>
        <td>{{number_format($total_principal_outstanding,2)}}</td>
        <td>{{number_format($total_interest_outstanding,2)}}</td>
        <td>{{number_format($total_fees_outstanding,2)}}</td>
        <td>{{number_format($total_penalties_outstanding,2)}}</td>
        <td>{{number_format($total_principal_outstanding+$total_interest_outstanding+$total_fees_outstanding+$total_penalties_outstanding,2)}}</td>
        <td colspan="3"></td>
        <td>{{number_format($total_principal_overdue+$total_interest_overdue+$total_fees_overdue+$total_penalties_overdue,2)}}</td>
        <td></td>
    </tr>
    </tfoot>
</table>