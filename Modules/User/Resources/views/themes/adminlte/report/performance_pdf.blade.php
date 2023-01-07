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
<h3 class="text-center">{{trans_choice('user::general.performance_report',2)}}</h3>
<table class="table table-bordered table-condensed table-hover">
    <thead>
    <tr>
        <th colspan="2">
            @if( !empty($branch_id))
                {{trans_choice('core::general.branch',1)}}:

                {{\Modules\Branch\Entities\Branch::find($branch_id)->name}}
            @endif
        </th>
    </tr>
    <tr>
        <th colspan="2">
            {{trans_choice('user::general.staff',1)}}:

            {{\Modules\User\Entities\User::find($loan_officer_id)->full_name}}
        </th>
    </tr>
    <tr>
        <th colspan="2">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
    </tr>
    <tr>
        <th colspan="2">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
    </tr>
    <tr style="background-color: #D1F9FF">
        <th colspan="">{{trans_choice('user::general.item',1)}}</th>
        <th colspan="">{{trans_choice('user::general.value',1)}}</th>

    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            {{__('user::general.Number of Clients')}}
        </td>
        <td>{{ number_format($data['number_of_clients']) }}</td>
    </tr>
    <tr>
        <td>
            {{__('user::general.Number of Loans')}}
        </td>
        <td>{{ number_format($data['number_of_loans']) }}</td>
    </tr>
    <tr>
        <td>
            {{__('user::general.Number of Savings')}}
        </td>
        <td>{{ number_format($data['number_of_savings']) }}</td>
    </tr>
    <tr>
        <td>
            {{__('user::general.Disbursed Loans Amount')}}
        </td>
        <td>{{ number_format($data['disbursed_loans_amount']) }}</td>
    </tr>
    <tr>
        <td>
            {{__('user::general.Total Payments Received')}}
        </td>
        <td>{{ number_format($data['total_payments_received']) }}</td>
    </tr>
    </tbody>
</table>
