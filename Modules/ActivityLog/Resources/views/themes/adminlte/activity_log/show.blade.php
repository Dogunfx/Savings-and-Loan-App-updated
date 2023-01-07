@extends('core::layouts.master')
@section('title')
    {{ trans_choice('activitylog::general.activity_log',2) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('activitylog::general.activity_log',2) }} {{ trans_choice('core::general.detail',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('activitylog::general.activity_log',2) }} {{ trans_choice('core::general.detail',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card card-bordered card-preview">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>{{ trans_choice('user::general.user',1) }}</th>
                        <td>
                            @if(!empty($activity_log->causer))
                                {{$activity_log->causer->first_name}} {{$activity_log->causer->last_name}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans_choice('core::general.description',1) }}</th>
                        <td>
                            {{$activity_log->description}}
                        </td>
                    </tr>
                    @foreach($activity_log->properties as $key=>$value)
                        <tr>
                            <th>{{$key}}</th>
                            <td>
                                {{$value}}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th>{{ trans_choice('core::general.created_at',1) }}</th>
                        <td>
                            {{$activity_log->created_at}}
                        </td>
                    </tr>
                </table>

            </div>

        </div>
    </section>

@endsection
@section('scripts')

@endsection