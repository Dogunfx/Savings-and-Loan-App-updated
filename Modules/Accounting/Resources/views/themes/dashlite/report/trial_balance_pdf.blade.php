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
<h3 class="text-center">{{trans_choice('accounting::general.trial_balance',1)}}</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th colspan="2">
            {{trans_choice('core::general.branch',1)}}:
            @if(!empty($data->first()))
                {{$data->first()->branch}}
            @endif
        </th>
        <th>{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
        <th>{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr class="green-heading">
        <th>{{trans_choice('accounting::general.gl_code',1)}}</th>
        <th>{{trans_choice('core::general.account',1)}}</th>
        <th>{{trans_choice('accounting::general.debit',1)}}</th>
        <th>{{trans_choice('accounting::general.credit',1)}}</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $credit_total = 0;
    $debit_total = 0;
    ?>
    @foreach($data as $key)
        <?php
        $dr = 0;
        $cr = 0;
        if ($key->account_type == 'asset' || $key->account_type == 'expense') {
            $dr = $key->debit - $key->credit;
        } else {
            $cr = $key->credit - $key->debit;
        }
        $credit_total = $credit_total + $cr;
        $debit_total = $debit_total + $dr;
        ?>
        <tr>
            <td>{{ $key->gl_code }}</td>
            <td>
                {{$key->name}}
            </td>
            <td>
                @if(!empty($dr))
                    {{ number_format($dr,2) }}
                @endif
            </td>
            <td>
                @if(!empty($cr))
                    {{ number_format($cr,2) }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2"><b>{{trans_choice('core::general.total',1)}}</b></td>
        <td>{{number_format($debit_total,2)}}</td>
        <td>{{number_format($credit_total,2)}}</td>
    </tr>
    </tfoot>
</table>
