@extends('core::layouts.master')
@section('title')
    {{trans_choice('accounting::general.ledger',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">
                {{trans_choice('accounting::general.ledger',1)}}
                @if(!empty($start_date))
                    as on: {{$end_date}}</b>
                @endif
            </h6>

            <div class="box-tools pull-right hidden-print">
                <div class="input-group ">
                    <button type="button" class="btn btn-info btn-sm  dropdown-toggle" data-toggle="dropdown"
                            aria-expanded="true"> {{trans_choice('core::general.action',2)}}
                        <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{url('report/accounting/ledger?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/accounting/ledger?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/accounting/ledger?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                        </li>
                        <li>
                            <a href="{{url('report/accounting/ledger?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id)}}">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
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
        <div class="box box-white">
            <div class="box-body table-responsive no-padding">


                <table class="table table-bordered table-condensed table-hover table-striped">
                    <thead>
                    <tr>
                        <th colspan="2">
                            {{trans_choice('core::general.branch',1)}}:
                            @if(!empty($data->first()))
                                {{$data->first()->branch}}
                            @endif
                        </th>

                        <th colspan="2">{{trans_choice('accounting::general.as_on',1)}}: {{$start_date}} to {{$end_date}}</th>
                    </tr>
                    <tr class="bg-success">
                        <th>{{trans_choice('accounting::general.gl_code',1)}}</th>
                        <th>{{trans_choice('core::general.account',1)}}</th>
                        <th>Dr {{trans_choice('accounting::general.balance',1)}}</th>
                        <th>Cr {{trans_choice('accounting::general.balance',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    //group the results

                    $total_debit = 0;
                    $total_credit = 0;
                    ?>

                    @foreach($data as $key)
                        <?php
                        //group the results

                        $total_debit = $total_debit+$key->debit;
                        $total_credit = $total_credit+$key->credit;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>
                                {{$key->name}}
                            </td>
                            <td>
                                {{ number_format(($key->debit ),2) }}
                            </td>
                            <td>
                                {{ number_format(($key->credit ),2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><h4>{{trans_choice('core::general.total',1)}} </h4></td>
                        <td> <h4>{{ number_format($total_debit,2) }}</h4></td>
                        <td> <h4>{{ number_format($total_credit,2) }}</h4></td>
                    </tr>

                    </tbody>

                </table>
            </div>
        </div>

@endsection
@section('footer-scripts')

@endsection
