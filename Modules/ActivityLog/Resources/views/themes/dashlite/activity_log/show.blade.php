@extends('core::layouts.master')
@section('title')
    {{ trans_choice('activitylog::general.activity_log',2) }} {{ trans_choice('core::general.detail',2) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"> {{ trans_choice('activitylog::general.activity_log',2) }} {{ trans_choice('core::general.detail',2) }}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <a href="#" onclick="window.history.back()"
                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                    </a>

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
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
    </div>

@endsection
@section('scripts')

@endsection