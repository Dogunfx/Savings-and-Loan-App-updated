@extends('core::layouts.master')
@section('title')
    {{ trans_choice('payroll::general.payroll',1) }} #{{ $payroll->id }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"> {{ trans_choice('payroll::general.payroll',1) }}
                        #{{ $payroll->id }}</h3>
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

    <div class="row">
        <div class="col-md-3">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <table class="table">
                            <tr>
                                <td>{{ trans_choice('user::general.user',1) }} </td>
                                <td>
                                    {{$payroll->employee_name}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('core::general.date',1) }} </td>
                                <td>
                                    {{$payroll->date}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('payroll::general.template',1) }} </td>
                                <td>
                                    @if(!empty($payroll->payroll_template))
                                        {{$payroll->payroll_template->name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('payroll::general.work_duration',1) }} </td>
                                <td>
                                    {{number_format($payroll->work_duration,2)}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('payroll::general.duration_unit',1) }} </td>
                                <td>
                                    {{$payroll->duration_unit}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('payroll::general.amount_per_duration',1) }} </td>
                                <td>
                                    {{number_format($payroll->amount_per_duration,2)}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('payroll::general.total_duration_amount',1) }} </td>
                                <td>
                                    {{number_format($payroll->total_duration_amount,2)}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('payroll::general.gross',1) }} {{ trans_choice('payroll::general.amount',1) }}</td>
                                <td>
                                    <b>{{number_format($payroll->gross_amount,2)}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td> {{ trans_choice('payroll::general.payment',2) }}</td>
                                <td>
                                    <b>{{number_format($payments,2)}}</b>
                                </td>
                            </tr>
                            <tr>
                                <td> {{ trans_choice('payroll::general.balance',1) }}</td>
                                <td>
                                    <b>{{number_format($payroll->gross_amount-$payments,2)}}</b>
                                </td>
                            </tr>
                        </table>

                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-9">

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="nk-block-head nk-block-head-lg">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title">{{ trans_choice('payroll::general.item',2) }}</h4>

                            </div>
                            <div class="nk-block-head-content">
                                <a href="{{ url('payroll/'.$payroll->id.'/pdf') }}" target="_blank"
                                   class="btn btn-info btn-sm">
                                    <i class="fa fa-file-pdf-o"></i> {{ trans_choice('core::general.pdf',1) }}
                                </a>
                                <a href="{{ url('payroll/'.$payroll->id.'/print') }}" target="_blank"
                                   class="btn btn-info btn-sm">
                                    <i class="fa fa-print"></i> {{ trans_choice('core::general.print',1) }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <h4>{{trans_choice('payroll::general.allowance',2)}}</h4>
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>{{trans_choice('payroll::general.allowance',1)}}</th>
                            <th>{{trans_choice('payroll::general.amount',1)}} {{trans_choice('payroll::general.type',1)}}</th>
                            <th>{{trans_choice('payroll::general.amount',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payroll->payroll_items->where('type','allowance')->all() as $key)
                            <tr v-for="(item,index) in selected_allowances">
                                <td>
                                    {{$key->name}}
                                </td>
                                <td>
                                    @if($key->amount_type=='fixed')
                                        {{trans_choice('payroll::general.fixed',1)}}
                                    @endif
                                    @if($key->amount_type=='percentage')
                                        {{trans_choice('payroll::general.percentage',1)}}
                                    @endif
                                </td>
                                <td>{{number_format($key->amount,2)}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2">{{trans_choice('payroll::general.total',1)}}</th>
                            <th colspan="1">{{ number_format($payroll->total_allowances,2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                    <h4>{{trans_choice('payroll::general.deduction',2)}}</h4>
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>{{trans_choice('payroll::general.deduction',1)}}</th>
                            <th>{{trans_choice('payroll::general.amount',1)}} {{trans_choice('payroll::general.type',1)}}</th>
                            <th>{{trans_choice('payroll::general.amount',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payroll->payroll_items->where('type','deduction')->all() as $key)
                            <tr v-for="(item,index) in selected_allowances">
                                <td>
                                    {{$key->name}}
                                </td>
                                <td>
                                    @if($key->amount_type=='fixed')
                                        {{trans_choice('payroll::general.fixed',1)}}
                                    @endif
                                    @if($key->amount_type=='percentage')
                                        {{trans_choice('payroll::general.percentage',1)}}
                                    @endif
                                </td>
                                <td>{{number_format($key->amount,2)}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2">{{trans_choice('payroll::general.total',1)}}</th>
                            <th colspan="1">{{ number_format($payroll->total_deductions,2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                    <h4>{{trans_choice('payroll::general.payment',2)}}</h4>
                    <h4 class="pull-right">
                        @can('payroll.payroll.create')
                            <a href="{{ url('payroll/'.$payroll->id.'/payment/create') }}"
                               class="btn btn-info btn-sm">
                                {{ trans_choice('core::general.add',1) }} {{ trans_choice('core::general.payment',1) }}
                            </a>
                        @endcan
                    </h4>
                    <div class="clearfix"></div>
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>{{trans_choice('core::general.id',1)}}</th>
                            <th>{{trans_choice('core::general.payment',1)}} {{trans_choice('payroll::general.type',1)}}</th>
                            <th>{{trans_choice('payroll::general.amount',1)}}</th>
                            <th>{{trans_choice('core::general.receipt',1)}}</th>
                            <th>{{trans_choice('core::general.date',1)}}</th>
                            <th>{{trans_choice('core::general.action',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payroll->payroll_payments as $key)
                            <tr>
                                <td>
                                    {{$key->id}}
                                </td>
                                <td>
                                    @if(!empty($key->payment_detail))
                                        @if(!empty($key->payment_detail->payment_type))
                                            {{$key->payment_detail->payment_type->name}}
                                        @endif
                                    @endif

                                </td>
                                <td>{{number_format($key->amount,2)}}</td>
                                <td>
                                    @if(!empty($key->payment_detail))
                                        {{$key->payment_detail->receipt}}
                                    @endif
                                </td>
                                <td>
                                    {{$key->submitted_on}}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                           data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-opt no-bdr">
                                                @can('payroll.payroll.edit')
                                                    <li><a href="{{url('payroll/payment/' . $key->id . '/edit') }}"
                                                           class="">{{ trans_choice('core::general.edit', 2) }}</a></li>
                                                @endcan
                                                @can('payroll.payroll.destroy')
                                                    <li><a href="{{url('payroll/payment/' . $key->id . '/destroy') }}"
                                                           class="confirm">{{ trans_choice('core::general.delete', 2) }}</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2">{{trans_choice('payroll::general.total',1)}}</th>
                            <th colspan="1">{{ number_format($payments,2) }}</th>
                            <th colspan="3"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')

@endsection