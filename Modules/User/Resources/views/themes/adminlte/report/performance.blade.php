@extends('core::layouts.master')
@section('title')
    {{trans_choice('user::general.performance_report',2)}}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans_choice('user::general.performance_report',2)}}</h1>
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
                                    href="{{url('report/user')}}">{{trans_choice('user::general.user',1)}} {{trans_choice('report::general.report',2)}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans_choice('user::general.performance_report',2)}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header with-border">
                <h6 class="card-title">
                    {{trans_choice('user::general.performance_report',2)}}
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
                            <a href="{{url('report/user/performance?download=1&type=csv&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.csv_format',1)}}</a>

                            <a href="{{url('report/user/performance?download=1&type=excel&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_format',1)}}</a>

                            <a href="{{url('report/user/performance?download=1&type=excel_2007&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id)}}"
                               class="dropdown-item">{{trans_choice('core::general.download',1)}} {{trans_choice('core::general.excel_2007_format',1)}}</a>

                            <a href="{{url('report/user/performance?download=1&type=pdf&start_date='.$start_date.'&end_date='.$end_date.'&branch_id='.$branch_id.'&loan_officer_id='.$loan_officer_id)}}"
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
                                       for="loan_officer_id">{{trans_choice('user::general.staff',1)}} </label>
                                <select class="form-control select2" name="loan_officer_id"
                                        id="loan_officer_id" required>
                                    <option value="" disabled
                                            selected>{{trans_choice('core::general.select',1)}}</option>
                                    @foreach($users as $key)
                                        <option value="{{$key->id}}"
                                                @if($loan_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
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
                                @if( !empty($branch_id))
                                    {{trans_choice('core::general.branch',1)}}:

                                    {{\Modules\Branch\Entities\Branch::find($branch_id)->name}}
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                {{trans_choice('user::general.staff',1)}}:

                                {{\Modules\User\Entities\User::find($loan_officer_id)->full_name}}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2">{{trans_choice('core::general.start_date',1)}}: {{$start_date}}</th>
                        </tr>
                        <tr>
                            <th colspan="2">{{trans_choice('core::general.end_date',1)}}: {{$end_date}}</th>
                        </tr>
                        <tr style="background-color: #D1F9FF">
                            <th colspan="">{{trans_choice('user::general.item',1)}}</th>
                            <th colspan="">{{trans_choice('user::general.value',1)}}</th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                {{__('user::general.Number of Clients')}}
                            </td>
                            <td>{{ number_format($data['number_of_clients']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{__('user::general.Number of Loans')}}
                            </td>
                            <td>{{ number_format($data['number_of_loans']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{__('user::general.Number of Savings')}}
                            </td>
                            <td>{{ number_format($data['number_of_savings']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{__('user::general.Disbursed Loans Amount')}}
                            </td>
                            <td>{{ number_format($data['disbursed_loans_amount']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                {{__('user::general.Total Payments Received')}}
                            </td>
                            <td>{{ number_format($data['total_payments_received']) }}</td>
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
