@extends('core::layouts.master')
@section('title')
    {{ $asset->name }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('asset::general.asset',1) }}
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
                                    href="{{url('asset')}}">{{ trans_choice('asset::general.asset',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $asset->name }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $asset->name }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive table-hover">
                            <tr>
                                <td>{{ trans_choice('core::general.branch',1) }}</td>
                                <td>
                                    @if(!empty($asset->branch))
                                        {{$asset->branch->name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('core::general.type',1) }}</td>
                                <td>
                                    @if(!empty($asset->type))
                                        {{$asset->type->name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('asset::general.purchase',1) }} {{ trans('core::general.date') }}</td>
                                <td>{{ $asset->purchase_date }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('asset::general.cost',1) }}</td>
                                <td>{{ number_format($asset->purchase_price,2) }}</td>
                            </tr>

                            <tr>
                                <td>{{ trans_choice('asset::general.life_span',1) }}</td>
                                <td>{{ $asset->life_span }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('core::general.updated_at') }}</td>
                                <td>{{ $asset->updated_at }}</td>
                            </tr>
                            @foreach($custom_fields as $custom_field)
                                <?php
                                $field = custom_field_build_form_field($custom_field, $asset->id);
                                ?>
                                <tr>
                                    <td>{{$field['label']}}</td>
                                    <td>
                                        @if($custom_field->type=='checkbox')
                                            @foreach(explode(',',$field['current'] ) as $key)
                                                {{$key}}<br>
                                            @endforeach
                                        @else
                                            {{$field['current'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>{{ trans_choice('general.note',2) }}</td>
                                <td>{!!   $asset->notes !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">{{ trans_choice('asset::general.depreciation',2) }}</h3>
                        <div class="box-tools pull-right">

                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table  table-bordered table-hover table-striped" id="">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('asset::general.year',1) }}</th>
                                <th>{{ trans_choice('asset::general.beginning_value',1) }}</th>
                                <th>{{ trans_choice('asset::general.depreciation',1) }}</th>
                                <th>{{ trans_choice('asset::general.ending_value',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($asset->depreciation as $key)
                                <tr>
                                    <td>{{ $key->year }}</td>
                                    <td>{{ number_format($key->beginning_value,2) }}</td>
                                    <td>{{ number_format($key->depreciation_value,2) }}</td>
                                    <td>{{ number_format($key->ending_value,2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection
