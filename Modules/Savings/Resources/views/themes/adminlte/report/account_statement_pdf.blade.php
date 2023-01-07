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
<h3 class="text-center">{{trans_choice('savings::general.account_statement',2)}}</h3>
<table class="table table-bordered table-condensed table-hover">
    <thead>
    <tr>
        <th colspan="2">
            {{trans_choice('core::general.branch',1)}}:
            @if(!empty($data->first()))
                {{$data->first()->branch}}
            @endif
        </th>
        <th colspan="2">
            {{trans_choice('client::general.client',1)}}:
            @if(!empty($data->first()))
                {{$data->first()->client}}
            @endif
        </th>
        <th colspan="2">
            {{trans_choice('savings::general.savings',1)}}#:
            @if(!empty($data->first()))
                {{$data->first()->savings_account_number}}
            @endif
        </th>
        <th colspan="1">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
        <th colspan="2">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr style="background-color: #D1F9FF">
        <th>{{trans_choice('core::general.id',1)}}</th>
        <th>{{trans_choice('core::general.branch',1)}}</th>
        <th>{{trans_choice('core::general.type',1)}}</th>
        <th>{{trans_choice('accounting::general.debit',1)}}</th>
        <th>{{trans_choice('accounting::general.credit',1)}}</th>
        <th>{{trans_choice('savings::general.balance',1)}}</th>
        <th>{{trans_choice('core::general.date',1)}}</th>
        <th>{{trans_choice('core::general.receipt',1)}}</th>
        <th>{{trans_choice('core::general.payment',1)}} {{trans_choice('core::general.type',1)}}</th>
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
            <td>{{ $key->id }}</td>

            <td>{{ $key->branch }}</td>
            <td>{{ $key->transaction_type }}</td>
            <td>
                {{ number_format($key->debit,2) }}
            </td>
            <td>
                {{ number_format( $key->credit,2) }}
            </td>
            <td>{{ number_format( $key->balance,2) }}</td>
            <td>{{ $key->submitted_on }}</td>
            <td>{{ $key->receipt }}</td>
            <td>{{ $key->payment_type }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3"><b>{{trans_choice('core::general.total',1)}}</b></td>
        <td>{{number_format($debit_total,2)}}</td>
        <td>{{number_format($credit_total,2)}}</td>
        <td colspan="4"></td>
    </tr>
    </tfoot>
</table>