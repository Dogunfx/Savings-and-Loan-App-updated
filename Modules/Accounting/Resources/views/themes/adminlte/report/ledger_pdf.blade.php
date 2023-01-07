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
<h3 class="text-center">{{trans_choice('accounting::general.ledger',1)}}</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th colspan="2">
            {{trans_choice('core::general.branch',1)}}:
            @if(!empty($data->first()))
                {{$data->first()->branch}}
            @endif
        </th>

        <th colspan="2">{{trans_choice('accounting::general.as_on',1)}}: {{$start_date}} to {{$end_date}}</th>
    </tr>
    <tr class="bg-success">
        <th>{{trans_choice('accounting::general.gl_code',1)}}</th>
        <th>{{trans_choice('core::general.account',1)}}</th>
        <th>Dr {{trans_choice('accounting::general.balance',1)}}</th>
        <th>Cr {{trans_choice('accounting::general.balance',1)}}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    //group the results

    $total_debit = 0;
    $total_credit = 0;
    ?>

    @foreach($data as $key)
        <?php
        //group the results

        $total_debit = $total_debit+$key->debit;
        $total_credit = $total_credit+$key->credit;
        ?>
        <tr>
            <td>{{ $key->gl_code }}</td>
            <td>
                {{$key->name}}
            </td>
            <td>
                {{ number_format(($key->debit ),2) }}
            </td>
            <td>
                {{ number_format(($key->credit ),2) }}
            </td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2"><h4>{{trans_choice('core::general.total',1)}} </h4></td>
        <td> <h4>{{ number_format($total_debit,2) }}</h4></td>
        <td> <h4>{{ number_format($total_credit,2) }}</h4></td>
    </tr>

    </tbody>

</table>