@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.manage',1) }} {{ trans_choice('core::general.module',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head-content">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.manage',1) }} {{ trans_choice('core::general.module',2) }}</h3>
                    <div class="nk-block-des text-soft">
                        <p>{{number_format(count($data))}} {{ trans_choice('core::general.module',2) }} {{ trans_choice('core::general.found',1) }}</p>
                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em
                                    class="icon ni ni-menu-alt-r"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li><a href="#" class="btn btn-white btn-outline-light"><em
                                                class="icon ni ni-download-cloud"></em><span>Export</span></a></li>
                                <li class="nk-block-tools-opt">
                                    <a href="{{ url('module/upload') }}" class="btn btn-info btn-sm">
                                        {{ trans_choice('core::general.upload',1) }} {{ trans_choice('core::general.module',1) }}
                                    </a>

                                </li>
                            </ul>
                        </div>
                    </div><!-- .toggle-wrap -->
                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block" id="app">
        <div class="card card-bordered card-stretch">
            <div class="card-inner-group">
                <div class="card-inner p-0">
                    <div class="nk-tb-list nk-tb-ulist is-compact">
                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col tb-col-mb">
                                <span class="sub-text">{{ trans_choice('core::general.name',1) }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <span class="sub-text">{{ trans_choice('core::general.description',1) }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-mb">
                                <span class="sub-text">{{ trans_choice('core::general.version',1) }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-md">

                                <span class="sub-text">{{ trans_choice('core::general.active',1) }}</span>
                            </div>
                        </div><!-- .nk-tb-item -->
                        @foreach($data as $key)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col  tb-col-mb">
                                    <a href="#">
                                        <span>{{$key->getName()}}</span>
                                    </a>
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    <span>{{$key->getDescription()}}</span>
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    <span>{{$key->get('version')}}</span>
                                </div>
                                <div class="nk-tb-col  tb-col-mb">
                                    @if($key->isEnabled())
                                        <span class="badge badge-success">{{trans_choice('core::general.yes',1)}}</span>
                                    @else
                                        <span class="badge badge-warning">{{trans_choice('core::general.no',1)}}</span>
                                    @endif
                                </div>
                                <div class="nk-tb-col tb-col-mb">
                                    @if($key->isEnabled())
                                        <a href="{{url('module/?action=disable&name='.$key->getName())}}"
                                           class="btn btn-danger confirm">{{trans_choice('core::general.disable',1)}}</a>
                                    @else
                                        <a href="{{url('module/?action=enable&name='.$key->getName())}}"
                                           class="btn btn-info confirm">{{trans_choice('core::general.enable',1)}}</a>
                                    @endif
                                    <a href="{{url('module/?action=reconfigure&name='.$key->getName())}}"
                                       class="btn btn-warning confirm">{{trans_choice('core::general.reconfigure',1)}}</a>


                                </div>
                            </div>
                        @endforeach
                    </div><!-- .nk-tb-list -->
                </div><!-- .card-inner -->

            </div><!-- .card-inner-group -->
        </div><!-- .card -->
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                records:{!!json_encode($data)!!},
                selectAll: false,
                selectedRecords: []
            },
            methods: {
                selectAllRecords() {
                    this.selectedRecords = [];
                    if (this.selectAll) {
                        this.records.data.forEach(item => {
                            this.selectedRecords.push(item.id);
                        });
                    }
                },
            },
        })
    </script>
@endsection
