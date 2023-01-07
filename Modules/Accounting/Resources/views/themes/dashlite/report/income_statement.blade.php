@extends('core::layouts.master')
@section('title')
    {{trans_choice('accounting::general.income_statement',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('accounting::general.income_statement',1)}}
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
                            <a href="{{url('report/accounting/income_statement?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/accounting/income_statement?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/accounting/income_statement?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/accounting/income_statement?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
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
                            <select class="form-control select2" name="branch_id" id="branch_id" required>
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
                            {{trans_choice('core::general.branch',1)}}:
                            @if(!empty($data->first()))
                                {{$data->first()->branch}}
                            @endif
                        </th>
                        <th>{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                        <th>{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                    </tr>
                    <tr style="background-color: #D1F9FF">
                        <th>{{trans_choice('accounting::general.gl_code',1)}}</th>
                        <th>{{trans_choice('core::general.account',1)}}</th>
                        <th>{{trans_choice('accounting::general.debit',1)}}</th>
                        <th>{{trans_choice('accounting::general.credit',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $credit_total = 0;
                    $debit_total = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $dr = 0;
                        $cr = 0;
                        if ($key->account_type == 'expense') {
                            $dr = $key->debit - $key->credit;
                            $class='text-danger';
                        } else {
                            $cr = $key->credit - $key->debit;
                            $class='text-success';
                        }
                        $credit_total = $credit_total + $cr;
                        $debit_total = $debit_total + $dr;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td class="{{$class}}">
                                {{$key->name}}
                            </td>
                            <td>
                                @if(!empty($dr))
                                    {{ number_format($dr,2) }}
                                @endif
                            </td>
                            <td>
                                @if(!empty($cr))
                                    {{ number_format($cr,2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2"><b>{{trans_choice('core::general.total',1)}}</b></td>
                        <td>{{number_format($debit_total,2)}}</td>
                        <td>{{number_format($credit_total,2)}}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><b>{{trans_choice('accounting::general.net_income',1)}}</b></td>
                        <td>{{number_format($credit_total-$debit_total,2)}}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
