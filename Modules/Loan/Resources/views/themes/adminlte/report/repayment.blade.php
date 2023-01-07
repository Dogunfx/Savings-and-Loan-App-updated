@extends('core::layouts.master')
@section('title')
    {{trans_choice('loan::general.repayment',2)}}
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
        <div class="card ">
            <div class="card-header">
                <h6 class="card-title">
                    {{trans_choice('loan::general.repayment',2)}}
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
                            <a href="{{url('report/loan/repayment?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                            <a href="{{url('report/loan/repayment?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                            <a href="{{url('report/loan/repayment?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                            <a href="{{url('report/loan/repayment?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}" class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
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
                                <select class="form-control select2" name="branch_id" id="branch_id" required>
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


                    <table class="table table-bordered table-condensed table-hover">
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
                            <th colspan="2">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                            <th colspan="3">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                        </tr>
                        <tr style="background-color: #D1F9FF">
                            <th>{{trans_choice('core::general.id',1)}}</th>
                            <th>{{trans_choice('client::general.client',1)}}</th>
                            <th>{{trans_choice('loan::general.loan',1)}}#</th>
                            <th>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</th>
                            <th>{{trans_choice('loan::general.principal',1)}}</th>
                            <th>{{ trans_choice('loan::general.interest',1) }}</th>
                            <th>{{trans_choice('loan::general.fee',2)}}</th>
                            <th>{{ trans_choice('loan::general.penalty',2) }}</th>
                            <th>{{ trans_choice('loan::general.total',1) }}</th>
                            <th>{{ trans_choice('core::general.date',1) }}</th>
                            <th>{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.type',1) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total_principal = 0;
                        $total_interest = 0;
                        $total_fees = 0;
                        $total_penalties = 0;
                        $total_amount = 0;
                        ?>
                        @foreach($data as $key)
                            <?php
                            $total_principal = $total_principal + $key->principal_repaid_derived;
                            $total_interest = $total_interest + $key->interest_repaid_derived;
                            $total_fees = $total_fees + $key->fees_repaid_derived;
                            $total_penalties = $total_penalties + $key->penalties_repaid_derived;
                            ?>
                            <tr>
                                <td>{{ $key->id }}</td>
                                <td>
                                    {{$key->client}}
                                </td>
                                <td>{{ $key->loan_id }}</td>
                                <td>{{ $key->loan_officer }}</td>
                                <td>{{ number_format( $key->principal_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->interest_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->fees_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->penalties_repaid_derived,2) }}</td>
                                <td>{{ number_format( $key->principal_repaid_derived+$key->interest_repaid_derived+$key->fees_repaid_derived+$key->penalties_repaid_derived,2) }}</td>
                                <td>{{ $key->submitted_on }}</td>
                                <td>{{ $key->payment_type }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"><b>{{trans_choice('core::general.total',1)}}</b></td>
                            <td>{{number_format($total_principal,2)}}</td>
                            <td>{{number_format($total_interest,2)}}</td>
                            <td>{{number_format($total_fees,2)}}</td>
                            <td>{{number_format($total_penalties,2)}}</td>
                            <td>{{number_format($total_principal+$total_interest+$total_fees+$total_penalties,2)}}</td>
                            <td colspan="2"></td>
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
