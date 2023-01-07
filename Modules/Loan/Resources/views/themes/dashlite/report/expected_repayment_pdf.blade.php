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
<h3 class="text-center"> {{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.repayment',2)}}</h3>
<table class="table table-bordered table-condensed table-striped table-hover">
    <thead>
    <tr>
        <th colspan="2">
            @if(!empty($data->first()) && !empty($branch_id))
                {{trans_choice('core::general.branch',1)}}:

                {{$data->first()->branch}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($data->first()) && !empty($loan_product_id))
                {{trans_choice('loan::general.product',1)}}:

                {{$data->first()->loan_product}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($data->first()) && !empty($loan_officer_id))
                {{trans_choice('loan::general.officer',1)}}:

                {{$data->first()->loan_officer}}
            @endif
        </th>
        <th colspan="3">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
        <th colspan="3">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr>
        <th></th>
        <th colspan="5">{{trans_choice('loan::general.expected',1)}}</th>
        <th colspan="5">{{trans_choice('loan::general.actual',1)}}</th>
        <th></th>
    </tr>
    <tr class="green-heading">
        <th>{{trans_choice('core::general.branch',1)}}</th>
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
        <th>{{ trans_choice('loan::general.balance',1) }}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_actual_principal = 0;
    $total_actual_interest = 0;
    $total_actual_fees = 0;
    $total_actual_penalties = 0;
    $total_actual_amount = 0;
    $total_expected_principal = 0;
    $total_expected_interest = 0;
    $total_expected_fees = 0;
    $total_expected_penalties = 0;
    $total_expected_amount = 0;
    ?>
    @foreach($data as $key)
        <?php
        $total_actual_principal = $total_actual_principal + $key->principal_repaid_derived;
        $total_actual_interest = $total_actual_interest + $key->interest_repaid_derived;
        $total_actual_fees = $total_actual_fees + $key->fees_repaid_derived;
        $total_actual_penalties = $total_actual_penalties + $key->penalties_repaid_derived;

        $total_expected_principal = $total_expected_principal + $key->principal;
        $total_expected_interest = $total_expected_interest + $key->interest;
        $total_expected_fees = $total_expected_fees + $key->fees;
        $total_expected_penalties = $total_expected_penalties + $key->penalties;
        ?>
        <tr>
            <td>{{ $key->branch }}</td>
            <td>{{ number_format( $key->principal,2) }}</td>
            <td>{{ number_format( $key->interest,2) }}</td>
            <td>{{ number_format( $key->fees,2) }}</td>
            <td>{{ number_format( $key->penalties,2) }}</td>
            <td>{{ number_format( $key->principal+$key->interest+$key->fees+$key->penalties,2) }}</td>
            <td>{{ number_format( $key->principal_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->interest_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->fees_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->penalties_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->principal_repaid_derived+$key->interest_repaid_derived+$key->fees_repaid_derived+$key->penalties_repaid_derived,2) }}</td>
            <td>{{ number_format( ($key->principal+$key->interest+$key->fees+$key->penalties)-($key->principal_repaid_derived+$key->interest_repaid_derived+$key->fees_repaid_derived+$key->penalties_repaid_derived),2) }}</td>

        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th><b>{{trans_choice('core::general.total',1)}}</b></th>
        <th>{{number_format($total_expected_principal,2)}}</th>
        <th>{{number_format($total_expected_interest,2)}}</th>
        <th>{{number_format($total_expected_fees,2)}}</th>
        <th>{{number_format($total_expected_penalties,2)}}</th>
        <th>{{number_format($total_expected_principal+$total_expected_interest+$total_expected_fees+$total_expected_penalties,2)}}</th>
        <th>{{number_format($total_actual_principal,2)}}</th>
        <th>{{number_format($total_actual_interest,2)}}</th>
        <th>{{number_format($total_actual_fees,2)}}</th>
        <th>{{number_format($total_actual_penalties,2)}}</th>
        <th>{{number_format($total_actual_principal+$total_actual_interest+$total_actual_fees+$total_actual_penalties,2)}}</th>
        <th>{{number_format(($total_expected_principal+$total_expected_interest+$total_expected_fees+$total_expected_penalties)-($total_actual_principal+$total_actual_interest+$total_actual_fees+$total_actual_penalties),2)}}</th>

    </tr>
    </tfoot>
</table>