@extends('core::layouts.master')
@section('title')
    {{trans_choice('loan::general.disbursement',2)}}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans_choice('loan::general.collection_sheet',1)}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                    href="{{url('report')}}">{{ trans_choice('report::general.report',2) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                    href="{{url('report/loan')}}">{{trans_choice('loan::general.loan',1)}} {{trans_choice('report::general.report',2)}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans_choice('loan::general.collection_sheet',1)}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header with-border">
                <h6 class="card-title">
                    {{trans_choice('loan::general.disbursement',2)}}
                    @if(!empty($start_date))
                        for period: <b>{{$start_date}} to {{$end_date}}</b>
                    @endif
                </h6>

                <div class="card-tools hidden-print">
                    <div class="dropdown">
                        <a href="#" class="btn btn-info btn-trigger btn-icon dropdown-toggle"
                           data-toggle="dropdown">
                            {{trans_choice('core::general.action',2)}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                            <a href="{{url('report/loan/disbursement?download=1&type=csv&&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                            <a href="{{url('report/loan/disbursement?download=1&type=excel&&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                            <a href="{{url('report/loan/disbursement?download=1&type=excel_2007&&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                            <a href="{{url('report/loan/disbursement?download=1&type=pdf&&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="{{Request::url()}}" class="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="branch_id">{{trans_choice('core::general.branch',1)}}</label>
                                <select class="form-control select2" name="branch_id" id="branch_id">
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($branches as $key)
                                        <option value="{{$key->id}}"
                                                @if($branch_id==$key->id) selected @endif>{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="start_date">{{trans_choice('core::general.start_date',1)}}</label>
                                <flat-pickr value="{{$start_date}}"
                                            class="form-control  @error('start_date') is-invalid @enderror"
                                            name="start_date" id="start_date" required>
                                </flat-pickr>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="end_date">{{trans_choice('core::general.end_date',1)}}</label>
                                <flat-pickr value="{{$end_date}}"
                                            class="form-control  @error('end_date') is-invalid @enderror"
                                            name="end_date" id="end_date" required>
                                </flat-pickr>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="loan_officer_id">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                                <select class="form-control select2" name="loan_officer_id" id="loan_officer_id">
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($users as $key)
                                        <option value="{{$key->id}}"
                                                @if($loan_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                       for="loan_product_id">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.product',1)}}</label>
                                <select class="form-control select2" name="loan_product_id" id="loan_product_id">
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($loan_products as $key)
                                        <option value="{{$key->id}}"
                                                @if($loan_product_id==$key->id) selected @endif>{{$key->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">{{trans_choice('core::general.filter',1)}}
                          </button>
                        </span>
                            <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                        </div>
                    </div>
                </form>

            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.box -->
        @if(!empty($start_date))
            <div class="card">
                <div class="card-body table-responsive p-0">


                    <table class="table table-bordered table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th colspan="6">
                                @if(!empty($data->first()) && !empty($branch_id))
                                    {{trans_choice('core::general.branch',1)}}:

                                    {{$data->first()->branch}}
                                @endif
                            </th>
                            <th colspan="9">

                            </th>
                            <th colspan="4">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                            <th colspan="4">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                        </tr>
                        <tr>
                            <th colspan="8"></th>
                            <th colspan="5"></th>
                            <th colspan="5">{{ trans_choice('loan::general.outstanding',1) }}</th>
                            <th colspan="5"></th>
                        </tr>
                        <tr style="background-color: #D1F9FF">
                            <th>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</th>
                            <th>{{trans_choice('core::general.branch',1)}}</th>
                            <th>{{trans_choice('client::general.client',1)}}</th>
                            <th>{{trans_choice('core::general.mobile',1)}}</th>
                            <th>{{trans_choice('loan::general.loan',1)}}#</th>
                            <th>{{trans_choice('loan::general.product',1)}}</th>
                            <th>{{trans_choice('loan::general.disbursed',1)}} {{trans_choice('core::general.date',1)}}</th>
                            <th>{{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.maturity',1)}} {{trans_choice('core::general.date',1)}}</th>
                            <th>{{trans_choice('loan::general.principal',1)}}</th>
                            <th>{{ trans_choice('loan::general.interest',1) }}</th>
                            <th>{{trans_choice('loan::general.fee',2)}}</th>
                            <th>{{ trans_choice('loan::general.penalty',2) }}</th>
                            <th>{{ trans_choice('loan::general.total',1) }}</th>
                            <th>{{trans_choice('loan::general.principal',1)}}</th>
                            <th>{{ trans_choice('loan::general.interest',1) }}</th>
                            <th>{{trans_choice('loan::general.fee',2)}}</th>
                            <th>{{ trans_choice('loan::general.penalty',2) }}</th>
                            <th>{{ trans_choice('loan::general.total',1) }}</th>
                            <th>{{ trans_choice('loan::general.fund',1) }}</th>
                            <th>{{ trans_choice('loan::general.purpose',1) }}</th>
                            <th>{{ trans_choice('loan::general.status',1) }}</th>
                            <th>{{ trans_choice('loan::general.arrears',1) }} {{ trans_choice('loan::general.amount',1) }}</th>
                            <th>{{ trans_choice('loan::general.day',2) }} {{ trans_choice('core::general.in',1) }} {{ trans_choice('loan::general.arrears',1) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total_principal = 0;
                        $total_principal_outstanding = 0;
                        $total_principal_overdue = 0;
                        $total_interest = 0;
                        $total_interest_outstanding = 0;
                        $total_interest_overdue = 0;
                        $total_fees = 0;
                        $total_fees_outstanding = 0;
                        $total_fees_overdue = 0;
                        $total_penalties = 0;
                        $total_penalties_outstanding = 0;
                        $total_penalties_overdue = 0;
                        $total_arrears_amount = 0;
                        ?>
                        @foreach($data as $key)
                            <?php
                            $total_principal = $total_principal + $key->repayment_schedules->sum('principal');
                            $total_interest = $total_interest + $key->repayment_schedules->sum('interest');
                            $total_fees = $total_fees + $key->repayment_schedules->sum('fees');
                            $total_penalties = $total_penalties + $key->repayment_schedules->sum('penalties');
                            $principal_outstanding = $key->repayment_schedules->sum('principal') - $key->repayment_schedules->sum('principal_written_off_derived') - $key->repayment_schedules->sum('principal_repaid_derived');
                            $interest_outstanding = $key->repayment_schedules->sum('interest') - $key->repayment_schedules->sum('interest_waived_derived') - $key->repayment_schedules->sum('interest_repaid_derived') - $key->repayment_schedules->sum('interest_written_off_derived');
                            $fees_outstanding = $key->repayment_schedules->sum('fees') + $key->disbursement_charges - $key->repayment_schedules->sum('fees_waived_derived') - $key->repayment_schedules->sum('fees_repaid_derived') + $key->disbursement_charges - $key->repayment_schedules->sum('fees_written_off_derived');
                            $penalties_outstanding = $key->repayment_schedules->sum('penalties') - $key->repayment_schedules->sum('penalties_waived_derived') - $key->repayment_schedules->sum('penalties_repaid_derived') - $key->repayment_schedules->sum('penalties_written_off_derived');
                            $total_principal_outstanding = $total_principal_outstanding + $principal_outstanding;
                            $total_interest_outstanding = $total_interest_outstanding + $interest_outstanding;
                            $total_fees_outstanding = $total_fees_outstanding + $fees_outstanding;
                            $total_penalties_outstanding = $total_penalties_outstanding + $penalties_outstanding;
                            //arrears
                            $principal_overdue = 0;
                            $interest_overdue = 0;
                            $fees_overdue = 0;
                            $penalties_overdue = 0;
                            $arrears_days = 0;
                            $past_schedules = $key->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->count();
                            $trp = 0;

                            $arrears_last_schedule = $key->repayment_schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
                            if (!empty($arrears_last_schedule)) {
                                $overdue_schedules = $key->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);

                                $principal_overdue = $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived');
                                $interest_overdue = $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived');
                                $fees_overdue = $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived');
                                $penalties_overdue = $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');

                                $total_principal_overdue = $total_principal_overdue + $principal_overdue;
                                $total_interest_overdue = $total_interest_overdue + $interest_overdue;
                                $total_fees_overdue = $total_fees_overdue + $fees_overdue;
                                $total_penalties_overdue = $total_penalties_overdue + $penalties_overdue;
                                $arrears_days = $arrears_days + \Illuminate\Support\Carbon::today()->diffInDays(\Illuminate\Support\Carbon::parse($overdue_schedules->sortBy('due_date')->first()->due_date));

                            }
                            $total_overdue = $principal_overdue + $interest_overdue + $fees_overdue + $penalties_overdue;
                            $balance = $principal_outstanding + $interest_outstanding + $penalties_outstanding + $fees_outstanding;
                            ?>
                            <tr>
                                <td>{{ $key->loan_officer }}</td>
                                <td>{{ $key->branch }}</td>
                                <td>
                                    {{$key->client}}
                                </td>
                                <td>{{ $key->mobile }}</td>
                                <td>{{ $key->id }}</td>
                                <td>{{ $key->loan_product }}</td>
                                <td>{{ $key->disbursed_on_date }}</td>
                                <td>{{ $key->expected_maturity_date }}</td>
                                <td>{{ number_format($key->repayment_schedules->sum('principal'),2) }}</td>
                                <td>{{ number_format($key->repayment_schedules->sum('interest'),2) }}</td>
                                <td>{{ number_format($key->repayment_schedules->sum('fees'),2) }}</td>
                                <td>{{ number_format($key->repayment_schedules->sum('penalties'),2) }}</td>
                                <td>{{ number_format($key->repayment_schedules->sum('principal')+$key->repayment_schedules->sum('interest')+$key->repayment_schedules->sum('fees')+$key->repayment_schedules->sum('penalties'),2) }}</td>
                                <td>{{ number_format( $principal_outstanding,2) }}</td>
                                <td>{{ number_format( $interest_outstanding,2) }}</td>
                                <td>{{ number_format( $fees_outstanding,2) }}</td>
                                <td>{{ number_format( $penalties_outstanding,2) }}</td>
                                <td>{{ number_format($principal_outstanding+$interest_outstanding+$fees_outstanding+ $penalties_outstanding,2) }}</td>
                                <td>{{ $key->fund }}</td>
                                <td>{{ $key->loan_purpose }}</td>
                                <td>
                                    @if($key->status=='active')
                                        {{trans_choice('loan::general.active',1)}}
                                    @endif
                                    @if($key->status=='closed')
                                        {{trans_choice('loan::general.closed',1)}}
                                    @endif
                                    @if($key->status=='rescheduled')
                                        {{trans_choice('loan::general.rescheduled',1)}}
                                    @endif
                                    @if($key->status=='written_off')
                                        {{trans_choice('loan::general.written_off',1)}}
                                    @endif
                                </td>
                                <td>{{ number_format( $total_overdue,2) }}</td>
                                <td>{{$arrears_days}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="8"><b>{{trans_choice('core::general.total',1)}}</b></td>
                            <td>{{number_format($total_principal,2)}}</td>
                            <td>{{number_format($total_interest,2)}}</td>
                            <td>{{number_format($total_fees,2)}}</td>
                            <td>{{number_format($total_penalties,2)}}</td>
                            <td>{{number_format($total_principal+$total_interest+$total_fees+$total_penalties,2)}}</td>
                            <td>{{number_format($total_principal_outstanding,2)}}</td>
                            <td>{{number_format($total_interest_outstanding,2)}}</td>
                            <td>{{number_format($total_fees_outstanding,2)}}</td>
                            <td>{{number_format($total_penalties_outstanding,2)}}</td>
                            <td>{{number_format($total_principal_outstanding+$total_interest_outstanding+$total_fees_outstanding+$total_penalties_outstanding,2)}}</td>
                            <td colspan="3"></td>
                            <td>{{number_format($total_principal_overdue+$total_interest_overdue+$total_fees_overdue+$total_penalties_overdue,2)}}</td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {},
            methods: {},
        })
    </script>
@endsection