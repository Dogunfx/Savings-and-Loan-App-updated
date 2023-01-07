@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-inner">
                    <h5 class="card-title">{{$loan->loan_product->name}}(#{{$loan->id}})</h5>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if($loan->status=='submitted' ||$loan->status=='pending')

                                    @endif
                                    @if($loan->status=='active')
                                        @can('loan.loans.transactions.create')
                                            <a href="{{url('loan/'.$loan->id.'/repayment/create')}}"
                                               class="btn btn-primary"><i class="fa fa-dollar"></i>
                                                {{ trans_choice('loan::general.make',1) }} {{ trans_choice('loan::general.repayment',1) }}
                                            </a>
                                        @endcan


                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-sm-8 col-md-8 p-10">
                                @if($loan->status=='submitted' ||$loan->status=='pending'||$loan->status=='withdrawn'||$loan->status=='approved'||$loan->status=='rejected')
                                    @if($loan->status=='submitted')
                                        <span class="label label-warning status-label">{{ trans_choice('loan::general.pending_approval',1) }}</span>
                                    @endif
                                    @if($loan->status=='approved')
                                        <span class="label label-warning status-label">{{ trans_choice('loan::general.awaiting_disbursement',1) }}</span>
                                    @endif
                                    @if($loan->status=='withdrawn')
                                        <span class="label label-danger status-label">{{ trans_choice('loan::general.withdrawn',1) }}</span>

                                    @endif
                                    @if($loan->status=='rejected')
                                        <span class="label label-danger status-label">{{ trans_choice('loan::general.rejected',1) }}</span>
                                    @endif
                                @endif
                                @if($loan->status=='active' ||$loan->status=='closed'||$loan->status=='written_off'||$loan->status=='overpaid'||$loan->status=='rescheduled')
                                    <?php
                                    $balance = 0;
                                    $timely_repayments = 0;

                                    $principal = $loan->repayment_schedules->sum('principal');
                                    $principal_waived = $loan->repayment_schedules->sum('principal_waived_derived');
                                    $principal_paid = $loan->repayment_schedules->sum('principal_repaid_derived');
                                    $principal_written_off = 0;
                                    $principal_outstanding = 0;
                                    $principal_overdue = 0;
                                    $interest = $loan->repayment_schedules->sum('interest');
                                    $interest_waived = $loan->repayment_schedules->sum('interest_waived_derived');
                                    $interest_paid = $loan->repayment_schedules->sum('interest_repaid_derived');
                                    $interest_written_off = $loan->repayment_schedules->sum('interest_written_off_derived');
                                    $interest_outstanding = 0;
                                    $interest_overdue = 0;
                                    $fees = $loan->repayment_schedules->sum('fees') + $loan->disbursement_charges;
                                    $fees_waived = $loan->repayment_schedules->sum('fees_waived_derived');
                                    $fees_paid = $loan->repayment_schedules->sum('fees_repaid_derived') + $loan->disbursement_charges;
                                    $fees_written_off = $loan->repayment_schedules->sum('fees_written_off_derived');
                                    $fees_outstanding = 0;
                                    $fees_overdue = 0;
                                    $penalties = $loan->repayment_schedules->sum('penalties');
                                    $penalties_waived = $loan->repayment_schedules->sum('penalties_waived_derived');
                                    $penalties_paid = $loan->repayment_schedules->sum('penalties_repaid_derived');
                                    $penalties_written_off = $loan->repayment_schedules->sum('penalties_written_off_derived');
                                    $penalties_outstanding = 0;
                                    $penalties_overdue = 0;
                                    //arrears
                                    $arrears_days = 0;
                                    $arrears_amount = 0;
                                    $arrears_last_schedule = $loan->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
                                    if (!empty($arrears_last_schedule)) {
                                        $overdue_schedules = $loan->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);
                                        $principal_overdue = $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived');
                                        $interest_overdue = $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived');
                                        $fees_overdue = $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived');
                                        $penalties_overdue = $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');
                                        $arrears_days = $arrears_days + \Illuminate\Support\Carbon::today()->diffInDays(\Illuminate\Support\Carbon::parse($overdue_schedules->sortBy('due_date')->first()->due_date));
                                    }

                                    $principal_outstanding = $principal - $principal_waived - $principal_paid - $principal_written_off;
                                    $interest_outstanding = $interest - $interest_waived - $interest_paid - $interest_written_off;
                                    $fees_outstanding = $fees - $fees_waived - $fees_paid - $fees_written_off;
                                    $penalties_outstanding = $penalties - $penalties_waived - $penalties_paid - $penalties_written_off;
                                    $balance = $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalties_outstanding;
                                    $arrears_amount = $principal_overdue + $interest_overdue + $fees_overdue + $penalties_overdue;
                                    ?>
                                    <h4 class="">{{ trans_choice('loan::general.balance',1) }}
                                        :
                                        <b>{{number_format($balance,$loan->decimals)}}</b>
                                    </h4>
                                    <h4 class="hidden">
                                        {{ trans_choice('loan::general.timely',1) }} {{ trans_choice('loan::general.repayment',2) }}
                                        :
                                        <b> 0%</b>
                                    </h4>
                                    <h4 class="">
                                        {{ trans_choice('loan::general.amount',1) }} {{ trans_choice('core::general.in',1) }} {{ trans_choice('loan::general.arrears',1) }}
                                        :
                                        <b class=" @if($arrears_amount) text-danger @endif">{{number_format($arrears_amount,$loan->decimals)}}</b>
                                    </h4>
                                    <h4 class="">
                                        {{ trans_choice('loan::general.day',2) }} {{ trans_choice('core::general.in',1) }} {{ trans_choice('loan::general.arrears',1) }}
                                        :
                                        <b class=" @if($arrears_days) text-danger @endif">{{$arrears_days}}</b>
                                    </h4>
                                    <table class="pretty displayschedule" id="summarytable">
                                        <thead>
                                        <tr>
                                            <th class="empty"></th>
                                            <th>{{ trans_choice('loan::general.contract',1) }}</th>
                                            <th>{{ trans_choice('loan::general.paid',1) }}</th>
                                            <th>{{ trans_choice('loan::general.waived',1) }}</th>
                                            <th>{{ trans_choice('loan::general.written_off',1) }}</th>
                                            <th>{{ trans_choice('loan::general.outstanding',1) }}</th>
                                            <th>{{ trans_choice('loan::general.overdue',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.principal',1) }}</th>
                                            <td>{{number_format($principal,$loan->decimals)}}</td>
                                            <td>{{number_format($principal_paid,$loan->decimals)}}</td>
                                            <td>{{number_format($principal_waived,$loan->decimals)}}</td>
                                            <td>{{number_format($principal_written_off,$loan->decimals)}}</td>
                                            <td>{{number_format($principal_outstanding,$loan->decimals)}}</td>
                                            <td>{{number_format($principal_overdue,$loan->decimals)}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.interest',1) }}</th>
                                            <td>{{number_format($interest,$loan->decimals)}}</td>
                                            <td>{{number_format($interest_paid,$loan->decimals)}}</td>
                                            <td>{{number_format($interest_waived,$loan->decimals)}}</td>
                                            <td>{{number_format($interest_written_off,$loan->decimals)}}</td>
                                            <td>{{number_format($interest_outstanding,$loan->decimals)}}</td>
                                            <td>{{number_format($interest_overdue,$loan->decimals)}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.fee',2) }}</th>
                                            <td>{{number_format($fees,$loan->decimals)}}</td>
                                            <td>{{number_format($fees_paid,$loan->decimals)}}</td>
                                            <td>{{number_format($fees_waived,$loan->decimals)}}</td>
                                            <td>{{number_format($fees_written_off,$loan->decimals)}}</td>
                                            <td>{{number_format($fees_outstanding,$loan->decimals)}}</td>
                                            <td>{{number_format($fees_overdue,$loan->decimals)}}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.penalty',2) }}</th>
                                            <td>{{number_format($penalties,$loan->decimals)}}</td>
                                            <td>{{number_format($penalties_paid,$loan->decimals)}}</td>
                                            <td>{{number_format($penalties_waived,$loan->decimals)}}</td>
                                            <td>{{number_format($penalties_written_off,$loan->decimals)}}</td>
                                            <td>{{number_format($penalties_outstanding,$loan->decimals)}}</td>
                                            <td>{{number_format($penalties_overdue,$loan->decimals)}}</td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>{{ trans_choice('loan::general.total',1) }}</th>
                                            <th>{{number_format(($principal+$interest+$penalties+$fees),$loan->decimals)}}</th>
                                            <th>{{number_format(($principal_paid+$interest_paid+$fees_paid+$penalties_paid),$loan->decimals)}}</th>
                                            <th>{{number_format(($principal_waived+$interest_waived+$fees_waived+$penalties_waived),$loan->decimals)}}</th>
                                            <th>{{number_format(($principal_written_off+$interest_written_off+$fees_written_off+$penalties_written_off),$loan->decimals)}}</th>
                                            <th>{{number_format(($principal_outstanding+$interest_outstanding+$fees_outstanding+$penalties_outstanding),$loan->decimals)}}</th>
                                            <th>{{number_format(($principal_overdue+$interest_overdue+$fees_overdue+$penalties_overdue),$loan->decimals)}}</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @endif
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('loan::general.status',1) }}</th>
                                        <td>
                                            @if($loan->status=='submitted')
                                                <span class="label label-warning">{{ trans_choice('loan::general.pending_approval',1) }}</span>
                                            @endif
                                            @if($loan->status=='approved')
                                                <span class="label label-warning">{{ trans_choice('loan::general.awaiting_disbursement',1) }}</span>
                                            @endif
                                            @if($loan->status=='active')
                                                <span class="label label-success">{{ trans_choice('loan::general.active',1) }}</span>
                                            @endif
                                            @if($loan->status=='withdrawn')
                                                <span class="label label-danger">{{ trans_choice('loan::general.withdrawn',1) }}</span>
                                            @endif
                                            @if($loan->status=='rejected')
                                                <span class="label label-danger">{{ trans_choice('loan::general.rejected',1) }}</span>
                                            @endif
                                            @if($loan->status=='closed')
                                                <span class="label label-info">{{ trans_choice('loan::general.closed',1) }}</span>
                                            @endif
                                            @if($loan->status=='written_off')
                                                <span class="label label-danger">{{ trans_choice('loan::general.written_off',1) }}</span>
                                            @endif
                                            @if($loan->status=='rescheduled')
                                                <span class="label label-warning">{{ trans_choice('loan::general.rescheduled',1) }}</span>
                                            @endif
                                            @if($loan->status=='overpaid')
                                                <span class="label label-info">{{ trans_choice('loan::general.overpaid',1) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('client::general.client',1) }}</th>
                                        <td>
                                            @if(!empty($loan->client))
                                                <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('core::general.currency',1) }}</th>
                                        <td>
                                            @if(!empty($loan->currency))
                                                {{$loan->currency->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.officer',1) }}</th>
                                        <td>
                                            @if(!empty($loan->loan_officer))
                                                {{$loan->loan_officer->first_name}} {{$loan->loan_officer->last_name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.purpose',1) }}</th>
                                        <td>
                                            @if(!empty($loan->loan_purpose))
                                                {{$loan->loan_purpose->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('loan::general.fund',1) }}</th>
                                        <td>
                                            @if(!empty($loan->fund))
                                                {{$loan->fund->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{ trans_choice('loan::general.applied_amount',1) }}</th>
                                        <td>
                                            {{number_format($loan->applied_amount,$loan->decimals)}}
                                        </td>
                                    </tr>
                                    @if($loan->status=='active' ||$loan->status=='closed'||$loan->status=='approved'||$loan->status=='written_off'||$loan->status=='overpaid'||$loan->status=='rescheduled')

                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.approved_amount',1) }}</th>
                                            <td>
                                                {{number_format($loan->approved_amount,$loan->decimals)}}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($loan->status=='pending' ||$loan->status=='approved'||$loan->status=='submitted')
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.expected',1) }} {{ trans_choice('loan::general.disbursement',1) }} {{ trans_choice('core::general.date',1) }}</th>
                                            <td>
                                                {{$loan->expected_disbursement_date}}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($loan->status=='active' ||$loan->status=='closed'||$loan->status=='written_off'||$loan->status=='overpaid'||$loan->status=='rescheduled')
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.disbursed_amount',1) }}</th>
                                            <td>
                                                {{number_format($loan->principal,$loan->decimals)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-bold-loan">{{ trans_choice('loan::general.disbursement',1) }} {{ trans_choice('core::general.date',1) }}</th>
                                            <td>
                                                {{$loan->disbursed_on_date}}
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
    </div>
    <div class="row my-4">
        <div class="col-md-12">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#account_details" class="nav-link active"
                               data-toggle="tab">
                                {{ trans_choice('loan::general.account',1) }} {{ trans_choice('core::general.detail',2) }}
                            </a>
                        </li>
                        @if($loan->status=='active' ||$loan->status=='closed'||$loan->status=='written_off'||$loan->status=='overpaid'||$loan->status=='rescheduled')
                            <li class="nav-item">
                                <a href="#repayment_schedule" class="nav-link"
                                   data-toggle="tab">
                                    {{ trans_choice('loan::general.repayment',1) }} {{ trans_choice('loan::general.schedule',1) }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#loan_transactions" class="nav-link"
                                   data-toggle="tab">
                                    {{ trans_choice('loan::general.transaction',2) }}
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#loan_charges" class="nav-link"
                               data-toggle="tab">
                                {{ trans_choice('loan::general.charge',2) }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#loan_files" class="nav-link"
                               data-toggle="tab">
                                {{ trans_choice('loan::general.file',2) }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#loan_collateral" class="nav-link"
                               data-toggle="tab">
                                {{ trans_choice('loan::general.collateral',2) }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#loan_guarantors" class="nav-link"
                               data-toggle="tab">
                                {{ trans_choice('loan::general.guarantor',2) }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#loan_notes" class="nav-link"
                               data-toggle="tab">
                                {{ trans_choice('core::general.note',2) }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="account_details">
                            <table class="table table-striped table-hover">
                                <tbody>
                                <tr>
                                    <td>{{trans_choice('loan::general.loan_transaction_processing_strategy',1)}}</td>
                                    <td>
                                        @if(!empty($loan->loan_transaction_processing_strategy))
                                            {{$loan->loan_transaction_processing_strategy->translated_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.term',1)}}</td>
                                    <td>
                                        {{$loan->loan_term}}
                                        @if($loan->repayment_frequency_type=='days')
                                            {{trans_choice('loan::general.day',2)}}
                                        @endif
                                        @if($loan->repayment_frequency_type=='weeks')
                                            {{trans_choice('loan::general.week',2)}}
                                        @endif
                                        @if($loan->repayment_frequency_type=='months')
                                            {{trans_choice('loan::general.month',2)}}
                                        @endif
                                        @if($loan->repayment_frequency_type=='years')
                                            {{trans_choice('loan::general.year',2)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.repayment',2)}}</td>
                                    <td>
                                        {{trans_choice('loan::general.every',1)}} {{$loan->repayment_frequency}}
                                        @if($loan->repayment_frequency_type=='days')
                                            {{trans_choice('loan::general.day',2)}}
                                        @endif
                                        @if($loan->repayment_frequency_type=='weeks')
                                            {{trans_choice('loan::general.week',2)}}
                                        @endif
                                        @if($loan->repayment_frequency_type=='months')
                                            {{trans_choice('loan::general.month',2)}}
                                        @endif
                                        @if($loan->repayment_frequency_type=='years')
                                            {{trans_choice('loan::general.year',2)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.interest_methodology',1)}}</td>
                                    <td>
                                        @if($loan->interest_methodology=='flat')
                                            {{trans_choice('loan::general.flat',1)}}
                                        @endif
                                        @if($loan->interest_methodology=='declining_balance')
                                            {{trans_choice('loan::general.declining_balance',1)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.interest',1)}}</td>
                                    <td>
                                        {{number_format($loan->interest_rate,2)}} %
                                        {{trans_choice('loan::general.per',1)}}
                                        @if($loan->interest_rate_type=='month')
                                            {{trans_choice('loan::general.month',1)}}
                                        @endif
                                        @if($loan->interest_rate_type=='year')
                                            {{trans_choice('loan::general.year',1)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.grace_on_principal_paid',1)}}</td>
                                    <td>
                                        {{$loan->grace_on_principal_paid}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.grace_on_interest_paid',1)}}</td>
                                    <td>
                                        {{$loan->grace_on_interest_paid}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.grace_on_interest_charged',1)}}</td>
                                    <td>
                                        {{$loan->grace_on_interest_charged}}
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{trans_choice('core::general.submitted_on',1)}}</td>
                                    <td>
                                        {{$loan->submitted_on_date}}
                                        @if(!empty($loan->submitted_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$loan->submitted_by->first_name}} {{$loan->submitted_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.approved',1)}} {{trans_choice('core::general.on',1)}}</td>
                                    <td>
                                        {{$loan->approved_on_date}}
                                        @if(!empty($loan->approved_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$loan->approved_by->first_name}} {{$loan->approved_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans_choice('loan::general.disbursed',1)}} {{trans_choice('core::general.on',1)}}</td>
                                    <td>
                                        {{$loan->disbursed_on_date}}
                                        @if(!empty($loan->disbursed_by))
                                            {{trans_choice('core::general.by',1)}}
                                            {{$loan->disbursed_by->first_name}} {{$loan->disbursed_by->last_name}}
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        @if($loan->status=='active' ||$loan->status=='closed'||$loan->status=='written_off'||$loan->status=='overpaid'||$loan->status=='rescheduled')
                            <div class="tab-pane" id="repayment_schedule">
                                <div class="m-4">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-info  btn-action dropdown-toggle"
                                                data-toggle="dropdown">
                                            <span>{{trans_choice('core::general.action',1)}}</span>
                                            <em class="icon ni ni-chevron-down"></em>
                                        </button>
                                        <div class="dropdown-menu mt-1">
                                            <ul class="link-list-plain">
                                                <li><a href="{{url('portal/loan/'.$loan->id.'/schedule/email')}}"
                                                       class="confirm"><i
                                                                class="fa fa-envelope"></i> {{trans_choice('core::general.email',1)}} {{trans_choice('loan::general.schedule',1)}}
                                                    </a></li>
                                                <li><a href="{{url('portal/loan/'.$loan->id.'/schedule/print')}}"
                                                       target="_blank"><i
                                                                class="fa fa-print"></i> {{trans_choice('core::general.print',1)}} {{trans_choice('loan::general.schedule',1)}}
                                                    </a></li>
                                                <li><a href="{{url('portal/loan/'.$loan->id.'/schedule/pdf')}}"
                                                       target="_blank"><i
                                                                class="fa fa-file-pdf-o"></i> {{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf',1)}}
                                                    </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <table class="pretty displayschedule" id="repaymentschedule"
                                       style="margin-top: 20px;">
                                    <colgroup span="3"></colgroup>
                                    <colgroup span="3">
                                        <col class="lefthighlightcol">
                                        <col>
                                        <col class="righthighlightcol">
                                    </colgroup>
                                    <colgroup span="3">
                                        <col class="lefthighlightcol">
                                        <col>
                                        <col class="righthighlightcol">
                                    </colgroup>
                                    <colgroup span="3"></colgroup>
                                    <thead>
                                    <tr>
                                        <th class="empty" scope="colgroup" colspan="5">&nbsp;</th>
                                        <th class="highlightcol" scope="colgroup"
                                            colspan="3">{{trans_choice('loan::general.loan_amount_and_balance',1)}}</th>
                                        <th class="highlightcol" scope="colgroup"
                                            colspan="3">{{trans_choice('loan::general.total_cost_of_loan',1)}}</th>
                                        <th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{trans_choice('core::general.date',1)}}</th>
                                        <th scope="col"># {{trans_choice('loan::general.day',2)}}</th>
                                        <th scope="col">{{trans_choice('loan::general.paid',1)}} {{trans_choice('core::general.by',1)}}</th>
                                        <th scope="col"></th>
                                        <th class="lefthighlightcolheader"
                                            scope="col">{{trans_choice('loan::general.disbursement',1)}}</th>
                                        <th scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.due',1)}}</th>
                                        <th class="righthighlightcolheader"
                                            scope="col">{{trans_choice('loan::general.principal',1)}} {{trans_choice('loan::general.balance',1)}}</th>

                                        <th class="lefthighlightcolheader"
                                            scope="col">{{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.due',1)}}</th>
                                        <th scope="col">{{trans_choice('loan::general.fee',2)}}</th>
                                        <th class="righthighlightcolheader"
                                            scope="col">{{trans_choice('loan::general.penalty',2)}}

                                        </th>
                                        <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.due',1)}}</th>
                                        <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.paid',1)}}</th>
                                        <th scope="col">{{trans_choice('loan::general.total',1)}} {{trans_choice('loan::general.outstanding',1)}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td scope="row"></td>
                                        <td>{{$loan->disbursed_on_date}}</td>
                                        <td></td>
                                        <td><span style="color: #eb2442;"></span></td>
                                        <td>&nbsp;</td>
                                        <td class="lefthighlightcolheader">{{number_format($loan->principal,$loan->decimals)}}</td>
                                        <td></td>
                                        <td class="righthighlightcolheader">{{number_format($loan->principal,$loan->decimals)}}</td>
                                        <td class="lefthighlightcolheader"></td>
                                        <td>{{number_format($loan->disbursement_charges,$loan->decimals)}}</td>
                                        <td class="righthighlightcolheader"></td>
                                        <td>{{number_format($loan->disbursement_charges,$loan->decimals)}}</td>
                                        <td>{{number_format($loan->disbursement_charges,$loan->decimals)}}</td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    $count = 1;
                                    $total_days = 0;
                                    $total_principal = 0;
                                    $total_interest = 0;
                                    $total_fees = 0 + $loan->disbursement_charges;
                                    $total_penalties = 0;
                                    $total_due = 0;
                                    $total_paid = 0 + $loan->disbursement_charges;
                                    $total_outstanding = 0;
                                    $balance = $loan->principal
                                    ?>
                                    @foreach($loan->repayment_schedules as $key)
                                        <?php
                                        $days = \Carbon\Carbon::parse($key->due_date)->diffInDays(\Illuminate\Support\Carbon::parse($key->from_date));
                                        $total_days = $total_days + $days;
                                        $balance = $balance - $key->principal;
                                        $principal = $key->principal - $key->principal_waived_derived - $key->principal_written_off_derived;
                                        $interest = $key->interest - $key->interest_waived_derived - $key->interest_written_off_derived;
                                        $fees = $key->fees - $key->fees_waived_derived - $key->fees_written_off_derived;
                                        $penalties = $key->penalties - $key->penalties_waived_derived - $key->penalties_written_off_derived;
                                        $due = $principal + $interest + $fees + $penalties;
                                        $paid = $key->principal_repaid_derived + $key->interest_repaid_derived + $key->fees_repaid_derived + $key->penalties_repaid_derived;
                                        $outstanding = $due - $paid;
                                        $total_principal = $total_principal + $principal;
                                        $total_interest = $total_interest + $interest;
                                        $total_fees = $total_fees + $fees;
                                        $total_penalties = $total_penalties + $penalties;
                                        $total_due = $total_due + $due;
                                        $total_paid = $total_paid + $paid;
                                        $total_outstanding = $total_outstanding + $outstanding;

                                        ?>
                                        <tr>
                                            <td scope="row">{{$count}}</td>
                                            <td>{{$key->due_date}}</td>
                                            <td>{{$days}}</td>
                                            <td>
                                                @if($outstanding<=0)
                                                    <span style="@if(\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))color: #eb2442; @endif">{{$key->paid_by_date}}</span>
                                                @elseif($outstanding>0 && \Illuminate\Support\Carbon::now()->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                                                    <span style="color: #eb2442;">{{trans_choice('loan::general.overdue',1)}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($outstanding<=0)
                                                    @if(\Illuminate\Support\Carbon::parse($key->paid_by_date)->greaterThan(\Illuminate\Support\Carbon::parse($key->due_date)))
                                                        <i class="fa fa-question-circle"></i>
                                                    @else
                                                        <i class="fa fa-check-circle"></i>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="lefthighlightcolheader"></td>
                                            <td>{{number_format($principal,$loan->decimals)}}</td>
                                            <td class="righthighlightcolheader">{{number_format($balance,$loan->decimals)}}</td>
                                            <td class="lefthighlightcolheader">
                                                {{number_format($interest,$loan->decimals)}}
                                            </td>
                                            <td>{{number_format($fees,$loan->decimals)}}</td>
                                            <td class="righthighlightcolheader">{{number_format($penalties,$loan->decimals)}}</td>
                                            <td>{{number_format($due,$loan->decimals)}}</td>
                                            <td>{{number_format($paid,$loan->decimals)}}</td>
                                            <td>{{number_format($outstanding,$loan->decimals)}}</td>
                                        </tr>
                                        <?php
                                        $count++;
                                        ?>
                                    @endforeach
                                    </tbody>
                                    <tfoot class="ui-widget-header">
                                    <tr>
                                        <th colspan="2">{{trans_choice('loan::general.total',1)}}</th>
                                        <th>{{$total_days}}</th>
                                        <th></th>
                                        <th></th>
                                        <th class="lefthighlightcolheader">{{number_format($loan->principal,$loan->decimals)}}</th>
                                        <th>{{number_format($total_principal,$loan->decimals)}}</th>
                                        <th class="righthighlightcolheader">&nbsp;</th>
                                        <th class="lefthighlightcolheader">{{number_format($total_interest,$loan->decimals)}}</th>
                                        <th>{{number_format($total_fees,$loan->decimals)}}</th>
                                        <th class="righthighlightcolheader">{{number_format($total_penalties,$loan->decimals)}}</th>
                                        <th>{{number_format($total_due,$loan->decimals)}}</th>
                                        <th>{{number_format($total_paid,$loan->decimals)}}</th>
                                        <th>{{number_format($total_outstanding,$loan->decimals)}}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="tab-pane" id="loan_transactions">
                                <a href="{{url('portal/loan/'.$loan->id.'/repayment/create')}}"
                                   class="btn btn-info m-4"><i
                                            class="fa fa-money"></i> {{trans_choice('core::general.pay',1)}} {{trans_choice('core::general.online',1)}}
                                </a>
                                <table class="table table-striped table-hover" id="loan_transactions_table">
                                    <thead>
                                    <tr>
                                        <th>{{trans_choice('core::general.date',1)}}</th>
                                        <th>{{trans_choice('core::general.submitted_on',1)}}</th>
                                        <th>{{trans_choice('loan::general.transaction',1)}} {{trans_choice('core::general.type',1)}}</th>
                                        <th>{{trans_choice('loan::general.transaction',1)}} {{trans_choice('core::general.id',1)}}</th>
                                        <th>{{trans_choice('accounting::general.debit',1)}}</th>
                                        <th>{{trans_choice('accounting::general.credit',1)}}</th>
                                        <th>{{trans_choice('loan::general.balance',1)}}</th>
                                        <th>{{trans_choice('core::general.action',1)}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $balance = $loan->principal;
                                    ?>
                                    @foreach($loan->transactions as $key)
                                        <?php
                                        if ($key->loan_transaction_type_id == 10 || $key->loan_transaction_type_id == 11) {
                                            $balance = $balance + $key->amount;
                                        }
                                        if ($key->loan_transaction_type_id == 2 || $key->loan_transaction_type_id == 4 || $key->loan_transaction_type_id == 8 || $key->loan_transaction_type_id == 9 || $key->loan_transaction_type_id == 6) {
                                            $balance = $balance - $key->amount;
                                        }
                                        ?>
                                        <tr>
                                            <td>{{$key->created_on}}</td>
                                            <td>{{$key->submitted_on}}</td>
                                            <td>
                                                @if($key->loan_transaction_type_id == 1)
                                                    {{trans_choice('loan::general.disbursement',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 2)
                                                    {{trans_choice('loan::general.repayment',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 3)
                                                    {{trans_choice('loan::general.contra',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 4)
                                                    {{trans_choice('loan::general.waive',1)}} {{trans_choice('loan::general.interest',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 5)
                                                    {{trans_choice('loan::general.repayment',1)}} {{trans_choice('core::general.at',1)}} {{trans_choice('loan::general.disbursement',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 6)
                                                    {{trans_choice('loan::general.write_off',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 7)
                                                    {{trans_choice('loan::general.marked_for_rescheduling',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 8)
                                                    {{trans_choice('loan::general.recovery',1)}} {{trans_choice('loan::general.repayment',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 9)
                                                    {{trans_choice('loan::general.waive',1)}} {{trans_choice('loan::general.charge',2)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 10)
                                                    {{trans_choice('loan::general.fee',1)}} {{trans_choice('loan::general.applied',1)}}
                                                @endif
                                                @if($key->loan_transaction_type_id == 11)
                                                    {{trans_choice('loan::general.interest',1)}} {{trans_choice('loan::general.applied',1)}}
                                                @endif
                                            </td>
                                            <td>{{$key->id}}</td>
                                            <td>{{number_format($key->debit,$loan->decimals)}}</td>
                                            <td>{{number_format($key->credit,$loan->decimals)}}</td>
                                            <td>{{number_format($balance,$loan->decimals)}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                       data-toggle="dropdown"><em
                                                                class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a href="{{url('portal/loan/transaction/' . $key->id . '/show') }}"
                                                                   class=""><i
                                                                            class="fa fa-search"></i> {{ trans_choice('core::general.view', 2) }}
                                                                </a></li>
                                                            @if($key->loan_transaction_type_id == 2 && $key->reversed==0)
                                                                <li>
                                                                    <a href="{{url('portal/loan/transaction/' . $key->id . '/pdf') }}"
                                                                       target="_blank"><i
                                                                                class="fa fa-file-pdf-o"></i> {{ trans_choice('core::general.receipt', 1) }}
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{url('portal/loan/transaction/' . $key->id . '/print') }}"
                                                                       target="_blank"><i
                                                                                class="fa fa-print"></i> {{ trans_choice('core::general.print', 1) }}
                                                                    </a>
                                                                </li>

                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="tab-pane" id="loan_charges">

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.name',1) }}</th>
                                    <th>{{ trans_choice('loan::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</th>
                                    <th>{{ trans_choice('core::general.amount',1) }}</th>
                                    <th>{{ trans_choice('loan::general.collected_on',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($loan->charges as $key)
                                    <tr>
                                        <td>{{$key->name}}</td>
                                        <td>
                                            @if($key->loan_charge_option_id==1)
                                                {{number_format($key->amount,2)}} {{ trans_choice('loan::general.flat',1) }}
                                            @endif
                                            @if($key->loan_charge_option_id==2)
                                                {{number_format($key->amount,2)}}
                                                % {{ trans_choice('loan::general.principal_due_on_installment',1) }}
                                            @endif
                                            @if($key->loan_charge_option_id==3)
                                                {{number_format($key->amount,2)}}
                                                %  {{ trans_choice('loan::general.principal_interest_due_on_installment',1) }}
                                            @endif
                                            @if($key->loan_charge_option_id==4)
                                                {{number_format($key->amount,2)}}
                                                % {{ trans_choice('loan::general.interest_due_on_installment',1) }}
                                            @endif
                                            @if($key->loan_charge_option_id==5)
                                                {{number_format($key->amount,2)}}
                                                %  {{ trans_choice('loan::general.total_outstanding_loan_principal',1) }}
                                            @endif
                                            @if($key->loan_charge_option_id==6)
                                                {{number_format($key->amount,2)}}
                                                % {{ trans_choice('loan::general.percentage_of_original_loan_principal_per_installment',1) }}
                                            @endif
                                            @if($key->loan_charge_option_id==7)
                                                {{number_format($key->amount,2)}}
                                                % {{ trans_choice('loan::general.original_loan_principal',1) }}
                                            @endif
                                        </td>
                                        <td>{{number_format($key->calculated_amount,$loan->decimals)}}</td>
                                        <td>
                                            @if($key->loan_charge_type_id==1)
                                                {{ trans_choice('loan::general.disbursement',1) }}
                                            @endif
                                            @if($key->loan_charge_type_id==2)
                                                {{ trans_choice('loan::general.specified_due_date',1) }}
                                            @endif
                                            @if($key->loan_charge_type_id==3)
                                                {{ trans_choice('loan::general.installment',1) }} {{ trans_choice('loan::general.fee',2) }}
                                            @endif
                                            @if($key->loan_charge_type_id==4)
                                                {{ trans_choice('loan::general.overdue',1) }} {{ trans_choice('loan::general.installment',1) }} {{ trans_choice('loan::general.fee',1) }}
                                            @endif
                                            @if($key->loan_charge_type_id==5)
                                                {{ trans_choice('loan::general.disbursement_paid_with_repayment',1) }}
                                            @endif
                                            @if($key->loan_charge_type_id==6)
                                                {{ trans_choice('loan::general.loan_rescheduling_fee',1) }}
                                            @endif
                                            @if($key->loan_charge_type_id==7)
                                                {{ trans_choice('loan::general.overdue_on_loan_maturity',1) }}
                                            @endif
                                            @if($key->loan_charge_type_id==8)
                                                {{ trans_choice('loan::general.last_installment_fee',1) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($key->loan_charge_type_id==1 ||$key->loan_charge_type_id==5)
                                                {{ trans_choice('loan::general.charge',1) }} {{ trans_choice('loan::general.paid',1) }}
                                            @else
                                                @if($key->waived==1)
                                                    {{ trans_choice('loan::general.charge',1) }} {{ trans_choice('loan::general.waived',1) }}
                                                @else

                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="loan_files">

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.name',1) }}</th>
                                    <th>{{ trans_choice('core::general.description',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($loan->files as $key)
                                    <tr>
                                        <td>{{$key->name}}</td>
                                        <td>{{$key->description}}</td>
                                        <td>
                                            <a href="{{asset('storage/uploads/loans/'.$key->link)}}"
                                               target="_blank"><i class="fa fa-download"></i> </a>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="loan_collateral">

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('loan::general.type',1) }}</th>
                                    <th>{{ trans_choice('loan::general.value',1) }}</th>
                                    <th>{{ trans_choice('core::general.description',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($loan->collateral as $key)
                                    <tr>
                                        <td>
                                            @if(!empty($key->collateral_type))
                                                {{$key->collateral_type->name}}
                                            @endif
                                        </td>
                                        <td>{{number_format($key->value,$loan->decimals)}}</td>
                                        <td>{{$key->description}}</td>
                                        <td>
                                            <a href="{{asset('storage/uploads/loans/'.$key->link)}}"
                                               target="_blank"><i class="fa fa-download"></i> </a>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="loan_guarantors">

                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.name',1) }}</th>
                                    <th>{{ trans_choice('core::general.amount',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($loan->guarantors as $key)
                                    <tr>
                                        <td>
                                            @if($key->is_client==1)
                                                @if(!empty($key->client))
                                                    <a href="{{url('client/'.$key->client_id.'/show')}}">
                                                        {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                                    </a>
                                                @endif
                                            @else
                                                {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                            @endif
                                        </td>
                                        <td>{{number_format($key->guaranteed_amount,$loan->decimals)}}</td>
                                        <td>
                                            @if($key->is_client==1)
                                                <a href="#"><i
                                                            class="fa fa-eye"></i> </a>
                                            @else
                                                <a href="{{url('portal/loan/guarantor/'.$key->id.'/show')}}"><i
                                                            class="fa fa-eye"></i> </a>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="loan_notes">
                            <div>

                                @foreach($loan->notes as $key)
                                    <blockquote>
                                        <p>{{$key->description}}</p>
                                        @if(!empty($key->created_by))
                                            <small>{{trans_choice('core::general.by',1)}} {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                                {{trans_choice('core::general.on',1)}} <cite
                                                        title="Source Title">{{$key->created_at}}</cite></small>
                                        @else
                                            <small>  {{trans_choice('core::general.on',1)}} <cite
                                                        title="Source Title">{{$key->created_at}}</cite></small>
                                        @endif

                                    </blockquote>
                                @endforeach
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

    @if($loan->status=='active')
        <script>

        </script>
    @endif
@endsection
