@extends('core::layouts.master')
@section('title')
    {{ trans_choice('income::general.income',1) }} #{{ $income->id }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('income::general.type',1) }}
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
                                    href="{{url('income/type')}}">{{ trans_choice('income::general.income',1) }}  {{ trans_choice('income::general.type',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('income::general.type',1) }}</li>
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
                        <td>{{ $income->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.type',1) }}</th>
                        <td>
                            @if(!empty($income->income_type))
                                {{$income->income_type->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.created_by',1) }}</th>
                        <td>
                            @if(!empty($income->created_by))
                                {{$income->created_by->first_name}} {{$income->created_by->last_name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('accounting::general.asset',1) }} {{ trans_choice('accounting::general.account',1) }}</th>
                        <td>
                            @if(!empty($income->asset_chart))
                                {{$income->asset_chart->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('accounting::general.income',1) }} {{ trans_choice('accounting::general.account',1) }}</th>
                        <td>
                            @if(!empty($income->income_chart))
                                {{$income->income_chart->name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('income::general.amount',1) }}</th>
                        <td>{{ number_format($income->amount,2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.date',1) }}</th>
                        <td>{{ $income->date }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.description',1) }}</th>
                        <td>{{ $income->description }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
@endsection
@section('scripts')

@endsection