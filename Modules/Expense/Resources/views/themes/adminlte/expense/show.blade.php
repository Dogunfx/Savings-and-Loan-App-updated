@extends('core::layouts.master')
@section('title')
    {{ trans_choice('expense::general.expense',1) }} #{{ $expense->id }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('expense::general.expense',1) }} #{{ $expense->id }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                    href="{{url('expense')}}">{{ trans_choice('expense::general.expense',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('expense::general.expense',1) }}
                            #{{ $expense->id }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-body p-0">


                <table class="table table-bordered table-hover table-striped">
                    <tbody>
                    <tr>
                        <th>{{ trans_choice('core::general.id',1) }}</th>
                        <td>{{ $expense->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.type',1) }}</th>
                        <td>
                            @if(!empty($expense->expense_type))
                                {{$expense->expense_type->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.created_by',1) }}</th>
                        <td>
                            @if(!empty($expense->created_by))
                                {{$expense->created_by->first_name}} {{$expense->created_by->last_name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('accounting::general.asset',1) }} {{ trans_choice('accounting::general.account',1) }}</th>
                        <td>
                            @if(!empty($expense->asset_chart))
                                {{$expense->asset_chart->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('accounting::general.expense',1) }} {{ trans_choice('accounting::general.account',1) }}</th>
                        <td>
                            @if(!empty($expense->expense_chart))
                                {{$expense->expense_chart->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('expense::general.amount',1) }}</th>
                        <td>{{ number_format($expense->amount,2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.date',1) }}</th>
                        <td>{{ $expense->date }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('expense::general.recurring',1) }}</th>
                        <td>
                            @if($expense->recurring==1)
                                <span class="label label-success">{{ trans_choice('core::general.yes',1) }}</span>
                            @else
                                <span class="label label-danger">{{ trans_choice('core::general.no',1) }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($expense->recurring==1)
                        <tr>
                            <th>{{ trans_choice('expense::general.recur_frequency',1) }}</th>
                            <td>
                                {{ trans_choice('expense::general.every',1) }} {{ $expense->recur_frequency }} {{ trans_choice('expense::general.'.$expense_type->recur_type,2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ trans_choice('expense::general.recur_start_date',1) }}</th>
                            <td>{{ $expense->recur_start_date }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans_choice('expense::general.recur_next_date',1) }}</th>
                            <td>{{ $expense->recur_next_date }}</td>
                        </tr>
                        <tr>
                            <th>{{ trans_choice('expense::general.recur_end_date',1) }}</th>
                            <td>{{ $expense->recur_end_date }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ trans_choice('core::general.description',1) }}</th>
                        <td>{{ $expense->description }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection