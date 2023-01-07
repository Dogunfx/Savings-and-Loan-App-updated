@extends('core::layouts.master')
@section('title')
    {{ $asset->name }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"> {{ $asset->name }}</h3>
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

    <div class="row">
        <div class="col-md-4">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ $asset->name }}</h4>

                                </div>
                            </div>
                        </div>
                        <table class="table  table-hover">
                            <tbody>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ trans_choice('asset::general.depreciation',2) }}</h4>

                                </div>

                            </div>
                        </div>
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
    </div>
@endsection
@section('scripts')

@endsection
