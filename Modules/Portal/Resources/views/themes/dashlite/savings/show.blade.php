@extends('core::layouts.master')
@section('title')
    {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('savings::general.savings',1) }} {{ trans_choice('core::general.detail',2) }}
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
                                    href="{{url('savings')}}">{{ trans_choice('savings::general.savings',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.savings',1) }} {{ trans_choice('core::general.detail',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-12">
                <div class="panel ">
                    <div class="panel-heading">
                        <h6 class="panel-title">{{$savings->savings_product->name}}(#{{$savings->id}})</h6>

                        <div class="heading-elements">

                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">


                                    @if($savings->status=='active')
                                        @can('savings.savings.transactions.create')
                                            <a href="{{url('savings/'.$savings->id.'/deposit/create')}}"
                                               class="btn btn-success"><i class="fa fa-dollar"></i>
                                                {{ trans_choice('savings::general.make',1) }} {{ trans_choice('savings::general.deposit',1) }}
                                            </a>
                                            <a href="{{url('savings/'.$savings->id.'/withdrawal/create')}}"
                                               class="btn btn-warning"><i class="fa fa-money"></i>
                                                {{ trans_choice('savings::general.make',1) }} {{ trans_choice('savings::general.withdrawal',1) }}
                                            </a>
                                        @endcan


                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-8 col-md-8 p-10">
                                @if($savings->status=='submitted' ||$savings->status=='pending'||$savings->status=='withdrawn'||$savings->status=='approved'||$savings->status=='rejected')
                                    @if($savings->status=='submitted')
                                        <span class="label label-warning status-label">{{ trans_choice('savings::general.pending_approval',1) }}</span>
                                    @endif
                                    @if($savings->status=='approved')
                                        <span class="label label-warning status-label">{{ trans_choice('savings::general.awaiting_activation',1) }}</span>
                                    @endif
                                    @if($savings->status=='withdrawn')
                                        <span class="label label-danger status-label">{{ trans_choice('savings::general.withdrawn',1) }}</span>

                                    @endif
                                    @if($savings->status=='rejected')
                                        <span class="label label-danger status-label">{{ trans_choice('savings::general.rejected',1) }}</span>
                                    @endif
                                @endif
                                @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='dormant'||$savings->status=='inactive')
                                    <table class="table table-striped table-bordered">
                                        <tbody>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.current',1) }} {{ trans_choice('savings::general.balance',1) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->sum('credit')-$savings->transactions->where('reversed',0)->sum('debit'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.interest',1) }} {{ trans_choice('savings::general.earned',1) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',11)->sum('amount')+$savings->calculated_interest,$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.interest',1) }} {{ trans_choice('savings::general.posted',1) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',11)->sum('amount'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('core::general.total',1) }} {{ trans_choice('savings::general.deposit',2) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',1)->sum('amount'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('core::general.total',1) }} {{ trans_choice('savings::general.withdrawal',2) }}</th>
                                            <td>
                                                {{number_format($savings->transactions->where('reversed',0)->where('savings_transaction_type_id',2)->sum('amount'),$savings->decimals)}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.status',1) }}</th>
                                        <td>
                                            @if($savings->status=='submitted')
                                                <span class="label label-warning">{{ trans_choice('savings::general.pending_approval',1) }}</span>
                                            @endif
                                            @if($savings->status=='approved')
                                                <span class="label label-warning">{{ trans_choice('savings::general.awaiting_activation',1) }}</span>
                                            @endif
                                            @if($savings->status=='active')
                                                <span class="label label-success">{{ trans_choice('savings::general.active',1) }}</span>
                                            @endif
                                            @if($savings->status=='withdrawn')
                                                <span class="label label-danger">{{ trans_choice('savings::general.withdrawn',1) }}</span>
                                            @endif
                                            @if($savings->status=='rejected')
                                                <span class="label label-danger">{{ trans_choice('savings::general.rejected',1) }}</span>
                                            @endif
                                            @if($savings->status=='closed')
                                                <span class="label label-info">{{ trans_choice('savings::general.closed',1) }}</span>
                                            @endif
                                            @if($savings->status=='dormant')
                                                <span class="label label-warning">{{ trans_choice('savings::general.dormant',1) }}</span>
                                            @endif
                                            @if($savings->status=='inactive')
                                                <span class="label label-warning">{{ trans_choice('savings::general.inactive',1) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('core::general.currency',1) }}</th>
                                        <td>
                                            @if(!empty($savings->currency))
                                                {{$savings->currency->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.savings',1) }} {{ trans_choice('savings::general.officer',1) }}</th>
                                        <td>
                                            @if(!empty($savings->savings_officer))
                                                {{$savings->savings_officer->first_name}} {{$savings->savings_officer->last_name}}
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-savings">{{ trans_choice('savings::general.interest_rate',1) }}</th>
                                        <td>
                                            {{number_format($savings->interest_rate,2)}}%
                                        </td>
                                    </tr>
                                    @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='dormant'||$savings->status=='inactive')
                                        <tr>
                                            <th class="table-bold-savings">{{ trans_choice('savings::general.activated_on',1) }}</th>
                                            <td>
                                                {{$savings->activated_on_date}}
                                            </td>
                                        </tr>

                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#account_details"
                               data-toggle="tab">
                                {{ trans_choice('savings::general.account',1) }} {{ trans_choice('core::general.detail',2) }}
                            </a>
                        </li>
                        @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='dormant'||$savings->status=='overpaid'||$savings->status=='rescheduled')
                            <li class="">
                                <a href="#savings_transactions"
                                   data-toggle="tab">
                                    {{ trans_choice('savings::general.transaction',2) }}
                                </a>
                            </li>
                        @endif
                        <li class="">
                            <a href="#savings_charges"
                               data-toggle="tab">
                                {{ trans_choice('savings::general.charge',2) }}
                            </a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="account_details">
                            <table class="table table-striped table-hover">
                                <tbody>
                                <tr>
                                    <td>{{trans_choice('savings::general.compounding_period',1)}}</td>
                                    <td>
                                        @if($savings->compounding_period=='daily')
                                            {{trans_choice('savings::general.daily',2)}}
                                        @endif
                                        @if($savings->compounding_period=='weekly')
                                            {{trans_choice('savings::general.weekly',2)}}
                                        @endif
                                        @if($savings->compounding_period=='monthly')
                                            {{trans_choice('savings::general.monthly',2)}}
                                        @endif
                                        @if($savings->compounding_period=='biannual')
                                            {{trans_choice('savings::general.biannual',2)}}
                                        @endif
                                        @if($savings->compounding_period=='annually')
                                            {{trans_choice('savings::general.annually',2)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('savings::general.interest_posting_period_type',1)}}</td>
                                    <td>
                                        @if($savings->interest_posting_period_type=='daily')
                                            {{trans_choice('savings::general.daily',2)}}
                                        @endif
                                        @if($savings->interest_posting_period_type=='weekly')
                                            {{trans_choice('savings::general.weekly',2)}}
                                        @endif
                                        @if($savings->interest_posting_period_type=='monthly')
                                            {{trans_choice('savings::general.monthly',2)}}
                                        @endif
                                        @if($savings->interest_posting_period_type=='biannual')
                                            {{trans_choice('savings::general.biannual',2)}}
                                        @endif
                                        @if($savings->interest_posting_period_type=='annually')
                                            {{trans_choice('savings::general.annually',2)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('savings::general.interest_calculation_type',1)}}</td>
                                    <td>
                                        @if($savings->interest_calculation_type=='daily_balance')
                                            {{trans_choice('savings::general.daily_balance',1)}}
                                        @endif
                                        @if($savings->interest_calculation_type=='average_daily_balance')
                                            {{trans_choice('savings::general.average_balance',1)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('savings::general.category',1)}}</td>
                                    <td>
                                        @if($savings->savings_product->savings_category=='voluntary')
                                            {{trans_choice('savings::general.voluntary',1)}}
                                        @endif
                                        @if($savings->savings_product->savings_category=='compulsory')
                                            {{trans_choice('savings::general.compulsory',1)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('core::general.submitted_on',1)}}</td>
                                    <td>
                                        {{$savings->submitted_on_date}}
                                        {{trans_choice('core::general.by',1)}}
                                        @if(!empty($savings->submitted_by))
                                            {{$savings->submitted_by->first_name}} {{$savings->submitted_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('savings::general.approved',1)}} {{trans_choice('core::general.on',1)}}</td>
                                    <td>
                                        {{$savings->approved_on_date}}

                                        @if(!empty($savings->approved_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$savings->approved_by->first_name}} {{$savings->approved_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('savings::general.activated',1)}} {{trans_choice('core::general.on',1)}}</td>
                                    <td>
                                        {{$savings->activated_on_date}}

                                        @if(!empty($savings->activated_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$savings->activated_by->first_name}} {{$savings->activated_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        @if($savings->status=='active' ||$savings->status=='closed'||$savings->status=='inactive'||$savings->status=='dormant'||$savings->status=='rescheduled')
                            <div class="tab-pane" id="savings_transactions">
                                <table class="table table-striped table-hover" id="savings_transactions_table">
                                    <thead>
                                    <tr>
                                        <th>{{trans_choice('core::general.date',1)}}</th>
                                        <th>{{trans_choice('core::general.submitted_on',1)}}</th>
                                        <th>{{trans_choice('savings::general.transaction',1)}} {{trans_choice('core::general.type',1)}}</th>
                                        <th>{{trans_choice('savings::general.transaction',1)}} {{trans_choice('core::general.id',1)}}</th>
                                        <th>{{trans_choice('accounting::general.debit',1)}}</th>
                                        <th>{{trans_choice('accounting::general.credit',1)}}</th>
                                        <th>{{trans_choice('savings::general.balance',1)}}</th>
                                        <th>{{trans_choice('core::general.action',1)}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $balance = 0;
                                    ?>
                                    @foreach($savings->transactions as $key)
                                        <?php
                                        $balance = $balance + $key->credit - $key->debit;
                                        ?>
                                        <tr>
                                            <td>{{$key->created_on}}</td>
                                            <td>{{$key->submitted_on}}</td>
                                            <td>
                                                {{$key->name}}
                                            </td>
                                            <td>{{$key->id}}</td>
                                            <td>{{number_format($key->debit,$savings->decimals)}}</td>
                                            <td>{{number_format($key->credit,$savings->decimals)}}</td>
                                            <td>{{number_format($balance,$savings->decimals)}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                                            data-toggle="dropdown"
                                                            aria-expanded="true"><i class="fa fa-navicon"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li>
                                                            <a href="{{url('portal/savings/transaction/' . $key->id . '/show') }}"
                                                               class=""><i
                                                                        class="fa fa-search"></i> {{ trans_choice('core::general.view', 2) }}
                                                            </a></li>

                                                        <li>
                                                            <a href="{{url('portal/savings/transaction/' . $key->id . '/pdf') }}"
                                                               target="_blank"><i
                                                                        class="fa fa-file-pdf-o"></i> {{ trans_choice('core::general.receipt', 1) }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{url('portal/savings/transaction/' . $key->id . '/print') }}"
                                                               target="_blank"><i
                                                                        class="fa fa-print"></i> {{ trans_choice('core::general.print', 1) }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endcan

                        <div class="tab-pane" id="savings_charges">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.name',1) }}</th>
                                    <th>{{ trans_choice('savings::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</th>
                                    <th>{{ trans_choice('savings::general.collected_on',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($savings->charges as $key)
                                    <tr>
                                        <td>{{$key->name}}</td>
                                        <td>
                                            @if($key->savings_charge_option_id==1)
                                                {{number_format($key->amount,2)}} {{ trans_choice('savings::general.flat',1) }}
                                            @endif
                                            @if($key->savings_charge_option_id==2)
                                                {{number_format($key->amount,2)}}
                                                % {{ trans_choice('savings::general.percentage_of_amount',1) }}
                                            @endif
                                            @if($key->savings_charge_option_id==3)
                                                {{number_format($key->amount,2)}}
                                                %  {{ trans_choice('savings::general.percentage_of_savings_balance',1) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($key->savings_charge_type_id==1)
                                                {{ trans_choice('savings::general.savings_activation',1) }}
                                            @endif
                                            @if($key->savings_charge_type_id==2)
                                                {{ trans_choice('savings::general.specified_due_date',1) }}
                                            @endif
                                            @if($key->savings_charge_type_id==3)
                                                {{ trans_choice('savings::general.withdrawal_fee',1) }}
                                            @endif
                                            @if($key->savings_charge_type_id==4)
                                                {{ trans_choice('savings::general.annual_fee',1) }}
                                            @endif
                                            @if($key->savings_charge_type_id==5)
                                                {{ trans_choice('savings::general.monthly_fee',1) }}
                                            @endif
                                            @if($key->savings_charge_type_id==6)
                                                {{ trans_choice('savings::general.inactivity_fee',1) }}
                                            @endif
                                            @if($key->savings_charge_type_id==7)
                                                {{ trans_choice('savings::general.quarterly_fee',1) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($key->is_paid==1)
                                                {{ trans_choice('savings::general.charge',1) }} {{ trans_choice('savings::general.paid',1) }}
                                            @else
                                                @if($key->waived==1)
                                                    {{ trans_choice('savings::general.charge',1) }} {{ trans_choice('savings::general.waived',1) }}
                                                @else
                                                    {{ trans_choice('savings::general.outstanding',1) }}

                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')


    </script>
    @if($savings->status=='active')

    @endif
@endsection
