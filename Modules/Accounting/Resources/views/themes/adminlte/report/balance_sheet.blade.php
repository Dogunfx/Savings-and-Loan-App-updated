@extends('core::layouts.master')
@section('title')
    {{trans_choice('accounting::general.balance_sheet',1)}}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans_choice('accounting::general.balance_sheet',1)}}</h1>
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
                                    href="{{url('report/accounting')}}">{{trans_choice('accounting::general.accounting',1)}} {{trans_choice('report::general.report',2)}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans_choice('accounting::general.balance_sheet',1)}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">
                    {{trans_choice('accounting::general.balance_sheet',1)}}
                    @if(!empty($start_date))
                        as on: {{$end_date}}</b>
                    @endif
                </h3>

                <div class="card-tools hidden-print">
                    <div class="dropdown">
                        <a href="#" class="btn btn-info btn-trigger btn-icon dropdown-toggle"
                           data-toggle="dropdown">
                            {{trans_choice('core::general.action',2)}}
                        </a>
                        <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                            <a href="{{url('report/accounting/balance_sheet?download=1&type=csv&end_date='.$end_date.'&branch_id='.$branch_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>
                            <a href="{{url('report/accounting/balance_sheet?download=1&type=excel&end_date='.$end_date.'&branch_id='.$branch_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>
                            <a href="{{url('report/accounting/balance_sheet?download=1&type=excel_2007&end_date='.$end_date.'&branch_id='.$branch_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>
                            <a href="{{url('report/accounting/balance_sheet?download=1&type=pdf&end_date='.$end_date.'&branch_id='.$branch_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.pdf_format',1)}}</a>
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
                                <select class="form-control" name="branch_id" id="branch_id" required>
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
        @if(!empty($end_date))
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th colspan="2">
                                {{trans_choice('core::general.branch',1)}}:
                                @if(!empty($data->first()))
                                    {{$data->first()->branch}}
                                @endif
                            </th>

                            <th>{{trans_choice('accounting::general.as_on',1)}}: {{$end_date}}</th>
                        </tr>
                        <tr class="bg-success">
                            <th>{{trans_choice('accounting::general.gl_code',1)}}</th>
                            <th>{{trans_choice('core::general.account',1)}}</th>
                            <th>{{trans_choice('accounting::general.balance',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        //group the results
                        $assets = [];
                        $liabilities = [];
                        $equities = [];
                        $total_assets = 0;
                        $total_liabilities = 0;
                        $total_equities = 0;
                        foreach ($data as $key) {
                            if ($key->account_type == 'asset') {
                                array_push($assets, $key);
                                $total_assets = $total_assets + ($key->debit - $key->credit);
                            }
                            if ($key->account_type == 'equity') {
                                array_push($equities, $key);
                                $total_equities = $total_equities + ($key->credit - $key->debit);
                            }
                            if ($key->account_type == 'liability') {
                                array_push($liabilities, $key);
                                $total_liabilities = $total_liabilities + ($key->credit - $key->debit);
                            }
                        }

                        ?>
                        <tr>
                            <td colspan="3" class="text-center"><h4>{{trans_choice('accounting::general.asset',2)}}</h4>
                            </td>
                        </tr>
                        @foreach($assets as $key)
                            <tr>
                                <td>{{ $key->gl_code }}</td>
                                <td>
                                    {{$key->name}}
                                </td>
                                <td>
                                    {{ number_format(($key->debit - $key->credit),2) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2">
                                <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.asset',2)}}</h4>
                            </td>
                            <td><h4>{{ number_format($total_assets,2) }}</h4></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-center">
                                <h4>{{trans_choice('accounting::general.liability',2)}}</h4>
                            </td>
                        </tr>

                        @foreach($liabilities as $key)
                            <tr>
                                <td>{{ $key->gl_code }}</td>
                                <td>
                                    {{$key->name}}
                                </td>
                                <td>
                                    {{ number_format(($key->credit - $key->debit),2) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2">
                                <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.liability',2)}}</h4>
                            </td>
                            <td><h4>{{ number_format($total_liabilities,2) }}</h4></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-center">
                                <h4>{{trans_choice('accounting::general.equity',2)}}</h4>
                            </td>
                        </tr>

                        @foreach($equities as $key)

                            <tr>
                                <td>{{ $key->gl_code }}</td>
                                <td>
                                    {{$key->name}}
                                </td>
                                <td>
                                    {{ number_format(($key->credit - $key->debit),2) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2">
                                <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.equity',2)}}</h4>
                            </td>
                            <td><h4>{{ number_format($total_equities,2) }}</h4></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <h4>{{trans_choice('core::general.total',1)}} {{trans_choice('accounting::general.liability',2)}} {{trans_choice('core::general.and',1)}} {{trans_choice('accounting::general.equity',2)}}</h4>
                            </td>
                            <td><h4>{{ number_format($total_equities+$total_liabilities,2) }}</h4></td>
                        </tr>
                        </tbody>

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
