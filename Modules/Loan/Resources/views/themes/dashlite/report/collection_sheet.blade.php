@extends('core::layouts.master')
@section('title')
    {{trans_choice('loan::general.collection_sheet',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('loan::general.collection_sheet',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="box-tools pull-right hidden-print">
                <div class="input-group ">
                    <button type="button" class="btn btn-info btn-sm  dropdown-toggle" data-toggle="dropdown"
                            aria-expanded="true"> {{trans_choice('core::general.action',2)}}
                        <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{url('report/loan/collection_sheet?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/loan/collection_sheet?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/loan/collection_sheet?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/loan/collection_sheet?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id.'&loan_product_id='.$loan_product_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="box-body">
            <form method="get" action="{{Request::url()}}" class="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="branch_id">{{trans_choice('core::general.branch',1)}}</label>
                            <select class="form-control select2" name="branch_id" id="branch_id">
                                <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
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
                            <input type="text" name="start_date" class="form-control date-picker"
                                   placeholder=""
                                   value="{{$start_date}}" id="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="end_date">{{trans_choice('core::general.end_date',1)}}</label>
                            <input type="text" name="end_date" class="form-control date-picker"
                                   placeholder=""
                                   value="{{$end_date}}" id="end_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"
                                   for="loan_officer_id">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                            <select class="form-control select2" name="loan_officer_id" id="loan_officer_id">
                                <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
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
                                <option value="" disabled selected>{{trans_choice('core::general.select',1)}}</option>
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
        <div class="box box-white">
            <div class="box-body table-responsive no-padding">


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
                        <th colspan="2">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                    </tr>
                    <tr style="background-color: #D1F9FF">
                        <th>{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</th>
                        <th>{{trans_choice('core::general.branch',1)}}</th>
                        <th>{{trans_choice('client::general.client',1)}}</th>
                        <th>{{trans_choice('core::general.mobile',1)}}</th>
                        <th>{{trans_choice('loan::general.loan',1)}}#</th>
                        <th>{{trans_choice('loan::general.product',1)}}</th>
                        <th>{{ trans_choice('loan::general.expected',1) }} {{ trans_choice('loan::general.maturity',1) }} {{ trans_choice('core::general.date',1) }}</th>
                        <th>{{trans_choice('loan::general.repayment',1)}} {{ trans_choice('core::general.date',1) }}</th>
                        <th>{{ trans_choice('loan::general.expected',1) }} {{trans_choice('loan::general.amount',1)}}</th>
                        <th>{{ trans_choice('loan::general.total',1) }} {{trans_choice('loan::general.due',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_due = 0;
                    $total_expected_amount = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $total_due = $total_due + $key->total_due;
                        $total_expected_amount = $total_expected_amount + $key->expected_amount;
                        ?>
                        <tr>
                            <td>{{ $key->loan_officer }}</td>
                            <td>{{ $key->branch }}</td>
                            <td>
                                {{$key->client}}
                            </td>
                            <td>{{ $key->mobile }}</td>
                            <td>{{ $key->loan_id }}</td>

                            <td>{{ $key->loan_product }}</td>
                            <td>{{ $key->expected_maturity_date }}</td>
                            <td>{{ $key->due_date }}</td>
                            <td>{{ number_format( $key->expected_amount,2) }}</td>
                            <td>{{ number_format( $key->total_due,2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="8"><b>{{trans_choice('core::general.total',1)}}</b></td>
                        <td>{{number_format($total_expected_amount,2)}}</td>
                        <td>{{number_format($total_due,2)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
