<style>
    body{
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
<h3 class="text-center"> {{trans_choice('loan::general.collection_sheet',1)}}</h3>
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
        <th colspan="2">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr class="green-heading">
        <th>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</th>
        <th>{{trans_choice('core::general.branch',1)}}</th>
        <th>{{trans_choice('client::general.client',1)}}</th>
        <th>{{trans_choice('core::general.mobile',1)}}</th>
        <th>{{trans_choice('loan::general.loan',1)}}#</th>
        <th>{{trans_choice('loan::general.product',1)}}</th>
        <th>{{ trans_choice('loan::general.expected',1) }} {{ trans_choice('loan::general.maturity',1) }} {{ trans_choice('core::general.date',1) }}</th>
        <th>{{trans_choice('loan::general.repayment',1)}} {{ trans_choice('core::general.date',1) }}</th>
        <th>{{ trans_choice('loan::general.expected',1) }} {{trans_choice('loan::general.amount',1)}}</th>
        <th>{{ trans_choice('loan::general.total',1) }} {{trans_choice('loan::general.due',1)}}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_due = 0;
    $total_expected_amount = 0;
    ?>
    @foreach($data as $key)
        <?php
        $total_due = $total_due + $key->total_due;
        $total_expected_amount = $total_expected_amount + $key->expected_amount;
        ?>
        <tr>
            <td>{{ $key->loan_officer }}</td>
            <td>{{ $key->branch }}</td>
            <td>
                {{$key->client}}
            </td>
            <td>{{ $key->mobile }}</td>
            <td>{{ $key->loan_id }}</td>

            <td>{{ $key->loan_product }}</td>
            <td>{{ $key->expected_maturity_date }}</td>
            <td>{{ $key->due_date }}</td>
            <td>{{ number_format( $key->expected_amount,2) }}</td>
            <td>{{ number_format( $key->total_due,2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8"><b>{{trans_choice('core::general.total',1)}}</b></td>
        <td>{{number_format($total_expected_amount,2)}}</td>
        <td>{{number_format($total_due,2)}}</td>
    </tr>
    </tfoot>
</table>