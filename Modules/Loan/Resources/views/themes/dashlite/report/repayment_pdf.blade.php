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
<h3 class="text-center"> {{trans_choice('loan::general.repayment',2)}}</h3>
<table class="table table-bordered table-striped table-hover">
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
        <th colspan="2">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
        <th colspan="3">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr class="green-heading">
        <th>{{trans_choice('core::general.id',1)}}</th>
        <th>{{trans_choice('client::general.client',1)}}</th>
        <th>{{trans_choice('loan::general.loan',1)}}#</th>
        <th>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</th>
        <th>{{trans_choice('loan::general.principal',1)}}</th>
        <th>{{ trans_choice('loan::general.interest',1) }}</th>
        <th>{{trans_choice('loan::general.fee',2)}}</th>
        <th>{{ trans_choice('loan::general.penalty',2) }}</th>
        <th>{{ trans_choice('loan::general.total',1) }}</th>
        <th>{{ trans_choice('core::general.date',1) }}</th>
        <th>{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.type',1) }}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_principal = 0;
    $total_interest = 0;
    $total_fees = 0;
    $total_penalties = 0;
    $total_amount = 0;
    ?>
    @foreach($data as $key)
        <?php
        $total_principal = $total_principal + $key->principal_repaid_derived;
        $total_interest = $total_interest + $key->interest_repaid_derived;
        $total_fees = $total_fees + $key->fees_repaid_derived;
        $total_penalties = $total_penalties + $key->penalties_repaid_derived;
        ?>
        <tr>
            <td>{{ $key->id }}</td>
            <td>
                {{$key->client}}
            </td>
            <td>{{ $key->loan_id }}</td>
            <td>{{ $key->loan_officer }}</td>
            <td>{{ number_format( $key->principal_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->interest_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->fees_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->penalties_repaid_derived,2) }}</td>
            <td>{{ number_format( $key->principal_repaid_derived+$key->interest_repaid_derived+$key->fees_repaid_derived+$key->penalties_repaid_derived,2) }}</td>
            <td>{{ $key->submitted_on }}</td>
            <td>{{ $key->payment_type }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4"><b>{{trans_choice('core::general.total',1)}}</b></td>
        <td>{{number_format($total_principal,2)}}</td>
        <td>{{number_format($total_interest,2)}}</td>
        <td>{{number_format($total_fees,2)}}</td>
        <td>{{number_format($total_penalties,2)}}</td>
        <td>{{number_format($total_principal+$total_interest+$total_fees+$total_penalties,2)}}</td>
        <td colspan="2"></td>
    </tr>
    </tfoot>
</table>