@extends('core::layouts.master')
@section('title')
    {{ trans_choice('income::general.income',1) }} #{{ $income->id }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">  {{ trans_choice('income::general.income',1) }} #{{ $income->id }}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <a href="#" onclick="window.history.back()"
                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                    </a>

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
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
    </div>
@endsection
@section('scripts')

@endsection