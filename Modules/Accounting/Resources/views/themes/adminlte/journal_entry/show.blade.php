@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.view',1) }}  {{ trans_choice('core::general.transaction',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.view',1) }}  {{ trans_choice('core::general.transaction',1) }}
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
                                    href="{{url('accounting/journal_entry')}}">{{ trans_choice('accounting::general.journal',1) }} {{ trans_choice('core::general.entry',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('accounting::general.journal',1) }} {{ trans_choice('core::general.entry',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header with-border">
                <h6 class="card-title">{{ trans_choice('core::general.transaction',1) }}
                    # {{$data->first()->transaction_number}}</h6>

                <div class="card-tools">
                    @if($data->first()->reversed==0 && $data->first()->reversible==1)
                        @can('accounting.journal_entries.reverse')
                            <a href="{{ url('accounting/journal_entry/'.$data->first()->transaction_number.'/reverse') }}"
                               class="btn btn-danger btn-sm confirm">{{ trans_choice('accounting::general.reverse',1) }}</a>
                        @endcan
                    @else
                        <span class="text-danger">{{ trans_choice('core::general.transaction',1) }} {{ trans_choice('accounting::general.reversed',1) }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td>{{ trans_choice('core::general.branch',1) }}</td>
                        <td>
                            @if(!empty($data->first()->branch))
                                {{$data->first()->branch->name}}
                            @endif
                        </td>
                        <td>{{ trans_choice('core::general.transaction',1) }} {{ trans_choice('core::general.date',1) }}</td>
                        <td>
                            {{$data->first()->date}}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans_choice('core::general.created_by',1) }}</td>
                        <td>
                            @if(!empty($data->first()->created_by))
                                {{$data->first()->created_by->first_name}} {{$data->first()->created_by->last_name}}
                            @else
                                {{ trans_choice('core::general.system',1) }}
                            @endif
                        </td>
                        <td>{{ trans_choice('core::general.created_on',1) }}</td>
                        <td>
                            {{$data->first()->created_at}}
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th> {{ trans_choice('core::general.id',1) }}</th>
                        <th> {{ trans_choice('core::general.type',1) }}</th>
                        <th> {{ trans_choice('core::general.account',1) }}</th>
                        <th> {{ trans_choice('accounting::general.debit',1) }}</th>
                        <th> {{ trans_choice('accounting::general.credit',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                @if(!empty($key->chart_of_account))
                                    @if($key->chart_of_account->account_type=='asset')
                                        {{trans_choice('accounting::general.asset',1)}}
                                    @endif
                                    @if($key->chart_of_account->account_type=='expense')
                                        {{trans_choice('accounting::general.expense',1)}}
                                    @endif
                                    @if($key->chart_of_account->account_type=='equity')
                                        {{trans_choice('accounting::general.equity',1)}}
                                    @endif
                                    @if($key->chart_of_account->account_type=='liability')
                                        {{trans_choice('accounting::general.liability',1)}}
                                    @endif
                                    @if($key->chart_of_account->account_type=='income')
                                        {{trans_choice('accounting::general.income',1)}}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->chart_of_account))
                                    {{$key->chart_of_account->name}}
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->debit))
                                    {{number_format($key->debit,2)}}
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->credit))
                                    {{number_format($key->credit,2)}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">

            </div>
        </div>
    </section>
@endsection