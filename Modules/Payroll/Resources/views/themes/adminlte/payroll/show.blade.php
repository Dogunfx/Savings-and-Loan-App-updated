@extends('core::layouts.master')
@section('title')
    {{ trans_choice('payroll::general.payroll',1) }} #{{ $payroll->id }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('payroll::general.payroll',1) }} #{{ $payroll->id }}
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
                                    href="{{url('payroll')}}">{{ trans_choice('payroll::general.payroll',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.payroll',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">{{ trans_choice('payroll::general.payroll',1) }} #{{ $payroll->id }}</h3>
                    </div>

                    <div class="card-body">
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
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">{{ trans_choice('payroll::general.item',2) }}</h6>

                        <div class="card-tools">
                            <a href="{{ url('payroll/'.$payroll->id.'/pdf') }}" target="_blank"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> {{ trans_choice('core::general.pdf',1) }}
                            </a>
                            <a href="{{ url('payroll/'.$payroll->id.'/print') }}" target="_blank"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-print"></i> {{ trans_choice('core::general.print',1) }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
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
                        <h4 class="float-right">
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
                                        <div class="btn-group">
                                            <button href="#" class="btn btn-default dropdown-toggle"
                                                    data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @can('payroll.payroll.edit')
                                                    <a href="{{url('payroll/payment/' . $key->id . '/edit') }}"
                                                       class="dropdown-item">
                                                        <i class="far fa-edit"></i> {{ trans_choice('core::general.edit', 2) }}
                                                    </a>
                                                @endcan
                                                @can('payroll.payroll.destroy')
                                                    <a href="{{url('payroll/payment/' . $key->id . '/destroy') }}"
                                                       class="dropdown-item confirm">
                                                        <i class="fas fa-trash"></i> {{ trans_choice('core::general.delete', 2) }}
                                                    </a>

                                                @endcan
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
    </section>
@endsection
@section('scripts')

@endsection