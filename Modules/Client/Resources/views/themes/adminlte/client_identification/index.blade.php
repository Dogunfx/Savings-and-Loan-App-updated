@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.client',1) }}  {{ trans_choice('client::general.type',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('client::general.client',1) }}   {{ trans_choice('client::general.type',2) }}</h3>

            <div class="box-tools pull-right">

                <a href="{{ url('client_identification_type/create') }}" class="btn btn-info btn-sm">
                    {{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.type',1) }}
                </a>

            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-bordered table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.name',1) }}</th>
                    <th>{{ trans_choice('core::general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{$key->name}}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="true"><i class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a href="{{url('client_identification_type/' . $key->id . '/edit') }}"
                                           class="">{{ trans_choice('core::general.edit', 2) }}</a></li>
                                    <li><a href="{{url('client_identification_type/' . $key->id . '/destroy') }}"
                                           class="confirm">{{ trans_choice('core::general.delete', 2) }}</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
