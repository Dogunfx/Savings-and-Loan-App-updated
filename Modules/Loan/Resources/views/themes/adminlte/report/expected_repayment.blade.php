@extends('core::layouts.master')
@section('title')
    {{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.repayment',2)}}
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
                    {{trans_choice('loan::general.expected',1)}} {{trans_choice('loan::general.repayment',2)}}
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
                            <a href="{{url('report/loan/expected_repayment?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                            <a href="{{url('report/loan/expected_repayment?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                            <a href="{{url('report/loan/expected_repayment?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                            <a href="{{url('report/loan/expected_repayment?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
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
                    <table class="table table-bordered table-condensed table-striped table-hover">
                        <thead>
                        <tr>
                            <th colspan="2">
                                @if(!empty($data->first()) && !empty($branch_id))
                                    {{trans_choice('core::general.branch',1)}}:

                                    {{$data->first()->branch}}
                                @endif
                            </th>
                            <th colspan="2">
                                @if(!empty($data->first()) && !empty($loan_product_id))
                                    {{trans_choice('loan::general.product',1)}}:

                                    {{$data->first()->loan_product}}
                                @endif
                            </th>
                            <th colspan="2">
                                @if(!empty($data->first()) && !empty($loan_officer_id))
                                    {{trans_choice('loan::general.officer',1)}}:

                                    {{$data->first()->loan_officer}}
                                @endif
                            </th>
                            <th colspan="3">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                            <th colspan="3">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th colspan="5">{{trans_choice('loan::general.expected',1)}}</th>
                            <th colspan="5">{{trans_choice('loan::general.actual',1)}}</th>
                            <th></th>
                        </tr>
                        <tr style="background-color: #D1F9FF">
                            <th>{{trans_choice('core::general.branch',1)}}</th>
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
                            <th>{{ trans_choice('loan::general.balance',1) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total_actual_principal = 0;
                        $total_actual_interest = 0;
                        $total_actual_fees = 0;
                        $total_actual_penalties = 0;
                        $total_actual_amount = 0;
                        $total_expected_principal = 0;
                        $total_expected_interest = 0;
                        $total_expected_fees = 0;
                        $total_expected_penalties = 0;
                        $total_expected_amount = 0;
                        ?>
                        @foreach($data as $key)
                            <?php
                            $total_actual_principal = $total_actual_principal + $key->principal_repaid_derived;
                            $total_actual_interest = $total_actual_interest + $key->interest_repaid_derived;
                            $total_actual_fees = $total_actual_fees + $key->fees_repaid_derived;
                            $total_actual_penalties = $total_actual_penalties + $key->penalties_repaid_derived;

                            $total_expected_principal = $total_expected_principal + $key->principal;
                            $total_expected_interest = $total_expected_interest + $key->interest;
                            $total_expected_fees = $total_expected_fees + $key->fees;
                            $total_expected_penalties = $total_expected_penalties + $key->penalties;
                            ?>
                            <tr>
                                <td>{{ $key->branch }}</td>
                                <td>{{ number_format( $key->principal,2) }}</td>
                                <td>{{ number_format( $key->interest,2) }}</td>
                                <td>{{ number_format( $key->fees,2) }}</td>
                                <td>{{ number_format( $key->penalties,2) }}</td>
                                <td>{{ number_format( $key->principal+$key->interest+$key->fees+$key->penalties,2) }}</td>
                                <td>{{ number_format( $key->principal_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->interest_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->fees_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->penalties_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->principal_repaid_derived+$key->interest_repaid_derived+$key->fees_repaid_derived+$key->penalties_repaid_derived,2) }}</td>
                                <td>{{ number_format( ($key->principal+$key->interest+$key->fees+$key->penalties)-($key->principal_repaid_derived+$key->interest_repaid_derived+$key->fees_repaid_derived+$key->penalties_repaid_derived),2) }}</td>

                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th><b>{{trans_choice('core::general.total',1)}}</b></th>
                            <th>{{number_format($total_expected_principal,2)}}</th>
                            <th>{{number_format($total_expected_interest,2)}}</th>
                            <th>{{number_format($total_expected_fees,2)}}</th>
                            <th>{{number_format($total_expected_penalties,2)}}</th>
                            <th>{{number_format($total_expected_principal+$total_expected_interest+$total_expected_fees+$total_expected_penalties,2)}}</th>
                            <th>{{number_format($total_actual_principal,2)}}</th>
                            <th>{{number_format($total_actual_interest,2)}}</th>
                            <th>{{number_format($total_actual_fees,2)}}</th>
                            <th>{{number_format($total_actual_penalties,2)}}</th>
                            <th>{{number_format($total_actual_principal+$total_actual_interest+$total_actual_fees+$total_actual_penalties,2)}}</th>
                            <th>{{number_format(($total_expected_principal+$total_expected_interest+$total_expected_fees+$total_expected_penalties)-($total_actual_principal+$total_actual_interest+$total_actual_fees+$total_actual_penalties),2)}}</th>

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