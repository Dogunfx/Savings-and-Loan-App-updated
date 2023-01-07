@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.view',1) }}  {{ trans_choice('core::general.transaction',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.transaction',1) }}
                # {{$data->first()->transaction_number}}</h6>

            <div class="box-tools pull-right">
                @if($data->first()->reversed==0 && $data->first()->reversible==1)
                    <a href="{{ url('journal_entry/'.$data->first()->transaction_number.'/reverse') }}"
                       class="btn btn-danger btn-sm confirm">{{ trans_choice('accounting::general.reverse',1) }}</a>
                @else
                    <span class="text-danger">{{ trans_choice('core::general.transaction',1) }} {{ trans_choice('accounting::general.reversed',1) }}</span>
                @endif
            </div>
        </div>
        <div class="box-body">
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
    <!-- /.box -->
@endsection