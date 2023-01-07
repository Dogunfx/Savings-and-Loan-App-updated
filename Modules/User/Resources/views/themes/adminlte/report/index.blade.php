@extends('core::layouts.master')
@section('title')
    {{trans_choice('user::general.user',1)}} {{trans_choice('report::general.report',2)}}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{trans_choice('user::general.user',1)}} {{trans_choice('report::general.report',2)}}
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
                                    href="{{url('report')}}">{{ trans_choice('report::general.report',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active"> {{trans_choice('user::general.user',1)}} {{trans_choice('report::general.report',2)}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-body p-0">
                <table id="" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('core::general.name',1)}}</th>
                        <th>{{trans_choice('core::general.description',1)}}</th>
                        <th>{{trans_choice('core::general.action',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <a href="{{url('report/user/performance')}}">{{trans_choice('user::general.performance_report',1)}}</a>
                        </td>
                        <td>
                            {{trans_choice('user::general.user_performance_report_description',1)}}
                        </td>
                        <td><a href="{{url('report/user/performance')}}"><i
                                        class="fa fa-search"></i> </a>
                        </td>
                    </tr>

                    </tbody>
                </table>

            </div>


        </div>
    </section>
@endsection
@section('scripts')

@endsection
