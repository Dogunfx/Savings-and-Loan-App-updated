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
<h3 class="text-center">{{trans_choice('savings::general.account',2)}}</h3>
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
            @if(!empty($data->first()) && !empty($savings_product_id))
                {{trans_choice('savings::general.product',1)}}:

                {{$data->first()->savings_product}}
            @endif
        </th>
        <th colspan="2">
            @if(!empty($data->first()) && !empty($savings_officer_id))
                {{trans_choice('savings::general.officer',1)}}:

                {{$data->first()->savings_officer}}
            @endif
        </th>
        <th colspan="2">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
        <th colspan="1">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr class="green-heading">
        <th>{{trans_choice('client::general.client',1)}}</th>
        <th>{{trans_choice('savings::general.savings',1)}}#</th>
        <th>{{trans_choice('core::general.branch',1)}}</th>
        <th>{{trans_choice('savings::general.savings',1)}} {{trans_choice('savings::general.officer',1)}}</th>
        <th>{{trans_choice('savings::general.product',1)}}</th>
        <th>{{trans_choice('savings::general.deposit',2)}}</th>
        <th>{{trans_choice('savings::general.withdrawal',2)}}</th>
        <th>{{trans_choice('savings::general.balance',1)}}</th>
        <th>{{trans_choice('core::general.date',1)}}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $credit_total = 0;
    $debit_total = 0;
    ?>
    @foreach($data as $key)
        <?php
        $credit_total = $credit_total + $key->credit;
        $debit_total = $debit_total + $key->debit;
        ?>
        <tr>
            <td>
                {{$key->client}}
            </td>
            <td>{{ $key->id }}</td>
            <td>{{ $key->branch }}</td>
            <td>{{ $key->savings_officer }}</td>
            <td>{{ $key->savings_product }}</td>
            <td>
                {{ number_format( $key->credit,2) }}
            </td>
            <td>
                {{ number_format($key->debit,2) }}
            </td>

            <td>{{ number_format( $key->balance,2) }}</td>
            <td>{{ $key->submitted_on_date }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5"><b>{{trans_choice('core::general.total',1)}}</b></td>

        <td>{{number_format($credit_total,2)}}</td>
        <td>{{number_format($debit_total,2)}}</td>
        <td colspan="2"></td>
    </tr>
    </tfoot>
</table>