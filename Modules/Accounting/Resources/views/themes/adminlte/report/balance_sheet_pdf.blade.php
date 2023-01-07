<style>
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
<h3 class="text-center">{{trans_choice('accounting::general.balance_sheet',1)}}</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th colspan="2">
            {{trans_choice('core::general.branch',1)}}:
            @if(!empty($data->first()))
                {{$data->first()->branch}}
            @endif
        </th>

        <th>{{trans_choice('accounting::general.as_on',1)}}: {{$end_date}}</th>
    </tr>
    <tr class="bg-success">
        <th>{{trans_choice('accounting::general.gl_code',1)}}</th>
        <th>{{trans_choice('core::general.account',1)}}</th>
        <th>{{trans_choice('accounting::general.balance',1)}}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    //group the results
    $assets = [];
    $liabilities = [];
    $equities = [];
    $total_assets = 0;
    $total_liabilities = 0;
    $total_equities = 0;
    foreach ($data as $key) {
        if ($key->account_type == 'asset') {
            array_push($assets, $key);
            $total_assets = $total_assets + ($key->debit - $key->credit);
        }
        if ($key->account_type == 'equity') {
            array_push($equities, $key);
            $total_equities = $total_equities + ($key->credit - $key->debit);
        }
        if ($key->account_type == 'liability') {
            array_push($liabilities, $key);
            $total_liabilities = $total_liabilities + ($key->credit - $key->debit);
        }
    }

    ?>
    <tr>
        <td colspan="3" class="text-center"><h4>{{trans_choice('accounting::general.asset',2)}}</h4>
        </td>
    </tr>
    @foreach($assets as $key)
        <tr>
            <td>{{ $key->gl_code }}</td>
            <td>
                {{$key->name}}
            </td>
            <td>
                {{ number_format(($key->debit - $key->credit),2) }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2">
            <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.asset',2)}}</h4></td>
        <td><h4>{{ number_format($total_assets,2) }}</h4></td>
    </tr>
    <tr>
        <td colspan="3" class="text-center"><h4>{{trans_choice('accounting::general.liability',2)}}</h4>
        </td>
    </tr>

    @foreach($liabilities as $key)
        <tr>
            <td>{{ $key->gl_code }}</td>
            <td>
                {{$key->name}}
            </td>
            <td>
                {{ number_format(($key->credit - $key->debit),2) }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2">
            <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.liability',2)}}</h4></td>
        <td><h4>{{ number_format($total_liabilities,2) }}</h4></td>
    </tr>
    <tr>
        <td colspan="3" class="text-center"><h4>{{trans_choice('accounting::general.equity',2)}}</h4>
        </td>
    </tr>

    @foreach($equities as $key)

        <tr>
            <td>{{ $key->gl_code }}</td>
            <td>
                {{$key->name}}
            </td>
            <td>
                {{ number_format(($key->credit - $key->debit),2) }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2">
            <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.equity',2)}}</h4></td>
        <td><h4>{{ number_format($total_equities,2) }}</h4></td>
    </tr>

    <tr>
        <td colspan="2">
            <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.liability',2)}} {{trans_choice('core::general.and',1)}} {{trans_choice('accounting::general.equity',2)}}</h4>
        </td>
        <td><h4>{{ number_format($total_equities+$total_liabilities,2) }}</h4></td>
    </tr>
    </tbody>

</table>